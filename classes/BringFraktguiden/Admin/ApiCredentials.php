<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Admin_Notices;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;

class ApiCredentials
{
	public function process_mybring_api_credentials() {
		$api_uid_key         = $this->get_field_key( 'mybring_api_uid' );
		$api_key_key         = $this->get_field_key( 'mybring_api_key' );
		$customer_number_key = $this->get_field_key( 'mybring_customer_number' );

		$api_uid         = filter_input( INPUT_POST, $api_uid_key );
		$api_key         = filter_input( INPUT_POST, $api_key_key );
		$customer_number = filter_input( INPUT_POST, $customer_number_key );

		$mybring_authentication = [
			'message'       => '',
			'authenticated' => false,
		];

		$is_credential_missing = false;

		if ( ! $api_uid || ! $api_key ) {
			$is_credential_missing = true;
		} else {
			Fraktguiden_Admin_Notices::remove_missing_api_credentials_notice();
		}

		if ( ! $customer_number && Fraktguiden_Helper::pro_activated() && Fraktguiden_Helper::booking_enabled() ) {
			Fraktguiden_Admin_Notices::add_missing_api_customer_number_notice();
			$is_credential_missing = true;
		} else {
			Fraktguiden_Admin_Notices::remove_missing_api_customer_number_notice();
		}

		if ( $is_credential_missing ) {
			$mybring_authentication['message'] = __( 'Missing credentials', 'bring-fraktguiden-for-woocommerce' );
			update_option( 'mybring_authentication', $mybring_authentication );
			update_option( 'mybring_authenticated_key', '', true );

			return;
		}

		$key  = get_option( 'mybring_authenticated_key' );
		$hash = md5( $api_uid . $api_key . $customer_number );

		if ( $key === $hash ) {
			// We already tried this combination, skip this for re-saves.
			return;
		}

		// Try to authenticate.
		$request  = new \WP_Bring_Request();
		$params   = $this->make_shipping_guide_request_body(
			null,
			[ Fraktguiden_Service::find( self::$field_key, 'SERVICEPAKKE' ) ],
			[ [ 'weight_in_grams' => 1000 ] ]
		);
		$options  = [
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			],
			'body'    => json_encode( $params ),
		];
		$response = $request->post( self::SERVICE_URL, [], $options );

		if ( 200 !== $response->status_code ) {
			$this->mybring_error( $response->body );
			$mybring_authentication['message'] = 'Mybring error: ' . $response->body;
			update_option( 'mybring_authentication', $mybring_authentication );
			update_option( 'mybring_authenticated_key', '', true );

			return;
		}

		$result = json_decode( $response->body, true );

		/*
		Check for customer_number authentication error
		May the programming gods have mercy. Bring does not have a authentication endpoint
		and authentication credentials has to be passed on every request. The shipping API is
		simply the easiest api to test against, but only certain products actually require
		auth. I've picked "Servicepakke" because it seems to be the most reliable (hasn't
		changed the last year). Now I wouldn't normally rant like this, I mean it would be
		fine if the API just threw a 400 error if you half authenticate, but NO, it just
		silently fails and doesn't give the rates. UGH! Here's a hacky workaround. I'm
		reading the TraceMessage for all the results to see if the customer_number was
		authenticated.
		*/
		if ( isset( $result['traceMessages'] ) ) {
			foreach ( $result['traceMessages'] as $messages ) {
				if ( ! is_array( $messages ) ) {
					$messages = [ $messages ];
				}

				foreach ( $messages as $message ) {
					if ( false === strpos( $message, 'does not have access to customer' ) ) {
						continue;
					}

					$this->mybring_error( $message );
//					$this->validation_messages = sprintf( '<p class="error-message">%s</p>', $message );
					update_option( 'mybring_authentication', $mybring_authentication );
					update_option( 'mybring_authenticated_key', '', true );

					return;
				}
			}
		}

		$mybring_authentication['message']       = __( 'Successfully authenticated',
			'bring-fraktguiden-for-woocommerce' );
		$mybring_authentication['authenticated'] = true;

		// Success. All authentication methods have passed.
		update_option( 'mybring_authentication', $mybring_authentication );
		update_option( 'mybring_authenticated_key', $hash, true );
	}
	/**
	 * Add Mybring error
	 *
	 * @param string $message Error message.
	 *
	 * @return void
	 */
	public function mybring_error( $message ) {
		if ( strpos( $message, 'Authentication failed.' ) === 0 ) {
			$message = sprintf( '<strong>%s:</strong> %s.',
				__( 'Mybring authentication failed', 'bring-fraktguiden-for-woocommerce' ),
				__( "Couldn't connect to Bring with your API credentials. Please check that they are correct",
					'bring-fraktguiden-for-woocommerce' ) );
		}

		Fraktguiden_Admin_Notices::add_notice( 'mybring_error', $message, 'error', false );
	}

}
