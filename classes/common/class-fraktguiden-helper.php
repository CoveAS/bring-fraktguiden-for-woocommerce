<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Class Fraktguiden_Helper
 *
 * Shared between regular and pro version
 */
class Fraktguiden_Helper {

  // Be careful changing the ID!
  // Used for Shipping method ID's etc. for existing orders.
  const ID = 'bring_fraktguiden';

  const TEXT_DOMAIN = 'bring-fraktguiden';

  static function get_all_services() {
    $selected_service_name = Fraktguiden_Helper::get_option('service_name');
    $service_name = $selected_service_name ? $selected_service_name  : 'ProductName';
    $services = self::get_services_data();
    $result   = [ ];
    foreach ( $services as $key => $service ) {
      $result[$key] = $service[$service_name];
    }
    return $result;
  }

  static function get_all_selected_services() {
    $selected_service_name = Fraktguiden_Helper::get_option('service_name');
    $service_name = $selected_service_name ? $selected_service_name  : 'ProductName';

    $services = self::get_services_data();
    $selected = self::get_option( 'services' );
    $result   = [ ];
    foreach ( $services as $key => $service ) {
      if ( in_array( $key, $selected ) ) {
        $result[$key] = $service[$service_name];
      }
    }
    return $result;
  }

  static function get_service_data_for_key( $key_to_find ) {
    $result = [ ];

    $all_services = self::get_services_data();
    foreach ( $all_services as $key => $service ) {
      if ( $key == $key_to_find ) {
        $result = $service;
        break;
      }

    }
    return $result;
  }

  static function get_all_services_with_customer_types() {
    $services = self::get_services_data();
    $result   = [ ];
    foreach ( $services as $key => $service ) {
      $service['CustomerTypes'] = self::get_customer_types_for_service_id( $key );
      $result[$key]             = $service;
    }
    return $result;
  }

  static private function get_customer_types_for_service_id( $service_id ) {
    $customer_types = self::get_customer_types_data();
    $result         = [ ];
    foreach ( $customer_types as $k => $v ) {
      //$result[] = $key;
      foreach ( $v as $item ) {
        if ( $item == $service_id ) {
          $result[] = $k;
        }
      }
    }
    return $result;
  }

