# Bring Fraktguiden for WooCommerce

WooCommerce shipping plugin integrating the Bring/Posten carrier API. Admin UI is built with a custom build-time template compiler — understanding this pipeline is essential before working on any admin page or component.

## Architecture Overview

Admin pages are written as `.bfg.php` source templates using a custom component syntax. At build time, `npm run compile-php` compiles them into plain PHP files in `build/`. The PHP runtime only ever loads files from `build/` — source files are never included directly. There is zero runtime overhead from the compiler.

## PHP version

The plugin requires PHP 8.3. The local command line PHP may be newer.

Write code that runs on PHP 8.3. Do not use syntax or functions added after
8.3, even when the local PHP accepts them.

The requirement is written in three places. Change all three together:
`readme.txt`, the `Requires PHP` header in
`bring-fraktguiden-for-woocommerce.php`, and the `php` entry in
`composer.json`.

## Data over lists

Mark a trait on the data, never as a list of ids in code. A service that behaves
in a special way carries a flag in `config/services.php`, and the code reads
that flag.

A list of ids in code goes stale the moment someone adds a row to the data, and
nothing points the reader from the data to the list.

Write a list in code only when no data source holds the trait. Name the source
in a comment when you do.

### Data keys

Write a data key as a plain string, in the same spelling the data file uses.

Do: `$service['nvit'] ?? true`.

Do not: `private const FLAG = 'nvit';` and then `$service[self::FLAG]`. The
constant hides the key from the reader and buys nothing.

Name the key after the trait of the row, not after the rule that reads it. A
second rule may read the same trait later.

Return the flag as it is. Do not wrap it in a comparison such as
`false !== ($service['nvit'] ?? true)`. A boolean needs no test.

## Fields and rules

A field is one thing. Describe it in one place.

Do: hold the meta key, the label, the description, the placeholder and the
clean rule of a field in one value object. Walk a list of those objects to
render and to save. A script config is one more reader of that list.

Do not: write the same field out again in a second render method, in a save
method and in a script config. Every copy goes stale on its own.

Do: give a save method one typed argument per field, or one object.

Do not: pass several untyped values in a fixed order. Two of them swap without
a sound.

Do: give a class one job. A class that renders, saves, prints markup and loads
a script is four classes.

## Failed idea: several shipping lines per order

The booking code loops over every Bring shipping line of an order and sends one
booking per line. `Bring_Booking::send_booking()` holds the loop.

The idea does not work. A WooCommerce order records no link between a product
line and a shipping line. Nothing says which goods travel on which shipment.

So each booking claims the whole order. `order_update_packages()` packs every
product line of the order, then saves that package list on whichever shipping
line it was called for. An order with two Bring shipping lines therefore books
the same goods twice.

Any new per order data follows the same shape, because the order offers nothing
finer. Write it for the whole order and let each booking carry it.

Do not build on the loop. Do not add a feature that needs to know which goods
belong to which shipping line.

The booking box of the order screen already books one shipment per order. See
`doc/booking-box.md`. The loop stays only for the bulk action on the orders
list.

## Directory Map

| Path | Purpose |
|---|---|
| `src/templates/admin/pages/*.bfg.php` | Admin page source templates (edit these) |
| `src/templates/admin/pages/pro/*.bfg.php` | Pro page state partials (one per license state) |
| `src/components/*.bfgc.php` | Reusable component definitions |
| `build/*` | Compiled output (do not edit) |
| `resources/css/tailwind.css` | CSS source — Tailwind + all component styles |
| `classes/BringFraktguiden/Admin/SettingsPage.php` | Registers admin menu, renders all pages |
| `bin/compile-templates.php` | Compilation entry point |
| `src/Compiler/` | Custom HTML parser + processor pipeline |

## Template System (.bfg.php)

Source templates use a custom tag syntax that compiles away entirely:

| Syntax | Compiles to |
|---|---|
| `<bfg-section>` | Expanded component HTML |
| `<t>Text</t>` | `<?php esc_html_e('Text', 'bring-fraktguiden-for-woocommerce'); ?>` |
| `:attr="$var"` | PHP variable substitution in attribute |
| `attr="value"` | Static attribute (auto-translated if component handles it) |
| `<slot/>` | Replaced with component inner content |
| `<if :attr>...</if>` | Conditional block (renders if attribute is present) |
| `<else>...</else>` | Else branch |

**Rules:**
- Always use closing tags: `<bfg-section></bfg-section>` — self-closing breaks compilation
- Keep `<t>` text on a single line — line breaks break translation extraction
- After any edit to `.bfg.php` or `.bfgc.php`, run `npm run compile-php`

