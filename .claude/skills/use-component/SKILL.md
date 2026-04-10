---
name: use-component
description: Reference for using existing BFG components in .bfg.php templates. Use when adding a component to a page, looking up component syntax or attributes, converting raw HTML to components, or understanding available components.
---

# Use Component Skill

This skill helps you use existing BFG components in your `.bfg.php` templates.

## Quick Reference

### List Available Components
```bash
# List all components
php bin/list-components.php

# Show detailed info with descriptions
php bin/list-components.php --detailed
```

### Check Component Documentation
Each component file (`src/components/*.bfgc.php`) has a docblock with:
- Description
- Usage examples
- Available attributes
- Compiled output example

## Component Syntax

### Basic Usage
```php
<bfg-component-name>
    Content here
</bfg-component-name>
```

### With Attributes
```php
<bfg-component-name attribute="value" :dynamic-attr="$phpVariable">
    Content here
</bfg-component-name>
```

### Translatable Text (Inside Components)
```php
<t>Text to translate</t>
```
Compiles to: `<?php esc_html_e('Text to translate', 'bring-fraktguiden-for-woocommerce'); ?>`

### Dynamic Attributes
- Static: `attribute="hardcoded value"`
- Dynamic: `:attribute="$variable"` or `:attribute="function()"`
- Dynamic expressions: `:attribute="$var + 10"` or `:attribute="condition ? 'yes' : 'no'"`

## Migration Guide: HTML to Components

This section shows how to convert existing HTML markup to BFG components.

### Converting bfg-section from HTML to Components

**Before (HTML):**
```php
<div class="bfg-section">
    <div class="bfg-section__header">
        <h2><?php esc_html_e('Title', 'bring-fraktguiden-for-woocommerce'); ?></h2>
        <p><?php esc_html_e('Description here', 'bring-fraktguiden-for-woocommerce'); ?></p>
    </div>

    <div class="bfg-section__section">
        <p>Your content here</p>
    </div>
</div>
```

**After (Component):**
```php
<bfg-section>
    <bfg-section.header
        title="Title"
        description="Description here"
    ></bfg-section.header>

    <bfg-section.section>
        <p>Your content here</p>
    </bfg-section.section>
</bfg-section>
```

**Key Points:**
- The component automatically wraps `title` and `description` in `esc_html_e()` during compilation
- Just provide the plain text without translation functions
- The text domain is added automatically

### Translation Handling

Components handle translation automatically for certain attributes:

**Attributes that auto-translate:**
- `<bfg-section.header title="..." description="...">` - both title and description
- Any text inside `<t>...</t>` tags

**Manual translation still needed:**
- Labels, placeholders, and other PHP code outside components
- Dynamic content from variables

**Example:**
```php
<!-- Component attributes: auto-translated -->
<bfg-section.header title="Settings" description="Configure your options"></bfg-section.header>

<!-- Content inside t: auto-translated -->
<bfg-notice type="info">
    <t>Your changes have been saved</t>
</bfg-notice>

<!-- Labels outside components: manual translation needed -->
<label><?php esc_html_e('Email Address', 'bring-fraktguiden-for-woocommerce'); ?></label>
```

### Escaping Special Characters in Attributes

When attribute values contain special characters, use HTML entities:

```php
<!-- Quotes in descriptions -->
<bfg-section.header
    title="Shipping Address"
    description="By default, your WooCommerce store address is used as the &quot;from&quot; address during booking."
></bfg-section.header>

<!-- Apostrophes -->
<bfg-section.header
    title="User's Profile"
    description="Manage the user&apos;s personal information"
></bfg-section.header>
```

Common entities: `&quot;` for `"`, `&apos;` for `'`, `&amp;` for `&`, `&lt;` for `<`, `&gt;` for `>`

### Multiple Headers/Sections (Divider Pattern)

You can have multiple headers and sections in one box for sub-sections:

