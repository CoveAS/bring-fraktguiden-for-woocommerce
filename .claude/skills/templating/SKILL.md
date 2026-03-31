---
name: templating
description: Use for working with custom admin pages
---

# Admin Templating Guide

Templates are organized in:
- `templates/admin/pages/` - Full page templates (settings, home, booking, etc.)
- `templates/admin/components/` - Reusable UI components
- `templates/admin/fields/` - Form field templates

## CSS Class Naming (BEM)

Classes use BEM methodology with `bfg-` prefix:
- **Block**: `bfg-{block}` - Standalone entity (e.g., `bfg-section`, `bfg-field`)
- **Element**: `bfg-{block}__{element}` - Part of a block (e.g., `bfg-section__header`, `bfg-section__section`)
- **Modifier**: `bfg-{block}--{modifier}` - Variant of block/element (e.g., `bfg-field--checkbox-box`, `bfg-badge--completed`)

### Block Hierarchy Examples

```
bfg-section                        // Container card
├── bfg-section__header           // Title area with h2 and optional description
│   └── bfg-section__header--divider  // Modifier for top border
└── bfg-section__section          // Content area with fields

bfg-step-row                   // Clickable row with indicator
├── bfg-step-row__indicator   // Left circle with number/checkmark
├── bfg-step-row__number      // Number inside indicator
├── bfg-step-row__content     // Text container
│   ├── bfg-step-row__label   // Title text
│   └── bfg-step-row__description  // Subtitle text
└── bfg-step--in-progress     // State modifier on parent

bfg-subscription              // Subscription status display
├── bfg-subscription__item    // Single key-value row
│   ├── bfg-subscription__label
│   └── bfg-subscription__value
│       └── bfg-subscription__value--success  // Type modifier
└── bfg-subscription--expired  // State modifier
```

## Utility Classes

Use `bfgu-` prefix for spacing/layout utilities:
- `bfgu-flex`, `bfgu-flex-row`, `bfgu-flex-1`
- `bfgu-gap-{n}`, `bfgu-mt-{n}`, `bfgu-mb-{n}`

## Components vs Fields

### When to Create a Component

Components are **layout wrappers** or **compound UI elements**. Create one when:

1. It wraps multiple elements (e.g., `input-with-suffix` combines a field + span)
2. It has visual styling beyond a form control (e.g., `checkbox-box` adds card styling)
3. It's reused across multiple pages with the same structure
4. It contains conditional rendering logic (e.g., `step-row` shows checkmark OR number)

**Location**: `templates/admin/components/{name}.php`

**Invocation**:
```php
use BringFraktguiden\Admin\Component;
echo Component::boxHeader('Title', 'Description');
echo Component::checkboxBox($field);
echo Component::inputWithSuffix($field, 'cm', 'sm');
```

### When to Create a Field

Fields are **raw form controls** that map to field types. Create one when:

1. It renders a single HTML form element (`<input>`, `<select>`, `<textarea>`)
2. It's tied to a field type in the `Field` class (`text`, `number`, `checkbox`, `select`)
3. It needs access to field data (`$name`, `$value`, `$options`, `$custom_attributes`)

**Location**: `templates/admin/fields/{type}.php`

**Invocation**: Fields render automatically via the `Field` class:
```php
echo $fields->my_field;           // Full render (wrapper + field + description)
echo $fields->my_field->field();  // Just the form control
echo $fields->my_field->label();  // Just the label
```

## Building a Settings Page

### Basic Structure

```php
<div class="wrap bfg-admin-page bfg-admin-page__{page-name}">
    <div class="bfg-page__main">
        <div class="bfg-page__header">
            <h1><?php esc_html_e('Page Title', 'bring-fraktguiden-for-woocommerce'); ?></h1>
        </div>
        <div class="bfg-notices">
            <div class="wp-header-end"></div>
        </div>

        <form method="post" action="options.php">
            <?php settings_fields('bring_fraktguiden_settings'); ?>

            <!-- Content boxes here -->

        </form>
    </div>
</div>
```

### Content Box Pattern

```php
<div class="bfg-section">
    <?php echo Component::boxHeader(
        __('Section Title', 'bring-fraktguiden-for-woocommerce'),
        __('Optional description text', 'bring-fraktguiden-for-woocommerce')
    ); ?>

    <div class="bfg-section__section">
        <!-- Standard field -->
        <div class="bfg-field">
            <?php echo $fields->my_field->label(); ?>
            <?php echo $fields->my_field; ?>
        </div>

        <!-- Checkbox in card style -->
        <?php echo Component::checkboxBox($fields->my_checkbox); ?>

        <!-- Input with unit suffix -->
        <div class="bfg-field">
            <?php echo $fields->price->label(); ?>
            <?php echo Component::inputWithSuffix($fields->price, 'NOK', 'lg'); ?>
        </div>

        <!-- Grouped fields -->
        <div class="bfg-field">
            <label class="bfg-field-group-title"><?php esc_html_e('Group Label', 'bring-fraktguiden-for-woocommerce'); ?></label>
            <div class="bfgu-flex bfgu-flex-row bfgu-gap-4">
                <div class="bfgu-flex-1">
                    <?php echo $fields->field_a->label(); ?>
                    <?php echo $fields->field_a->field(); ?>
                </div>
                <div class="bfgu-flex-1">
                    <?php echo $fields->field_b->label(); ?>
                    <?php echo $fields->field_b->field(); ?>
                </div>
            </div>
            <p class="bfg-description"><?php esc_html_e('Shared description', 'bring-fraktguiden-for-woocommerce'); ?></p>
        </div>

        <?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
    </div>
</div>
```

## Adding a New Component

1. Create template in `templates/admin/components/{name}.php`:

```php
<?php
/**
 * My Component
 *
 * @var array $args {
 *     @type string $title Required. Component title.
 *     @type string $type  Optional. Type modifier.
 * }
 */
$args = wp_parse_args($args ?? [], [
    'title' => '',
    'type' => 'default',
]);

$class = 'bfg-my-component';
if ($args['type'] !== 'default') {
    $class .= ' bfg-my-component--' . $args['type'];
}
?>
<div class="<?php echo esc_attr($class); ?>">
    <span class="bfg-my-component__title"><?php echo esc_html($args['title']); ?></span>
</div>
```

2. Add static method in `Component.php`:

```php
public static function myComponent(string $title, string $type = 'default'): string
{
    return self::render('my-component', [
        'title' => $title,
        'type' => $type,
    ]);
}
```

## Available Components

| Component | Usage | Method |
|-----------|-------|--------|
| `box-header` | Section title with description | `Component::boxHeader($title, $desc, $divider)` |
| `checkbox-box` | Checkbox in card container | `Component::checkboxBox($field)` |
| `input-with-suffix` | Input + unit label | `Component::inputWithSuffix($field, $suffix, $size)` |
| `notice-banner` | Alert/warning message | `Component::noticeBanner($message, $type)` |
| `step-row` | Setup wizard step | `Component::stepRow($args)` |
| `progress-bar` | Completion indicator | `Component::progressBar($completed, $total, $label)` |
| `status-card` | Key-value status display | `Component::statusCard($items, $type)` |
| `badge` | Status label | `Component::badge($text, $type)` |
| `feature-list` | Checkmark list | `Component::featureList($features, $compact)` |
| `custom-select` | Styled dropdown | `Component::customSelect($name, $options, $selected)` |