  static function get_services_data() {
    return [
        'SERVICEPAKKE'               => [
            'ProductCode'     => '1202',
            'ProductName'     => 'Klimanøytral Servicepakke',
            'DisplayName'     => 'På posten',
            'DescriptionText' => 'Hentes på mottakers lokale postkontor/post i butikk.',
            'HelpText'        => 'Sendingen er en Klimanøytral Servicepakke som blir levert til mottakers postkontor/ post i butikk. Mottaker kan velge å hente sendingen på et annet postkontor/post i butikk enn sitt lokale. Mottaker varsles om at sendingen er ankommet via SMS, e-post eller hentemelding i postkassen. Transporttid er normalt 1-3 virkedager, avhengig av strekning. Sendingen kan spores ved hjelp av sporingsnummeret.',
        ],
        'PA_DOREN'                   => [
            'ProductCode'     => '1736',
            'ProductName'     => 'På Døren',
            'DisplayName'     => 'Hjem på kvelden, 17-21',
            'DescriptionText' => 'Pakken leveres hjem til deg, sjåføren ringer 30 - 60 min. før ankomst',
            'HelpText'        => 'Sendingen leveres hjem til deg mellom klokken 17 og 21. Du varsles i god tid om forventet utleveringsdag på sms og/eller e-post, i tillegg til nytt varsel når sendingen er lastet på bil for utkjøring samme kveld. Sjåfør ringer deg på mobiltelefon 30 - 60 minutter før levering. Dersom sendingen ikke kan leveres, blir den fraktet til lokalt postkontor/ post i butikk og du vil motta en varsel om dette via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingsnummeret.',
        ],
        'BPAKKE_DOR-DOR'             => [
            'ProductCode'     => '1000',
            'ProductName'     => 'Bedriftspakke',
            'DisplayName'     => 'På jobben, 08-16',
            'DescriptionText' => 'Leveres uten at sjåføren ringer først',
            'HelpText'        => 'Sendingen er en Bedriftspakke som leveres til mottakers arbeidssted mellom klokken 08 og 16. Bestiller du varsling, vil mottaker varsles når sendingen er lastet på bil for uttkjøring, via SMS og/eller e-post. Dersom sendingen ikke kan leveres, blir den fraktet til lokalt postkontor/ post i butikk. Mottaker varsles om dette via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingssnummeret.',
        ],
        'EKSPRESS09'                 => [
            'ProductCode'     => '1002',
            'ProductName'     => 'Bedriftspakke Ekspress-Over natten',
            'DisplayName'     => 'Ekspress over natten',
            'DescriptionText' => 'Levering på dør vil skje påfølgende dag innen kl 1600 for dette postnummeret.',
            'HelpText'        => 'Levering hjem på dør før kl 0900 (til mindre steder normalt før kl 1600). Du kan varsles ved SMS/e-post, forutsatt at du har oppgitt telefonnummer/epostadresse ved bestilling. Sendingen kan spores ved hjelp av sporingsnummeret.',
        ],
        'MINIPAKKE'                  => [
            'ProductCode'     => '3110',
            'ProductName'     => 'Minipakke',
            'DisplayName'     => 'I postkassen (sporbar)',
            'DescriptionText' => 'Leveres innen 1-5 dager',
            'HelpText'        => 'Pakken leveres i mottakers postkasse, og er egnet for små og forholdsvis lette sendinger (maksimalt 2 kg). Mottaker varsles om at pakken ankommer, via SMS og/eller e-post. For at pakken skal kunne leveres i postkassen, må de innvendige målene på postkassen være minst 31 x 21 x 6 cm. Postkassen kan ikke være rør-/sylinderformet, og lokk må være ulåst ved levering. Dersom postkassen er låst eller full slik at pakken ikke kan leveres, vil den bli sendt til mottakers postkontontor/ post i butikk (hentefrist 14 dager). Mottaker får beskjed om dette via SMS og/eller e-post. Transporttid er normalt 1-3 virkedager, avhengig av strekning. Pakken kan spores ved hjelp av sporingsnummeret.',
        ],
        'A-POST'                     => [
            'ProductCode'     => 'N/A',
            'ProductName'     => 'A-Prioritert',
            'DisplayName'     => 'I postkassen (A-Prioritert)',
            'DescriptionText' => 'Sendingene får plass i en vanlig postkasse',
            'HelpText'        => 'Brev leveres direkte i mottakerens postkasse. Hvis brevet er for stort, må det hentes på mottakers lokale postkontor/ post i butikk innen 2 uker. Mottaker får beskjed om dette via hentemelding i postkassen. Sendingen blir sendt som A-Prioritert, og normal transporttid er 1 virkedag. Brev kan ikke spores.',
        ],
        'B-POST'                     => [
            'ProductCode'     => 'N/A',
            'ProductName'     => 'B-Økonomi',
            'DisplayName'     => 'I postkassen (B-Økonomi)',
            'DescriptionText' => 'Sendingene får plass i en vanlig postkasse',
            'HelpText'        => 'Brev leveres direkte i mottakerens postkasse. Hvis brevet er for stort, må det hentes på mottakers lokale postkontor/ post i butikk innen 2 uker. Mottaker får beskjed om dette via hentemelding i postkassen. Sendingen blir sendt som B-Økonomi, og normal transporttid er 3-5 virkedager. Brev kan ikke spores.',
        ],
        'SMAAPAKKER_A-POST'          => [
            'ProductCode'     => 'N/A',
            'ProductName'     => 'Småpakker A-Post',
            'DisplayName'     => 'I postkassen (A: haster)',
            'DescriptionText' => 'Leveres innen 1-2 dager, ikke sporbar',
            'HelpText'        => 'Pakken leveres direkte i mottakerens postkasse. Hvis pakken er for stor, må den hentes på mottakers lokale postkontor/ post i butikk innen 2 uker. Mottaker får beskjed om dette via hentemelding i postkassen. Pakken blir sendt som A-Prioritert, og normal transporttid er 1 virkedag. Pakken kan ikke spores.',
        ],
        'SMAAPAKKER_B-POST'          => [
            'ProductCode'     => 'N/A',
            'ProductName'     => 'Småpakker B-Post',
            'DisplayName'     => 'I postkassen (B-Økonomi)',
            'DescriptionText' => 'Leveres innen 3-5 dager, ikke sporbar',
            'HelpText'        => 'Pakken leveres direkte i mottakerens postkasse. Hvis pakken er for stor, må den hentes på mottakers lokale postkontor/ post i butikk innen 2 uker. Mottaker får beskjed om dette via hentemelding i postkassen. Pakken blir sendt som B-Økonomi, og normal transporttid er 3 virkedager. Pakken kan ikke spores.',
        ],
        'EXPRESS_NORDIC_SAME_DAY'    => [
            'ProductCode'     => '3336',
            'ProductName'     => 'Quickpack SameDay',
            'DisplayName'     => 'Omgående levering, dagtid og kveldstid',
            'DescriptionText' => 'Bud henter og leverer omgående til dør.',
            'HelpText'        => 'Når du trenger raskest mulig budlevering. Bring henter pakken omgående hos avsender og flyr den med første fly til mottaker. Innenfor Norden inkluderer produktet også forsikring av forsendelsen som dekker inntil NOK 100 000,- per sending.',
        ],
        'EXPRESS_INTERNATIONAL_0900' => [
            'ProductCode'     => '3337',
            'ProductName'     => 'Quickpack Over Night 0900',
            'DisplayName'     => 'Levering neste dag innen kl. 9',
            'DescriptionText' => 'Bud henter og leverer til dør.',
            'HelpText'        => 'Når du trenger raskest mulig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
        ],
        'EXPRESS_INTERNATIONAL_1200' => [
            'ProductCode'     => '3338',
            'ProductName'     => 'Quickpack Over Night 1200',
            'DisplayName'     => 'Levering neste dag innen kl. 12',
            'DescriptionText' => 'Bud henter og leverer til dør.',
            'HelpText'        => 'Når du trenger rask budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
        ],
        'EXPRESS_INTERNATIONAL'      => [
            'ProductCode'     => '3339',
            'ProductName'     => 'Quickpack Day Certain',
            'DisplayName'     => 'Levering neste dag',
            'DescriptionText' => 'Bud henter og leverer til dør.',
            'HelpText'        => 'Når du trenger en rask og rimelig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
        ],
        'EXPRESS_ECONOMY'            => [
            'ProductCode'     => '3340',
            'ProductName'     => 'Quickpack Express Economy',
            'DisplayName'     => 'Levering tidligst neste dag kl. 17',
            'DescriptionText' => 'Bud henter og leverer til dør.',
            'HelpText'        => 'Når du trenger rimelig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
        ],
        'CARGO_GROUPAGE'             => [
            'ProductCode'     => '3050',
            'ProductName'     => 'Cargo',
            'DisplayName'     => 'Cargo',
            'DescriptionText' => 'N/A',
            'HelpText'        => 'N/A',
        ],
        'BUSINESS_PARCEL'            => [
            'ProductCode'     => '0330',
            'ProductName'     => 'CarryOn Business',
            'DisplayName'     => 'Til mottakers dør',
            'DescriptionText' => 'Pakke til bedrifter i utlandet',
            'HelpText'        => 'CarryOn Business er en enkelt og effektiv måte å sende pakker til andre firmaer i Norden og til resten av verden. Pakker hentes i henhold til avtale, og leveres til mottaker mellom mandag- fredag i kontortiden. I enkelte land leveres pakkene på mottakers postkontor.',
        ],
        'PICKUP_PARCEL'              => [
            'ProductCode'     => '0340',
            'ProductName'     => 'CarryOn HomeShopping',
            'DisplayName'     => 'Till utlämningsställe',
            'DescriptionText' => 'Hentes på mottakers lokale utleveringssted i butikk.',
            'HelpText'        => 'Sendingen blir levert til mottakers nærmeste utleveringssted. Mottaker kan velge å hente sendingen på et annet postkontor/post i butikk enn sitt lokale. Mottaker varsles om at sendingen er ankommet via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingsnummeret.',
        ],
        'COURIER_VIP'                => [
            'ProductCode'     => 'VIP25',
            'ProductName'     => 'Bud VIP',
            'DisplayName'     => 'Omgående levering',
            'DescriptionText' => 'Leveres omgående av bud til dør.',
            'HelpText'        => 'Sending hentes hos avsender innen 10 minutter og leveres direkte til mottaker.',
        ],
        'COURIER_1H'                 => [
            'ProductCode'     => '1H25',
            'ProductName'     => 'Bud 1 time',
            'DisplayName'     => 'Levering innen 1 time',
            'DescriptionText' => 'Leveres innen 1 time av bud til dør.',
            'HelpText'        => 'Sending hentes hos avsender og leveres til mottaker innen 1 time.',
        ],
        'COURIER_2H'                 => [
            'ProductCode'     => '2H25',
            'ProductName'     => 'Bud 2 timer',
            'DisplayName'     => 'Levering innen 2 timer',
            'DescriptionText' => 'Leveres av bud til dør innen 2 timer.',
            'HelpText'        => 'Sending hentes hos avsender og leveres til mottaker innen 2 timer.',
        ],
        'COURIER_4H'                 => [
            'ProductCode'     => '4H25',
            'ProductName'     => 'Bud 4 timer',
            'DisplayName'     => 'Levering innen 4 timer',
            'DescriptionText' => 'Leveres av bud til dør innen 4 timer.',
            'HelpText'        => 'Sending hentes hos avsender og leveres til mottaker innen 4 timer.',
        ],
        'COURIER_6H'                 => [
            'ProductCode'     => '6H25',
            'ProductName'     => 'Bud 6 timer',
            'DisplayName'     => 'Levering innen 6 timer',
            'DescriptionText' => 'Leveres av bud til dør innen 6 timer.',
            'HelpText'        => 'Sending hentes hos avsender og leveres til mottaker innen 6 timer.',
        ],
        'OIL_EXPRESS'                => [
            'ProductCode'     => '3050',
            'ProductName'     => 'OIL_EXPRESS',
            'DisplayName'     => 'Oil Express',
            'DescriptionText' => 'N/A',
            'HelpText'        => 'N/A',
        ]
    ];
  }