```php
<bfg-section>
    <!-- Main section -->
    <bfg-section.header
        title="Shipping Address"
        description="Configure your shipping details"
    ></bfg-section.header>
    <bfg-section.section>
        <p>Main section content...</p>
    </bfg-section.section>

    <!-- Sub-section with divider -->
    <bfg-section.header
        class="bfg-section__header--divider"
        title="Contact Information"
    ></bfg-section.header>
    <bfg-section.section>
        <p>Contact fields...</p>
    </bfg-section.section>
</bfg-section>
```

The `bfg-section__header--divider` class adds a visual separator.

### Converting Notices

**Before (Legacy Component class):**
```php
<?php echo Component::noticeBanner(
    '<strong>' . esc_html__('WARNING!', 'bring-fraktguiden-for-woocommerce') . '</strong> ' .
    esc_html__('This will change the status', 'bring-fraktguiden-for-woocommerce'),
    'warning'
); ?>
```

**After (Component):**
```php
<bfg-notice type="warning">
    <strong><t>WARNING!</t></strong> <t>This will change the status</t>
</bfg-notice>
```

### Custom Classes on Components

Pass custom classes through to components:

```php
<!-- On root element -->
<bfg-section class="my-custom-class another-class">
    <bfg-section.header title="Title"></bfg-section.header>
    <bfg-section.section>...</bfg-section.section>
</bfg-section>

<!-- On sub-components -->
<bfg-section.header
    class="bfg-section__header--divider"
    title="Section Title"
></bfg-section.header>
```

Classes are passed through to the root element of the compiled output.

### What to Convert vs. What to Keep

**✅ Convert to Components:**
- `<div class="bfg-section">` → `<bfg-section>`
- `Component::noticeBanner()` → `<bfg-notice>`
- Any structural/presentational markup with component equivalents

**❌ Keep as PHP Methods:**
- Form fields with custom behavior
- Any component that has complex PHP logic beyond presentation

**Example - Mixed Approach:**
```php
<bfg-section>
    <bfg-section.header title="Settings"></bfg-section.header>
    <bfg-section.section>
        <!-- Component for notice -->
        <bfg-notice type="info">
            <t>Configure your options below</t>
        </bfg-notice>

        <!-- Keep PHP method for complex form field -->
        <?php echo Component::validatedInputField([
            'id' => 'email',
            'type' => 'email',
            'label' => __('Email', 'bring-fraktguiden-for-woocommerce'),
            'validation' => ['required', 'email'],
        ]); ?>
    </bfg-section.section>
</bfg-section>
```

## Common Components

### Box Components (Container)
```php
<bfg-section class="custom-class">
    <bfg-section.header title="Title Here" description="Optional description"></bfg-section.header>
    <bfg-section.section>
        <p>Your content here</p>
    </bfg-section.section>
</bfg-section>
```

**Important:**
- Always use closing tags: `</bfg-section.header>`, NOT `<bfg-section.header />`
- Description is optional (will be hidden if not provided)
- Custom classes and attributes pass through to root element

### Notices
```php
<bfg-notice type="warning">
    <t>This is a warning message</t>
</bfg-notice>
```

Types: `warning`, `info`, `success`, `error`

### Badges
```php
<bfg-badge.completed><t>Completed</t></bfg-badge.completed>
<bfg-badge.in-progress><t>In Progress</t></bfg-badge.in-progress>
<bfg-badge.progress>3 of 5 completed</bfg-badge.progress>
```

### Form Fields
```php
<bfg-field.text
    name="field_name"
    :value="$fieldValue"
    placeholder="Enter text"
></bfg-field.text>

<bfg-field.number
    name="quantity"
    :value="$quantity"
    min="0"
    max="100"
></bfg-field.number>

<bfg-field.checkbox
    name="enabled"
    :checked="$isEnabled"
>
    <t>Enable this feature</t>
</bfg-field.checkbox>
```

### Progress Bar
```php
<bfg-progress :value="$percentage"></bfg-progress>
```

### Status Card
```php
<bfg-status-card class="status-card--success">
    <bfg-status-item label="Status" value="Active"></bfg-status-item>
    <bfg-status-item label="License Type" value="PRO"></bfg-status-item>
</bfg-status-card>
```

