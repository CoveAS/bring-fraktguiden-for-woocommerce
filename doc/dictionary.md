# Dictionary

The words this plugin uses, and what each one means here.

Many words are Norwegian, or are the trade name of one carrier. A reader from
outside Norway cannot guess them.

## The carrier

**Bring**
: The parcel carrier. Bring is the business arm of Posten, the Norwegian postal
  company. The plugin talks to the Bring APIs.

**Posten**
: The Norwegian postal company. Posten owns Bring. A service name may carry
  either word.

**Fraktguiden**
: Norwegian for "the freight guide". It is the Bring price API. The plugin asks
  it for the price of a cart, and shows the answers as shipping options.

**Mybring**
: The Bring customer portal, at https://www.mybring.com/ . A shop owner finds
  the API key there. The login email of that portal is the `mybring_api_uid`
  setting.

**customer number**
: The number of the account that pays for a shipment. A booking carries it in
  `consignments[].product.customerNumber`. See [booking-customer-number.md](booking-customer-number.md).

**Quickship**
: A partner that sells a Norwegian shipping agreement, at https://quickship.no/ .
  A shop with no Bring account can get one through Quickship at a lower price.
  The setup page offers this path on a Norwegian shop only.

**fraktavtale**
: Norwegian for "shipping agreement". It is the contract between a shop and
  Bring that sets the shipping prices.

## Services and shipments

**service**
: One product Bring sells, for example "Pakke i postkassen". Each service has an
  id and a row in `config/services.php`. A shop picks the services it offers.

**VAS**
: A value added service. It is an extra on a shipment, named by a number. VAS
  2084 is the electronic notification, also called `EVARSLING`.

**Pakkeboks**
: An unmanned parcel locker in Norway, open all day. A Pakkeboks parcel weighs
  at most 10 kg. See [pickup-point-types.md](pickup-point-types.md).

**pick-up point**
: A place where the customer collects the parcel. It is a shop, a post office or
  a Pakkeboks.

**NVIT**
: Short for "norske varer i transitt", Norwegian goods in transit. A shipment is
  NVIT when it travels from Norway to Norway through Sweden or Finland. The
  goods leave Norwegian ground, so they need customs data. See [nvit.md](nvit.md).

**HS code**
: The Harmonized System code. It is a number of 6 to 10 digits that names a kind
  of goods to customs. A customs declaration needs one per item line.

**booking**
: The order of a shipment at Bring. The plugin sends the order data to the
  Bring booking API, and Bring answers with a consignment and a label.

**consignment**
: One shipment inside a booking. It carries a consignment number, which is the
  number the customer tracks.

**label**
: The address sheet that goes on the parcel. Bring holds it as a file, and the
  plugin downloads it from the link in the booking answer.

**ZPL**
: Zebra Programming Language. It is the label format of a thermal label
  printer. Bring gives a label as ZPL or as PDF.

**return label**
: A second label that lets the customer send the parcel back. Bring makes one
  when the booking carries a `returnProduct`, and invoices it only when the
  customer uses it. See [return-label.md](return-label.md).

## The shop side

**shipping zone**
: A WooCommerce group of places with its own shipping options. Bring shows no
  option to a customer until a zone holds the Bring method.

**fallback price**
: The price the plugin charges when Bring gives no answer. This happens when the
  cart is too big, too heavy or too full, or when the Bring API is quiet.

**Pro**
: The paid part of the plugin, in the `pro/` folder. It adds booking, pick-up
  points and more. The license server ties a license to one shop domain.

## The template system

**`.bfg.php`**
: The source of an admin page. A build step compiles it into plain PHP in
  `build/`. The runtime loads the compiled file only.

**`.bfgc.php`**
: The source of a reusable component, in `src/components/`. A tag such as
  `<bfg-section.header>` expands to the markup of `section.header.bfgc.php`.

**`<t>`**
: A tag that marks text for translation. It compiles to an `esc_html_e()` call.

**`<slot/>`**
: The place inside a component where the content of the tag lands.

**step form partial**
: A step of the setup page that holds a form. The `form` property of a `Step`
  names a file in `src/templates/admin/pages/home/`. The page requires the
  compiled file in place of the plain step markup.

**`bfgu:`**
: The prefix of a Tailwind utility class in this plugin, for example
  `bfgu:flex`. It keeps the utilities apart from the WordPress admin styles.

**`bfg-`**
: The prefix of a component class, written in BEM, for example
  `bfg-connect__signup`. These live in `resources/css/`.

## Translation

**text domain**
: The name that groups the strings of one plugin. Here it is
  `bring-fraktguiden-for-woocommerce`.

**POT file**
: The catalog of source strings in English. A translator starts from it.

**PO file**
: The catalog of one language, with a translation per string.

**MO file**
: The compiled form of a PO file. WordPress reads the MO file, never the PO
  file.

**language pack**
: A translation that WordPress downloads from translate.wordpress.org into
  `wp-content/languages/plugins/`. A downloaded pack hides the catalog bundled
  with the plugin.
