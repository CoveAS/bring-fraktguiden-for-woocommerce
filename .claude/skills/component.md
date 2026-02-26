---
name: component
description: Use when working with components in admin/templates/pages.
---

# Component System Documentation

The plugin uses a component-based architecture for rendering reusable admin UI elements. Components are PHP templates located in `templates/admin/components/` and are rendered via the `Component` class.

## Quick Start

```php
use BringFraktguiden\Admin\Component;

// Use convenience methods (recommended)
echo Component::boxHeader('Title', 'Description');
echo Component::checkboxBox($field);

// Or use the generic render method
echo Component::render('badge', ['text' => 'Active', 'type' => 'completed']);
```

## The Component Class

Location: `classes/BringFraktguiden/Admin/Component.php`

The `Component` class provides two ways to render components:

1. **Static convenience methods** - Type-safe, IDE-friendly
2. **Generic render method** - Flexible, takes component name and args array

---

## Available Components

### boxHeader

Renders a section header with title and optional description.

```php
echo Component::boxHeader(
    __('Display Options', 'bring-fraktguiden-for-woocommerce'),
    __('Customize how shipping options appear to customers', 'bring-fraktguiden-for-woocommerce'),
    false // $divider - show top border
);
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$title` | string | Yes | - | Section title |
| `$description` | string | No | `''` | Description text (supports HTML via `wp_kses_post`) |
| `$divider` | bool | No | `false` | Show top divider line |

**Output structure:**
```html
<div class="bfg-box__header [bfg-box__header--divider]">
    <h2>Title</h2>
    <p>Description</p>
</div>
```

---

### boxSection

Wraps content in a box section container.

```php
echo Component::boxSection($content);
```

**Parameters:**
| Name | Type | Required | Description |
|------|------|----------|-------------|
| `$content` | string | Yes | Inner HTML content |

**Output structure:**
```html
<div class="bfg-box__section">
    {content}
</div>
```

---

### checkboxBox

Renders a checkbox field inside a card-style container. Used for feature toggles.

```php
echo Component::checkboxBox($fields->debug);
echo Component::checkboxBox($fields->display_desc);
```

**Parameters:**
| Name | Type | Required | Description |
|------|------|----------|-------------|
| `$field` | Field | Yes | A checkbox Field object |

**Output structure:**
```html
<div class="bfg-field bfg-field--checkbox-box">
    {field HTML}
</div>
```

**Usage note:** The Field object is cast to string, which triggers its `__toString()` method.

---

### inputWithSuffix

Renders a number input with a unit suffix (cm, kg, currency, etc.).

```php
echo Component::inputWithSuffix($fields->handling_fee, $currency, 'lg');
echo Component::inputWithSuffix($fields->minimum_length, 'cm');
echo Component::inputWithSuffix($fields->minimum_weight, 'kg');
echo Component::inputWithSuffix($fields->lead_time, __('days', 'bring-fraktguiden-for-woocommerce'));
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$field` | Field | Yes | - | The field object |
| `$suffix` | string | Yes | - | Suffix text (cm, kg, $, days) |
| `$size` | string | No | `'sm'` | Suffix size: `'sm'` or `'lg'` |

**Output structure:**
```html
<div class="bfg-input bfg-input--number">
    <input type="..." ... />
    <span class="bfg-suffix|bfg-suffix-lg">cm</span>
</div>
```

---

### noticeBanner

Renders a notification banner with icon and message.

```php
echo Component::noticeBanner(
    __('Your trial has expired.', 'bring-fraktguiden-for-woocommerce'),
    'warning'
);
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$message` | string | Yes | - | Message text (supports HTML via `wp_kses_post`) |
| `$type` | string | No | `'warning'` | Type: `'warning'`, `'info'`, `'success'`, `'error'` |

**Output structure:**
```html
<div class="bfg-notice-banner">
    <span class="bfg-notice-icon">{svg icon}</span>
    <p>Message</p>
</div>
```

---

### badge

Renders a status badge.

```php
echo Component::badge(__('Completed', 'bring-fraktguiden-for-woocommerce'), 'completed');
echo Component::badge(__('In Progress', 'bring-fraktguiden-for-woocommerce'), 'in-progress');
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$text` | string | Yes | - | Badge text |
| `$type` | string | No | `'default'` | Type: `'completed'`, `'in-progress'`, `'default'` |