A PHP tag works in attribute position, so `<option <?php selected($a, $b); ?>>`
compiles. A `<t>` tag works inside a component, because the compiler translates
slot content after it expands the component.

## Admin Page Rendering

All pages are registered and rendered by `BringFraktguiden\Admin\SettingsPage`.

Each page method:
1. Calls `self::maybe_build()` — auto-compiles in local dev
2. Prepares PHP variables (e.g., `$steps`, `$fields`, `$currency`)
3. Calls `require_once` on the compiled file from `build/templates/admin/pages/`

The compiled template receives variables via PHP scope — no explicit passing.

**Pages (source → compiled):**

| Page | Source | Data provided |
|---|---|---|
| Home / Get Started | `home.bfg.php` | `$steps` (array of Step objects), `$stepCount`, `$stepsCompleted`, `$nextStep` |
| Settings | `settings.bfg.php` | `$fields` (Fields instance), `$currency` |
| Pro | `pro.bfg.php` + `pro/*.bfg.php` | License/subscription data + derived view vars (`$bfg_cards_active`, `$bfg_features_subtitle`, etc.) — all prepared in `SettingsPage::pro_page()` |
| Pro Settings | `pro-settings.bfg.php` | Pro fields |
| Booking | `booking.bfg.php` | Booking config |
| Service Wizard | `service-wizard.bfg.php` | — |
| Fallback Options | `fallback-options.bfg.php` | — |
| Kitchen Sink | `kitchen-sink.bfg.php` | Dev-only component gallery |

## Component Inventory

Components live in `src/components/*.bfgc.php`. Each file has a docblock with description, usage example, and available attributes.

**Categories:**
- **Layout:** `section`, `section.header`, `section.section`
- **Notices:** `notice`
- **Badges:** `badge.completed`, `badge.completed.md`, `badge.completed.text`, `badge.in-progress`, `badge.progress`
- **Steps:** `step.completed`, `step.in-progress`, `step.pending`, `step-desc`
- **Fields:** `field.text`, `field.number`, `field.select`, `field.checkbox`, `conditional-field-group`
- **Feature cards:** `feature-card`, `feature-card.icon`, `feature-card.benefits`, `feature-list`
- **Subscription:** `subscription-info`, `subscription-item.*` (4 variants)
- **Pro page:** `pro-license-form`
- **Utilities:** `t`, `access-link`, `indicator-dot`, `progress`

**To see all components with descriptions:**
```bash
php bin/list-components.php
```

**Naming rule:** Tag `<bfg-section.header>` → file `src/components/section.header.bfgc.php` (drop `bfg-` prefix, add `.bfgc.php`).

## CSS

| Layer | Prefix | Where |
|---|---|---|
| Tailwind utilities | `bfgu:` (Tailwind v4 variant syntax) | Any template/component |
| Component/block styles | `bfg-` (BEM) | `resources/css/tailwind.css` |

- Edit styles in `resources/css/tailwind.css`
- Output compiles to `build/css/admin.css` — never edit this directly
- Frontend/checkout legacy styles: `assets/css/bring-fraktguiden.css`
- Pro-specific admin styles: `pro/assets/css/admin.css`

## Build Commands

| Command | When to run |
|---|---|
| `npm run compile-php` | After editing any `.bfg.php` or `.bfgc.php` file |
| `npm run build` | Full production build (CSS + JS + PHP) |
| `npm run dev` | Development build |
| `npm run watch` | Watch mode for CSS/JS (PHP still needs manual compile) |
| `npm run test-php-compiler` | Run compiler tests |

## Translations

The catalogs live in `languages/`. The POT file holds the source strings. The
`.po` file holds one language. The `.mo` file is the compiled form that
WordPress reads.

The text domain is `bring-fraktguiden-for-woocommerce`.

### Compile the templates first

Run `npm run compile-php` before you scan for strings.

A `<t>` tag in a `.bfg.php` file becomes an `esc_html_e()` call only in
`build/`. A scan of `src/` alone loses most admin strings.

`build/` is in `.gitignore`, so the line references in the POT file point at
paths that a fresh checkout does not hold. That is expected. Only the `msgid`
matters to a translator.

### Update the catalogs

Run the four commands in order.

```
npm run compile-php
wp i18n make-pot . languages/bring-fraktguiden-for-woocommerce.pot \
  --slug=bring-fraktguiden-for-woocommerce \
  --exclude=node_modules,vendor,tests,bin,src/Compiler
msgmerge --no-fuzzy-matching --update --backup=none \
  languages/bring-fraktguiden-for-woocommerce-nb_NO.po \
  languages/bring-fraktguiden-for-woocommerce.pot
msgfmt -o languages/bring-fraktguiden-for-woocommerce-nb_NO.mo \
  languages/bring-fraktguiden-for-woocommerce-nb_NO.po
```