  static function get_customer_types_data() {
    return [
        'Bring Parcels, Norway'     => [
            'BPAKKE_DOR-DOR',
            'BPAKKE_DOR-DOR_RETURSERVICE',
            'BUSINESS_PALLET',
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'BUSINESS_PARCEL_HALFPALLET',
            'BUSINESS_PARCEL_QUARTERPALLET',
            'EKSPRESS09',
            'EKSPRESS09_RETURSERVICE',
            'EXPRESS_NORDIC_0900_BULK',
            'HOME_DELIVERY_MAILBOX',
            'HOME_DELIVERY_PARCEL',
            'MINIPAKKE',
            'PA_DOREN',
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
            'SERVICEPAKKE',
            'SERVICEPAKKE_RETURSERVICE',
        ],
        'Bring Parcels, Denmark'    => [
            'BUSINESS_PALLET',
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'BUSINESS_PARCEL_HALFPALLET',
            'BUSINESS_PARCEL_QUARTERPALLET',
            'EXPRESS_NORDIC_0900_BULK',
            'HOME_DELIVERY_MAILBOX',
            'HOME_DELIVERY_PARCEL',
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
        ],
        'Bring Parcels, Sweden'     => [
            'BUSINESS_PALLET',
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'BUSINESS_PARCEL_HALFPALLET',
            'BUSINESS_PARCEL_QUARTERPALLET',
            'EXPRESS_NORDIC_0900_BULK',
            'HOME_DELIVERY_MAILBOX',
            'HOME_DELIVERY_PARCEL',
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
        ],
        'Bring Parcels, Finland'    => [
            'BUSINESS_PALLET',
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'BUSINESS_PARCEL_HALFPALLET',
            'BUSINESS_PARCEL_QUARTERPALLET',
            'EXPRESS_NORDIC_0900_BULK',
            'HOME_DELIVERY_MAILBOX',
            'HOME_DELIVERY_PARCEL',
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
        ],
        'Bring Cargo, Norway'       => [
            'CARGO',
            'CARGO_GROUPAGE',
        ],
        'Bring Express, Norway'     => [
            'COURIER_1H',
            'COURIER_2H',
            'COURIER_4H',
            'COURIER_BICYCLE_1H',
            'COURIER_BICYCLE_2H',
            'COURIER_BICYCLE_4H',
            'COURIER_BICYCLE_VIP',
            'COURIER_LONG_DISTANCE',
            'COURIER_VIP',
            'EXPRESS_ECONOMY',
            'EXPRESS_INTERNATIONAL',
            'EXPRESS_INTERNATIONAL_0900',
            'EXPRESS_INTERNATIONAL_1200',
            'EXPRESS_NORDIC_SAME_DAY',
        ],
        'Bring Express, Denmark'    => [
            'COURIER_1H',
            'COURIER_2H',
            'COURIER_4H',
            'COURIER_6H',
            'COURIER_BICYCLE_1H',
            'COURIER_BICYCLE_2H',
            'COURIER_BICYCLE_4H',
            'COURIER_BICYCLE_VIP',
            'COURIER_LONG_DISTANCE',
            'COURIER_VIP',
            'EXPRESS_ECONOMY',
            'EXPRESS_INTERNATIONAL',
            'EXPRESS_INTERNATIONAL_0900',
            'EXPRESS_INTERNATIONAL_1200',
            'EXPRESS_NORDIC_SAME_DAY',
        ],
        'Bring Express, Sweden'     => [
            'COURIER_1H',
            'COURIER_2H',
            'COURIER_4H',
            'COURIER_6H',
            'COURIER_BICYCLE_1H',
            'COURIER_BICYCLE_2H',
            'COURIER_BICYCLE_4H',
            'COURIER_BICYCLE_VIP',
            'COURIER_LONG_DISTANCE',
            'COURIER_VIP',
            'EXPRESS_ECONOMY',
            'EXPRESS_INTERNATIONAL',
            'EXPRESS_INTERNATIONAL_0900',
            'EXPRESS_INTERNATIONAL_1200',
            'EXPRESS_NORDIC_SAME_DAY',
        ],
        'Bring Oil Express, Norway' => [
            'OIL_EXPRESS'
        ]
    ];
  }