**Output structure:**
```html
<span class="bfg-badge [bfg-badge--completed|bfg-badge--in-progress]">
    Text
</span>
```

---

### stepRow

Renders a clickable step item for setup wizards.

```php
echo Component::stepRow([
    'number' => 1,
    'label' => __('Configure shipping', 'bring-fraktguiden-for-woocommerce'),
    'description' => __('Set up your shipping zones', 'bring-fraktguiden-for-woocommerce'),
    'action' => admin_url('admin.php?page=wc-settings&tab=shipping'),
    'completed' => true,
    'in_progress' => false,
]);
```

**Parameters (array):**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `number` | int | No | `1` | Step number |
| `label` | string | No | `''` | Step title |
| `description` | string | No | `''` | Step description |
| `action` | string | No | `'#'` | URL for the step action |
| `completed` | bool | No | `false` | Whether step is completed |
| `in_progress` | bool | No | `false` | Whether step is currently active |

**Output structure:**
```html
<a href="..." class="bfg-step-row [bfg-step--in-progress]">
    <div class="bfg-step-row__indicator">
        {checkmark svg OR step number}
    </div>
    <div class="bfg-step-row__content">
        <div class="bfg-step-row__label">Label</div>
        <div class="bfg-step-row__description">Description</div>
    </div>
    <span class="bfg-badge ...">Status</span>
</a>
```

---

### progressBar

Renders a progress bar with optional label.

```php
echo Component::progressBar(3, 5, sprintf(__('%d of %d completed', 'bring-fraktguiden-for-woocommerce'), 3, 5));
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$completed` | int | Yes | - | Completed steps count |
| `$total` | int | Yes | - | Total steps count |
| `$label` | string | No | `''` | Optional label text |

**Output structure:**
```html
<div class="bfg-progress-container">
    <span class="bfg-progress-badge">Label</span>
    <div class="bfg-progress-bar-new">
        <div class="bfg-progress-bar-fill" style="width: 60%;"></div>
    </div>
</div>
```

---

### statusCard

Renders a status card with key-value pairs.

```php
echo Component::statusCard([
    ['label' => __('Status', 'bring-fraktguiden-for-woocommerce'), 'value' => __('Active', 'bring-fraktguiden-for-woocommerce'), 'type' => 'success'],
    ['label' => __('License Type', 'bring-fraktguiden-for-woocommerce'), 'value' => __('PRO License', 'bring-fraktguiden-for-woocommerce'), 'type' => 'default'],
], 'default');
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$items` | array | Yes | - | Array of items with `label`, `value`, `type` |
| `$type` | string | No | `'default'` | Card type: `'default'`, `'test'`, `'trial'`, `'expired'` |

**Item value types:** `'default'`, `'success'`, `'test'`, `'trial'`, `'expired'`

**Output structure:**
```html
<div class="bfg-pro-status-card [bfg-pro-status-card--trial]">
    <div class="bfg-pro-status-card__item">
        <span class="bfg-pro-status-card__label">Label</span>
        <span class="bfg-pro-status-card__value [bfg-pro-status-card__value--success]">Value</span>
    </div>
</div>
```

---

### featureList

Renders a grid of features with checkmark icons.

```php
echo Component::featureList([
    __('MyBring Booking', 'bring-fraktguiden-for-woocommerce'),
    __('Free shipping threshold', 'bring-fraktguiden-for-woocommerce'),
    __('Fixed price per service', 'bring-fraktguiden-for-woocommerce'),
], true); // compact = true
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$features` | array | Yes | - | Array of feature strings |
| `$compact` | bool | No | `false` | Use compact styling |

**Output structure:**
```html
<ul class="bfg-pro-features-grid [bfg-pro-features-grid--compact]">
    <li>
        {checkmark svg}
        Feature text
    </li>
</ul>
```

---

### customSelect

Renders a custom styled select dropdown.