Use `--no-fuzzy-matching`. `msgmerge` otherwise guesses a translation from a
similar string and marks the entry fuzzy. WordPress skips a fuzzy entry and
shows the English text.

Run `msgfmt` after every edit of the `.po` file. WordPress reads the `.mo`
file, never the `.po` file.

### The bundled file does not always win

WordPress looks for a translation in `wp-content/languages/plugins/` first. It
falls back to the plugin `languages/` folder only when that file is absent.

WordPress downloads the first file from translate.wordpress.org on its own, for
every plugin in the wordpress.org directory. A download therefore hides the
bundled catalog, and an edit to `languages/` changes nothing on that site.

Check `wp-content/languages/plugins/bring-fraktguiden-for-woocommerce-*` when a
new translation does not appear on a site. Delete that file to test the bundled
one. WordPress downloads it again on the next update check.

Since WordPress 6.5 a downloaded translation can arrive as a `.l10n.php` file.
WordPress loads it in place of the `.mo` file with the same name.

### What translate.wordpress.org needs

A string appears on translate.wordpress.org only after a release ships as the
stable tag. A string added on a branch stays invisible there.

WordPress builds a language pack once 90 percent of the strings of the stable
release are translated and approved for that language. Below that threshold no
pack exists, and every site falls back to the bundled catalog.

A pack that already exists rebuilds after any change, even below 90 percent.

To send the Norwegian work upstream, import
`languages/bring-fraktguiden-for-woocommerce-nb_NO.po` on the plugin page at
translate.wordpress.org. The bundled file then serves only sites that have no
pack yet.

### When to load the text domain

WordPress loads the text domain of a wordpress.org plugin on its own. Since 4.6
it reads `wp-content/languages/plugins/`. Since 6.7 it loads just in time, at
the first translation call.

A language pack needs no `load_plugin_textdomain()` call, because it sits in
that folder.

The bundled catalog in the plugin `languages/` folder still needs the call.
The call registers the folder, and core reaches a bundled file only through
that registration. Keep the call.

Load a translation at the `init` action or later. WordPress 6.7 prints a
`_doing_it_wrong` notice when a translation call runs earlier.

`Bring_Fraktguiden::loaded()` runs on `plugins_loaded` and holds the setup.
`Bring_Fraktguiden::init()` runs on the `init` action and holds only the
`load_plugin_textdomain()` call. Keep the two apart.

## Database

Run `mysql` with no parameters. The file `~/.my.cnf` holds the user, the socket
and the default database. Add missing values to that file if a plain `mysql`
call fails.

The table prefix is `bfgd_`, so the options table is `bfgd_options`.

## Test site

The local WordPress admin is at https://bringdemo.test/wp/wp-admin/ .

An order screen is at `https://bringdemo.test/wp/wp-admin/post.php?post=<id>&action=edit`.

## Test orders

`bin/make-test-order.php` makes an order the Bring booking box accepts. Run it
with WP-CLI from the WordPress root.

```
wp eval-file wp-content/plugins/bring-fraktguiden-for-woocommerce/bin/make-test-order.php 9008 none,0.4,299,1 62034000,0,450,2
```

The first argument is the shipping postal code. 9008 is Tromso, which is an NVIT
transit route.

Every argument after it makes one order line, as `hs,weight,price,quantity`.
Write `none` for a product without an HS code. Write `0` for a missing weight or
a missing price.

The script prints the order id, the edit URL and the customs problems it made.
It reuses a product per set of values, so a repeat run adds no duplicate product.

The printed URL uses the `siteurl` option, which is `http://localhost`. Open the
order on https://bringdemo.test/ instead.

## Skills

Use `/skill-name` in chat to invoke:

| Skill | Use when |
|---|---|
| `/use-component` | Looking up how to use an existing component in a template |
| `/create-component` | Creating a new `.bfgc.php` component (TDD workflow) |
| `/templating` | Building or modifying admin pages (structure, fields, patterns) |
| `/css` | Styling decisions — Tailwind vs component class vs custom CSS |

## Documentation

`doc/` holds durable facts only. A durable fact stays true across sessions, for
example an external rule, an API contract, or a decision and its reason.

`doc/report/` holds everything else. Put a research note, an audit, a status
write-up, a migration log or any dated snapshot there.

Write a doc for a reader who was not in the session that produced it. Do not
record the questions or comparisons that came up while you worked.

## Working
Commit using the commit skill when you consider work done. Try to only commit your own work, but sometimes multiple session tangle their edits in the same files. In such cases commit their changes too.