<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

return [
	'common'       => [
		'title'    => __( 'New services', 'bring-fraktguiden-for-woocommerce' ),
		'expanded' => true,
		'services' => [
			'5800' => [
				'ProductCode' => '5800',
				'productName' => 'Pakke til hentested',
				'description' => 'Pakken kan spores og utleveres på ditt lokale hentested.',
				'helptext'    => 'Sendingen er en Klimanøytral Servicepakke som blir levert til mottakers postkontor/ post i butikk. Mottaker kan velge å hente sendingen på et annet postkontor/post i butikk enn sitt lokale. Mottaker varsles om at sendingen er ankommet via SMS, e-post eller hentemelding i postkassen. Transporttid er normalt 1-3 virkedager, avhengig av strekning. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => true,
				'oldcode'     => 'SERVICEPAKKE',
			],
			'5600' => [
				'ProductCode' => '5600',
				'productName' => 'Pakke levert hjem',
				'description' => 'Pakken kan spores og leveres hjem til deg mellom kl. 08-17 eller 17-21 avhengig av ditt postnummer. Sjåføren ringer 30-60 min. før ankomst ved levering på kveldstid.',
				'helptext'    => 'Pakke levert hjem leveres til mottaker mellom kl. 08-17 eller 17-21 avhengig av mottakers postnummer. Mottaker varsles i god tid om forventet utleveringsdag via SMS eller e-post, i tillegg til nytt varsel når sendingen er lastet på bil for utkjøring samme dag. Mottaker kan gi Posten fullmakt til at pakken settes igjen ved døren eller et angitt sted hvis mottaker ikke er hjemme. Sjåføren ringer mottaker 30-60 minutter før ankomst ved levering på kveldstid. Mottaker kan endre leveringsdag når pakken spores (gjelder ikke lokalpakker). Dersom sendingen ikke kan leveres, blir den sendt til mottakers lokale hentested (postkontor eller Post i Butikk). Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
				'oldcode'     => 'PA_DOREN',
			],
			'5000' => [
				'ProductCode' => '5000',
				'productName' => 'Pakke til bedrift',
				'description' => 'Pakken kan spores og utleveres på døren mellom kl. 8-16.',
				'helptext'    => 'Pakke til bedrift leveres på døren til bedrift mellom kl. 8 og 16. Dersom sendingen ikke kan leveres ved første forsøk, gjøres et nytt utleveringsforsøk neste virkedag. Dersom sendingen ikke kan leveres, blir den sendt til mottakers lokale hentested (postkontor eller Post i Butikk). Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
				'oldcode'     => 'BPAKKE_DOR-DOR',
			],
			'4850' => [
				'ProductCode' => '4850',
				'productName' => 'Ekspress neste dag',
				'description' => 'Pakken kan spores og utleveres neste virkedag på de fleste strekninger. Utlevering skjer på døren innen kl. 09:00, 11:30 eller 16:00. Enkelte strekninger kan ta mer enn én dag.',
				'helptext'    => 'Pakken sendes på de fleste strekninger slik at den utleveres neste virkedag. Utlevering skjer på døren innen kl. 09:00, 11:30 eller 16:00. Enkelte strekninger kan ta mer enn én dag. Dersom sendingen ikke kan leveres, blir den sendt til mottakers hentested (postkontor eller Post i Butikk). Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
				'oldcode'     => 'EKSPRESS09',
			],
			'5100' => [
				'ProductCode' => '5100',
				'productName' => 'Stykkgods til bedrift',
				'description' => 'Godset kan spores og leveres på døren mellom kl. 08-16.',
				'helptext'    => 'Godset leveres på døren til bedrift mellom kl. 08-16. Dersom sendingen ikke kan leveres ved første forsøk, kontaktes mottaker for å avtale ny utkjøring. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
				'oldcode'     => 'CARGO_GROUPAGE',
			],
			'5300' => [
				'ProductCode' => '5300',
				'productName' => 'Partigods til bedrift',
				'description' => 'Godset fraktes direkte fra avsender til mottaker og leveres på døren mellom kl. 08-16',
				'helptext'    => 'Godset fraktes direkte fra avsender til mottaker og leveres på døren mellom kl. 08-16. Dersom sendingen ikke kan leveres ved første forsøk, kontaktes mottaker for å avtale ny utkjøring.',
				'deprecated'  => false,
				'pickuppoint' => false,
				'oldcode'     => 'CARGO',
			],
			'5400' => [
				'ProductCode'    => '5400',
				'productName'    => 'Pall til bedrift',
				'description'    => 'Godset kan spores og leveres på døren mellom kl. 08-16.',
				'helpText'       => 'Godset leveres på døren til bedrift mellom kl. 08-16. Dersom sendingen ikke kan leveres ved første forsøk, kontaktes mottaker for å avtale ny utkjøring. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'maxWeightInKgs' => '9600',
				'deprecated'     => false,
				'pickuppoint'    => false,
				'oldcode'        => '',
			],
		],
	],
	'mailbox_new'  => [
		'title'    => __( 'Mailbox', 'bring-fraktguiden-for-woocommerce' ),
		'expanded' => true,
		'services' => [
			'3570' => [
				'ProductCode' => '3584',
				'class'       => 'warning',
				'productName' => 'Pakke i postkassen (sporbar)',
				'helptext'    => 'Pakke i postkassen leveres i mottakers postkasse, og er egnet for små og lette sendinger (maksimalt 5 kg). Dersom postkassen er låst eller full, blir pakken sendt til mottakers lokale hentested (postkontontor eller Post i Butikk).',
				'deprecated'  => false,
				'pickuppoint' => false,
				'ProductLink' => 'https://www.bring.no/sende/pakker/private-i-norge/pakke-i-postkassen',
				'description' => __( 'Packages up to 5 kg.', 'bring-fraktguiden-for-woocommerce' ) . PHP_EOL,
				'warning'     => __( 'Please note in order to use mailbox with tracking you need a RFID printer.',
					'bring-fraktguiden-for-woocommerce' ),
				'oldcode'     => 'PAKKE_I_POSTKASSEN_SPORBAR',
			],
			'3584' => [
				'ProductCode' => '3570',
				'productName' => 'Pakke i postkassen',
				'helptext'    => 'Pakke i postkassen leveres i mottakers postkasse, og er egnet for små og lette sendinger (maksimalt 5 kg). Dersom postkassen er låst eller full, blir pakken sendt til mottakers lokale hentested (postkontontor eller Post i Butikk).',
				'deprecated'  => false,
				'pickuppoint' => false,
				'ProductLink' => 'https://www.bring.no/sende/pakker/private-i-norge/pakke-i-postkassen',
				'description' => __( 'Packages up to 5 kg.', 'bring-fraktguiden-for-woocommerce' ),
				'oldcode'     => 'PAKKE_I_POSTKASSEN',
			],
		],
	],
	'old'          => [
		'title'    => __( 'Services', 'bring-fraktguiden-for-woocommerce' ),
		'expanded' => true,
		'services' => [
			'SERVICEPAKKE'   => [
				'ProductCode' => '1202',
				'productName' => 'Klimanøytral Servicepakke',
				'helptext'    => 'Sendingen er en Klimanøytral Servicepakke som blir levert til mottakers postkontor/ post i butikk. Mottaker kan velge å hente sendingen på et annet postkontor/post i butikk enn sitt lokale. Mottaker varsles om at sendingen er ankommet via SMS, e-post eller hentemelding i postkassen. Transporttid er normalt 1-3 virkedager, avhengig av strekning. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => true,
			],
			'PA_DOREN'       => [
				'ProductCode' => '1736',
				'productName' => 'På Døren',
				'helptext'    => 'Sendingen leveres hjem til deg mellom klokken 17 og 21. Du varsles i god tid om forventet utleveringsdag på sms og/eller e-post, i tillegg til nytt varsel når sendingen er lastet på bil for utkjøring samme kveld. Sjåfør ringer deg på mobiltelefon 30 - 60 minutter før levering. Dersom sendingen ikke kan leveres, blir den fraktet til lokalt postkontor/ post i butikk og du vil motta en varsel om dette via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
			],
			'MAIL'           => [
				'ProductCode' => 'MAIL',
				'productName' => 'Brev',
				'helptext'    => '',
				'deprecated'  => false,
				'warning'     => __( 'Please note that Mail does not return with prices from the api and you need to set a fixed price override to use this service' ),
				'pickuppoint' => false,
			],
			'CARGO_GROUPAGE' => [
				'ProductCode' => '3050',
				'productName' => 'Cargo',
				'helptext'    => '',
				'deprecated'  => false,
				'pickuppoint' => false,
			],
		],
	],
	'business'     => [
		'title'    => __( 'Other services', 'bring-fraktguiden-for-woocommerce' ),
		'expanded' => false,
		'services' => [
			/**
			 * Business parcel
			 * Fuel surcharge is not included in the price returned from the ShippingGuide and must be calculated / added manually. Bring Parcels is entitled to change this charge without notice to the customer. Any fuel surcharge applied will be detailed on the invoice.
			 */
			'BUSINESS_PARCEL'     => [
				'ProductCode' => '0330',
				'class'       => 'warning',
				'productName' => 'Business Parcel',
				'helptext'    => 'Business Parcel leveres på døren til bedrift mellom kl. 8 og 16. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'description' => 'Pakken kan spores og utleveres på døren mellom kl. 8-16.',
				'warning'     => __( 'Fuel surcharge is not included in the price returned from the ShippingGuide and must be calculated / added manually. Bring Parcels is entitled to change this charge without notice to the customer. Any fuel surcharge applied will be detailed on the invoice.',
					'bring-fraktguiden-for-woocommerce' ),
				'deprecated'  => false,
				'pickuppoint' => false,
			],
			/**
			 * Business pallets
			 */
			'BUSINESS_PALLET'     => [
				'ProductCode' => '0336',
				'class'       => 'warning',
				'productName' => 'Business Pallet',
				'helptext'    => 'Business Pallet leveres på døren til bedrift mellom kl. 8 og 16. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'description' => 'Pallen kan spores og utleveres på døren mellom kl. 8-16.',
				'warning'     => __( 'Fuel surcharge is not included in the price returned from the ShippingGuide and must be calculated / added manually. Bring Parcels is entitled to change this charge without notice to the customer. Any fuel surcharge applied will be detailed on the invoice.',
					'bring-fraktguiden-for-woocommerce' ),
				'deprecated'  => false,
				'pickuppoint' => false,
			],
			'BUSINESS_HALFPALLET' => [
				'ProductCode' => '0336',
				'class'       => 'warning',
				'productName' => 'Business Pallet (1/2)',
				'helptext'    => 'Business Pallet (1/2) leveres på døren til bedrift mellom kl. 8 og 16. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'description' => 'Pallen kan spores og utleveres på døren mellom kl. 8-16.',
				'warning'     => __( 'Fuel surcharge is not included in the price returned from the ShippingGuide and must be calculated / added manually. Bring Parcels is entitled to change this charge without notice to the customer. Any fuel surcharge applied will be detailed on the invoice.',
					'bring-fraktguiden-for-woocommerce' ),
				'deprecated'  => false,
				'pickuppoint' => false,
			],
		],
	],
	'other'        => [
		'title'    => __( 'Other services', 'bring-fraktguiden-for-woocommerce' ),
		'expanded' => false,
		'services' => [
			'BPAKKE_DOR-DOR'             => [
				'ProductCode' => '1000',
				'productName' => 'Bedriftspakke',
				'helptext'    => 'Sendingen er en Bedriftspakke som leveres til mottakers arbeidssted mellom klokken 08 og 16. Bestiller du varsling, vil mottaker varsles når sendingen er lastet på bil for uttkjøring, via SMS og/eller e-post. Dersom sendingen ikke kan leveres, blir den fraktet til lokalt postkontor/ post i butikk. Mottaker varsles om dette via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingssnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
			],
			'EKSPRESS09'                 => [
				'ProductCode' => '1002',
				'productName' => 'Bedriftspakke Ekspress-Over natten',
				'helptext'    => 'Levering hjem på dør før kl 0900 (til mindre steder normalt før kl 1600). Du kan varsles ved SMS/e-post, forutsatt at du har oppgitt telefonnummer/epostadresse ved bestilling. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
			],
			'EXPRESS_NORDIC_SAME_DAY'    => [
				'ProductCode' => '3336',
				'productName' => 'Quickpack SameDay',
				'helptext'    => 'Når du trenger raskest mulig budlevering. Bring henter pakken omgående hos avsender og flyr den med første fly til mottaker. Innenfor Norden inkluderer produktet også forsikring av forsendelsen som dekker inntil NOK 100 000,- per sending.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'EXPRESS_INTERNATIONAL_0900' => [
				'ProductCode' => '3337',
				'productName' => 'Quickpack Over Night 0900',
				'helptext'    => 'Når du trenger raskest mulig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'EXPRESS_INTERNATIONAL_1200' => [
				'ProductCode' => '3338',
				'productName' => 'Quickpack Over Night 1200',
				'helptext'    => 'Når du trenger rask budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'EXPRESS_INTERNATIONAL'      => [
				'ProductCode' => '3339',
				'productName' => 'Quickpack Day Certain',
				'helptext'    => 'Når du trenger en rask og rimelig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'EXPRESS_ECONOMY'            => [
				'ProductCode' => '3340',
				'productName' => 'Quickpack Express Economy',
				'helptext'    => 'Når du trenger rimelig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],

			/**
			 * PickUp Parcel
			 * Fuel surcharge is not included in the price returned from the ShippingGuide and must be calculated / added manually. Bring Parcels is entitled to change this charge without notice to the customer. Any fuel surcharge applied will be detailed on the invoice.
			 * For shipments to Denmark, PICKUP_PARCEL needs to be ordered with the “PICKUP_POINT” additional service to send as a low-cost delivery from Bring’s parcel shops or parcel lockers. Otherwise the shipment will be sent as a more expensive home delivery.
			 */
			'PICKUP_PARCEL'              => [
				'ProductCode' => '0340',
				'productName' => 'PickUp Parcel',
				'description' => 'Pakken kan spores og utleveres på ditt lokale hentested.',
				'helptext'    => 'PickUp Parcel leveres til mottakers lokale hentested. Mottaker kan velge å hente sendingen på et annet hentested enn sitt lokale. Mottaker varsles om at sendingen er ankommet via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
			],
			'HOME_DELIVERY_PARCEL'       => [
				'ProductCode' => '0349',
				'productName' => 'Home Delivery Parcel',
				'helptext'    => 'Parcels will be delivered between 8 a.m. - 5 p.m. or 5 p.m. - 9 p.m., based on the geographical location in the country.',
				'deprecated'  => false,
				'pickuppoint' => false,
			],

			/**
			 * PickUp Parcel
			 * Fuel surcharge is not included in the price returned from the ShippingGuide and must be calculated / added manually. Bring Parcels is entitled to change this charge without notice to the customer. Any fuel surcharge applied will be detailed on the invoice.
			 * For shipments to Denmark, PICKUP_PARCEL needs to be ordered with the “PICKUP_POINT” additional service to send as a low-cost delivery from Bring’s parcel shops or parcel lockers. Otherwise the shipment will be sent as a more expensive home delivery.
			 */
			'PICKUP_PARCEL_BULK'         => [
				'ProductCode' => '0342',
				'productName' => 'PickUp Parcel Bulk',
				'description' => 'Pakken kan spores og utleveres på ditt lokale hentested.',
				'helptext'    => 'PickUp Parcel leveres til mottakers lokale hentested. Mottaker kan velge å hente sendingen på et annet hentested enn sitt lokale. Mottaker varsles om at sendingen er ankommet via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingsnummeret.',
				'deprecated'  => false,
				'pickuppoint' => false,
			],

			/**
			 * Oil Express products can be shipped only in Norway and between certain postal codes.
			 */
			'OIL_EXPRESS'                => [
				'ProductCode' => '3050',
				'productName' => 'Oil express',
				'helptext'    => 'Oil Express products can be shipped only in Norway and between certain postal codes.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],

			/**
			 * Frigo
			 *
			 * Frigo products can be ordered only in Norway and requires a Frigo customer.
			 */
			'FRIGO'                      => [
				'ProductCode' => '',
				'productName' => 'FRIGO',
				'helptext'    => 'Frigo products can be ordered only in Norway and requires a Frigo customer.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
		],
	],
	'courier'      => [
		'title'    => __( 'Courier', 'bring-fraktguiden-for-woocommerce' ),
		'expanded' => false,
		'services' => [
			'COURIER_VIP' => [
				'ProductCode' => 'VIP25',
				'productName' => 'Budbil VIP',
				'helptext'    => 'Sendingen hentes hos avsender så raskt som mulig, eller til et fastsatt tidspunkt, og fraktes direkte til leveringssted med bil.',
				'description' => 'Samme dags leveringsløsning der sendingen hentes hos avsender så raskt som mulig, eller til et fastsatt tidspunkt, og fraktes direkte til leveringssted med bil. Ingen sendinger fra andre avsendere er med på samme oppdrag. Sendingen hentes og leveres av samme transportør.',
				'deprecated'  => false,
				'pickuppoint' => false,
			],
			'COURIER_1H'  => [
				'ProductCode' => '1H25',
				'productName' => 'Budbil 1 time',
				'helptext'    => 'Sending hentes hos avsender og leveres til mottaker innen 1 time.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'COURIER_2H'  => [
				'ProductCode' => '2H25',
				'productName' => 'Budbil 2 timer',
				'helptext'    => 'Sending hentes hos avsender og leveres til mottaker innen 2 timer.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'COURIER_4H'  => [
				'ProductCode' => '4H25',
				'productName' => 'Budbil 4 timer',
				'helptext'    => 'Sending hentes hos avsender og leveres til mottaker innen 4 timer.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'COURIER_6H'  => [
				'ProductCode' => '6H25',
				'productName' => 'Budbil 6 timer',
				'helptext'    => 'Sending hentes hos avsender og leveres til mottaker innen 6 timer.',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
		],
	],
	'homedelivery' => [
		'title'    => __( 'Home Delivery', 'bring-fraktguiden-for-woocommerce' ),
		'expanded' => false,
		'services' => [
			/**
			 * Home Delivery Product List
			 * Lead time given is from first Bring terminal to the terminal delivering
			 * the goods. Local cut-off times are not accounted for, so the client should
			 * adjust for these. These products are only available when accessed with a
			 * PARCELS_NORWAY customer number tied to an appropriate agreement.
			 */
			'SINGLE_INDOOR'                 => [
				'ProductCode' => '',
				'productName' => 'Single indoor',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'CURBSIDE'                      => [
				'ProductCode' => '',
				'productName' => 'Curbside',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'CURBSIDE_EXPRESS'              => [
				'ProductCode' => '',
				'productName' => 'Curbside Express',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'CURBSIDE_WEEKEND'              => [
				'ProductCode' => '',
				'productName' => 'Curbside Weekend',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'CURBSIDE_WEEKEND_EXPRESS'      => [
				'ProductCode' => '',
				'productName' => 'Curbside Weekend Express',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'CURBSIDE_EVENING'              => [
				'ProductCode' => '',
				'productName' => 'Curbside Evening',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'DOUBLE_INDOOR'                 => [
				'ProductCode' => '',
				'productName' => 'Double Indoor',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'INDOOR_LIGHT'                  => [
				'ProductCode' => '',
				'productName' => 'Indoor Light',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'SINGLE_INDOOR_EXPRESS'         => [
				'ProductCode' => '',
				'productName' => 'Single Indoor Express',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'DOUBLE_INDOOR_EXPRESS'         => [
				'ProductCode' => '',
				'productName' => 'Double Indoor Express',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'INDOOR_LIGHT_EXPRESS'          => [
				'ProductCode' => '',
				'productName' => 'Indoor Light Express',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'SINGLE_INDOOR_WEEKEND'         => [
				'ProductCode' => '',
				'productName' => 'Single Indoor Weekend',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'DOUBLE_INDOOR_WEEKEND'         => [
				'ProductCode' => '',
				'productName' => 'Double Indoor Weekend',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'INDOOR_LIGHT_WEEKEND'          => [
				'ProductCode' => '',
				'productName' => 'Indoor Light Weekend',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'SINGLE_INDOOR_WEEKEND_EXPRESS' => [
				'ProductCode' => '',
				'productName' => 'Single Indoor Weekend Express',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'DOUBLE_INDOOR_WEEKEND_EXPRESS' => [
				'ProductCode' => '',
				'productName' => 'Double Indoor Weekend Express',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'INDOOR_LIGHT_WEEKEND_EXPRESS'  => [
				'ProductCode' => '',
				'productName' => 'Indoor Light Weekend Express',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'DOUBLE_INDOOR_NO'              => [
				'ProductCode' => '',
				'productName' => 'Double Indoor (NO)',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'SINGLE_INDOOR_EVENING'         => [
				'ProductCode' => '',
				'productName' => 'Single Indoor Evening',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'DOUBLE_INDOOR_EVENING'         => [
				'ProductCode' => '',
				'productName' => 'Double Indoor Evening',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
			'INDOOR_LIGHT_EVENING'          => [
				'ProductCode' => '',
				'productName' => 'Indoor Light Evening',
				'helptext'    => '',
				'deprecated'  => true,
				'pickuppoint' => false,
			],
		],
	],
];