```php
echo Component::customSelect(
    'language',
    [
        'no' => __('Norwegian', 'bring-fraktguiden-for-woocommerce'),
        'en' => __('English', 'bring-fraktguiden-for-woocommerce'),
    ],
    'no', // selected value
    __('Select language', 'bring-fraktguiden-for-woocommerce') // placeholder
);
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$name` | string | Yes | - | Input name attribute |
| `$options` | array | Yes | - | Associative array of value => label |
| `$selected` | string | No | `''` | Currently selected value |
| `$placeholder` | string | No | `''` | Placeholder text |

---

### fieldWrapper

Wraps content in a field container.

```php
echo Component::fieldWrapper($content, 'bfg-field--special');
```

**Parameters:**
| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `$content` | string | Yes | - | Inner HTML content |
| `$class` | string | No | `''` | Additional CSS classes |

**Output structure:**
```html
<div class="bfg-field [additional-class]">
    {content}
</div>
```

---

## Generic Render Method

For flexibility or when adding new components:

```php
echo Component::render('component-name', [
    'arg1' => 'value1',
    'arg2' => 'value2',
]);
```

The component template receives the `$args` array and should use `wp_parse_args()` for defaults:

```php
<?php
// templates/admin/components/my-component.php
$args = wp_parse_args($args ?? [], [
    'text' => '',
    'type' => 'default',
]);
?>
<div class="my-component">
    <?php echo esc_html($args['text']); ?>
</div>
```

---

## Creating a New Component

1. Create a template file in `templates/admin/components/{name}.php`
2. Add a docblock describing the expected `$args`
3. Use `wp_parse_args()` for defaults
4. Escape all output appropriately
5. (Optional) Add a convenience method to `Component` class

**Template example:**

```php
<?php
/**
 * My Component
 *
 * @var array $args {
 *     @type string $title Required. The title.
 *     @type string $type  Optional. Type: 'primary', 'secondary'.
 * }
 */

$args = wp_parse_args($args ?? [], [
    'title' => '',
    'type' => 'primary',
]);
?>
<div class="bfg-my-component bfg-my-component--<?php echo esc_attr($args['type']); ?>">
    <h3><?php echo esc_html($args['title']); ?></h3>
</div>
```

**Convenience method:**

```php
public static function myComponent(string $title, string $type = 'primary'): string
{
    return self::render('my-component', [
        'title' => $title,
        'type' => $type,
    ]);
}
```

---

## Page Layout Pattern

A typical admin page structure:

```php
<?php
use BringFraktguiden\Admin\Component;

/** @var Fields $fields */
?>
<div class="wrap bfg-admin-page bfg-admin-page__settings">
    <div class="bfg-page__main">
        <div class="bfg-page__header">
            <h1><?php esc_html_e('Page Title', 'bring-fraktguiden-for-woocommerce'); ?></h1>
        </div>

        <div class="bfg-notices">
            <div class="wp-header-end"><!-- Notices appear after this div --></div>
        </div>

        <form method="post" action="options.php">
            <?php settings_fields('bring_fraktguiden_settings'); ?>

            <div class="bfg-box">
                <?php echo Component::boxHeader(
                    __('Section Title', 'bring-fraktguiden-for-woocommerce'),
                    __('Section description', 'bring-fraktguiden-for-woocommerce')
                ); ?>

                <div class="bfg-box__section">
                    <!-- Fields go here -->
                    <div class="bfg-field">
                        <?php echo $fields->my_field->label(); ?>
                        <?php echo $fields->my_field; ?>
                    </div>

                    <?php echo Component::checkboxBox($fields->my_checkbox); ?>

                    <?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
                </div>
            </div>
        </form>
    </div>
</div>
```

---

## CSS Classes Reference

| Class | Description |
|-------|-------------|
| `.bfg-admin-page` | Main page wrapper |
| `.bfg-page__main` | Content container |
| `.bfg-page__header` | Page header with title |
| `.bfg-box` | Card/box container |
| `.bfg-box__header` | Box header section |
| `.bfg-box__section` | Box content section |
| `.bfg-field` | Field wrapper |
| `.bfg-field--checkbox-box` | Checkbox card style |
| `.bfg-input` | Input wrapper |
| `.bfg-input--number` | Number input with suffix |
| `.bfg-badge` | Status badge |
| `.bfg-step-row` | Setup step item |
| `.bfg-notice-banner` | Notification banner |
| `.bfg-pro-status-card` | PRO status card |
| `.bfg-pro-features-grid` | Features grid list |
