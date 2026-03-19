---
name: use-component
description: Guide for using existing BFG components in templates. Shows component syntax, available components, and common patterns.
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
<bfg-t>Text to translate</bfg-t>
```
Compiles to: `<?php esc_html_e('Text to translate', 'bring-fraktguiden-for-woocommerce'); ?>`

### Dynamic Attributes
- Static: `attribute="hardcoded value"`
- Dynamic: `:attribute="$variable"` or `:attribute="function()"`
- Dynamic expressions: `:attribute="$var + 10"` or `:attribute="condition ? 'yes' : 'no'"`

## Common Components

### Box Components (Container)
```php
<bfg-box class="custom-class">
    <bfg-box.header title="Title Here" description="Optional description"></bfg-box.header>
    <bfg-box.section>
        <p>Your content here</p>
    </bfg-box.section>
</bfg-box>
```

**Important:**
- Always use closing tags: `</bfg-box.header>`, NOT `<bfg-box.header />`
- Description is optional (will be hidden if not provided)
- Custom classes and attributes pass through to root element

### Notices
```php
<bfg-notice type="warning">
    <bfg-t>This is a warning message</bfg-t>
</bfg-notice>
```

Types: `warning`, `info`, `success`, `error`

### Badges
```php
<bfg-badge.completed><bfg-t>Completed</bfg-t></bfg-badge.completed>
<bfg-badge.in-progress><bfg-t>In Progress</bfg-t></bfg-badge.in-progress>
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
    <bfg-t>Enable this feature</bfg-t>
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
    <bfg-badge.completed><bfg-t>Completed</bfg-t></bfg-badge.completed>
</bfg-step.completed>

<bfg-step.in-progress :href="$link" :number="2">
    Step Title
    <bfg-step-desc>Step description here</bfg-step-desc>
    <bfg-badge.in-progress><bfg-t>In Progress</bfg-t></bfg-badge.in-progress>
</bfg-step.in-progress>

<bfg-step.pending :href="$link" :number="3">
    Step Title
    <bfg-step-desc>Step description here</bfg-step-desc>
</bfg-step.pending>
```

## Important Rules

### ✅ DO
- Use closing tags for all components: `<bfg-box></bfg-box>`
- Keep `<bfg-t>` text on single line (no line breaks)
- Use `:attribute` syntax for dynamic/PHP values
- Use plain `attribute` for static strings
- Check component docblocks for usage examples
- Test compilation with `npm run compile-php`

### ❌ DON'T
- Use self-closing component tags: `<bfg-box.header />`
- Put line breaks inside `<bfg-t>` tags
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
<bfg-box class="my-custom-box" :data-id="$boxId">
    <bfg-box.header
        title="Configuration"
        description="Manage your plugin settings"
    ></bfg-box.header>
    <bfg-box.section>
        <div class="bfg-field">
            <label><bfg-t>Enable Feature</bfg-t></label>
            <bfg-field.checkbox
                name="enable_feature"
                :checked="$isEnabled"
            >
                <bfg-t>Turn this feature on</bfg-t>
            </bfg-field.checkbox>
        </div>

        <bfg-notice type="info">
            <bfg-t>This setting applies globally</bfg-t>
        </bfg-notice>
    </bfg-box.section>
</bfg-box>
```

### Conditional Rendering
```php
<?php if ($showWarning): ?>
    <bfg-notice type="warning">
        <bfg-t>Important: Review your settings before saving</bfg-t>
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
            <bfg-badge.completed><bfg-t>Completed</bfg-t></bfg-badge.completed>
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