### Steps
```php
<bfg-step.completed :href="$link">
    Step Title
    <bfg-step-desc>Step description here</bfg-step-desc>
    <bfg-badge.completed><t>Completed</t></bfg-badge.completed>
</bfg-step.completed>

<bfg-step.in-progress :href="$link" :number="2">
    Step Title
    <bfg-step-desc>Step description here</bfg-step-desc>
    <bfg-badge.in-progress><t>In Progress</t></bfg-badge.in-progress>
</bfg-step.in-progress>

<bfg-step.pending :href="$link" :number="3">
    Step Title
    <bfg-step-desc>Step description here</bfg-step-desc>
</bfg-step.pending>
```

## Important Rules

### ✅ DO
- Use closing tags for all components: `<bfg-section></bfg-section>`
- Keep `<t>` text on single line (no line breaks)
- Use `:attribute` syntax for dynamic/PHP values
- Use plain `attribute` for static strings
- Escape special characters in attributes: `&quot;` for quotes, `&apos;` for apostrophes
- Check component docblocks for usage examples
- Test compilation with `npm run compile-php`

### ❌ DON'T
- Use self-closing component tags: `<bfg-section.header />`
- Put line breaks inside `<t>` tags
- Mix static and dynamic attributes incorrectly
- Forget to compile after changes

## Workflow

1. **Find component**
   ```bash
   php bin/list-components.php
   ```

2. **Check documentation**
   - Open `src/components/{component}.bfgc.php`
   - Read docblock for usage examples and attributes

3. **Use in template**
   - Edit your `.bfg.php` file
   - Add component with proper syntax
   - Remember: closing tags required!

4. **Compile**
   ```bash
   npm run compile-php
   ```

5. **Verify**
   - Check compiled output in `build/templates/`
   - Test in browser

## Examples

### Complete Box Example
```php
<bfg-section class="my-custom-box" :data-id="$boxId">
    <bfg-section.header
        title="Configuration"
        description="Manage your plugin settings"
    ></bfg-section.header>
    <bfg-section.section>
        <div class="bfg-field">
            <label><t>Enable Feature</t></label>
            <bfg-field.checkbox
                name="enable_feature"
                :checked="$isEnabled"
            >
                <t>Turn this feature on</t>
            </bfg-field.checkbox>
        </div>

        <bfg-notice type="info">
            <t>This setting applies globally</t>
        </bfg-notice>
    </bfg-section.section>
</bfg-section>
```

### Conditional Rendering
```php
<?php if ($showWarning): ?>
    <bfg-notice type="warning">
        <t>Important: Review your settings before saving</t>
    </bfg-notice>
<?php endif; ?>
```

### Loops
```php
<?php foreach ($steps as $i => $step): ?>
    <?php if ($step->completed): ?>
        <bfg-step.completed :href="$step->url">
            <?php echo esc_html($step->title); ?>
            <bfg-step-desc><?php echo esc_html($step->description); ?></bfg-step-desc>
            <bfg-badge.completed><t>Completed</t></bfg-badge.completed>
        </bfg-step.completed>
    <?php else: ?>
        <bfg-step.pending :href="$step->url" :number="$i + 1">
            <?php echo esc_html($step->title); ?>
            <bfg-step-desc><?php echo esc_html($step->description); ?></bfg-step-desc>
        </bfg-step.pending>
    <?php endif; ?>
<?php endforeach; ?>
```

## Troubleshooting

### Component not compiling?
- Check component exists: `php bin/list-components.php`
- Verify syntax: closing tags required
- Run: `npm run compile-php`
- Check errors in terminal output

### Attributes not working?
- Use `:attr="$var"` for PHP variables
- Use `attr="value"` for static strings
- Check component docblock for supported attributes

### Styling issues?
- Components output specific classes
- Check compiled output in `build/templates/`
- See kitchen sink for examples: `src/templates/admin/pages/kitchen-sink.bfg.php`

## Reference

- Component files: `src/components/*.bfgc.php`
- Kitchen sink (all components): `src/templates/admin/pages/kitchen-sink.bfg.php`
- Compiled output: `build/templates/admin/pages/kitchen-sink.php`
- List components: `php bin/list-components.php --detailed`
