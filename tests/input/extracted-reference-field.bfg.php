<bfg-field.text
						id="booking_address_reference"
						label="Reference"
						description="The store's reference printed on the shipping label. Usually {order_id}, but can also be {products}.">
						<input
							type="text"
							id="booking_address_reference"
							name="booking_address_reference"
							placeholder="<?php echo esc_attr__('e.g. {order_id}', 'bring-fraktguiden-for-woocommerce'); ?>"
							maxlength="35"
							required
						/>
					</bfg-field.text>