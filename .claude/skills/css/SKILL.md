---
name: css
description: Use for styling or css work
---


# CSS guide


Chose the correct css file to use

- Frontend styles, assets/css/bring-fraktguiden.css, cart and checkout pages via [wp_enqueue_scripts](classes/ResourceManagement/Styles.php)
- WooCommerce settings styles, assets/css/bring-fraktguiden-admin.css, WooCommerce > Settings page via [admin_enqueue_scripts](classes/ResourceManagement/Scripts.php)
- Plugin admin pages styles, assets/css/bring-fraktguiden-admin-pages.css, Bring Fraktguiden menu pages `toplevel_page_bring_fraktguiden_home`, `bring-fraktguiden_page_bring_fraktguiden_settings`, `bring-fraktguiden_page_bring_fraktguiden_fallback`, `bring-fraktguiden_page_bring_fraktguiden_booking` via  [admin_enqueue_scripts](classes/BringFraktguiden/Admin/SettingsPage.php)
- Pro admin styles, pro/assets/css/admin.css, all admin pages via [admin_enqueue_scripts](pro/class-wc-shipping-method-bring-pro.php)


Avoid using !important unless it is necessary. When using !important document why it was used in a comment after the rule on the same line.
example:

```css
.bfg-custom-select {
	position: relative;
	width: 100% !important; /* override the .woocmerce.foo.bar class */
	max-width: none !important; /* override the .wordpress.foo.bar class */
	display: block;
}
```

Ground rules:
- Use prefix `bfgu-` for utility classes and `bfg-` for all other custom classes.
- Utility classes should be based on tailwind and at the top of the file.
- Component classes at bottom of file.
- When 4 our more utility classes are used on the same element, consider creating a component class.


## Component classes

Component classes should be named using BEM (Block, Element, Modifier) notation.

Example:

```css
.bfg-block {
	...
}
.bfg-block__element {
	...
}
.bfg-block__element--modifier {
	...
}
```

Avoid using "Element" unless there is a clear need for it.