  /**
   * Gets a Woo admin setting by key
   * Returns false if key is not found.
   *
   * @todo: There must be an API in woo for this. Investigate.
   *
   * @param string $key
   * @return string|bool
   */
  static function get_option( $key ) {
    $options = get_option( 'woocommerce_' . WC_Shipping_Method_Bring::ID . '_settings' );
    if (empty($options)) {
      return false;
    }

    return array_key_exists( $key, $options ) ? $options[$key] : false;
  }

  /**
   * Returns an array based on the filter in the callback function.
   * Same as PHP's array_filter but uses the key instead of value.
   *
   * @param array $array
   * @param callable $callback
   * @return array
   */
  static function array_filter_key( $array, $callback ) {
    $matched_keys = array_filter( array_keys( $array ), $callback );
    return array_intersect_key( $array, array_flip( $matched_keys ) );
  }

  /**
   * Returns an array with nordic country codes
   *
   * @return array
   */
  static function get_nordic_countries() {
    global $woocommerce;
    $countries = array( 'NO', 'SE', 'DK', 'FI', 'IS' );
    return Fraktguiden_Helper::array_filter_key( $woocommerce->countries->countries, function ( $k ) use ( $countries ) {
      return in_array( $k, $countries );
    } );
  }

  static function parse_shipping_method_id( $method_id ) {
    $parts = explode( ':', $method_id );
    //@todo: rename service > service_key
    return [
        'name'    => $parts[0],
        'service' => count( $parts ) == 2 ? strtoupper( $parts[1] ) : '',
    ];
  }
}