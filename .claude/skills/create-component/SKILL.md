---
name: create-component
description: Create a new BFG component using TDD approach. Creates component template (.bfgc.php), test input (.bfg.php), and expected output (.php), then runs tests to verify compilation works correctly.
---

# Create Component Skill

This skill guides you through creating a new BFG component using a test-driven development (TDD) approach.

## Process

### 1. Review existing components and compiled output
- Run `php bin/list-components.php` to see all available components
- Run `php bin/list-components.php --detailed` for component descriptions
- Check `src/templates/admin/pages/kitchen-sink.bfg.php` for component usage examples
- Check `build/templates/admin/pages/kitchen-sink.php` for compiled output examples
- Identify the HTML structure, classes, and behavior of the component

### 2. Create test input file
**Location:** `tests/input/{component-name}.bfg.php`

Example:
```php
<bfg-notice>This is a test notice</bfg-notice>
```

- Use simple, clear test cases
- Include representative attributes
- Keep it minimal but complete

### 3. Create expected output file
**Location:** `tests/expected/{component-name}.php`

Example:
```php
<div class="bfg-notice-banner">
    <span class="bfg-notice-icon">
        <svg>...</svg>
    </span>
    <p>This is a test notice</p>
</div>
```

- Match the exact compiled HTML structure
- Use `</path>` not `<path ... />` (DOM parser expands self-closing tags)
- Include all wrapper elements and classes

### 4. Create component template
**Location:** `src/components/{component-name}.bfgc.php`

Example:
```php
<?php
/**
 * BFG Notice Component
 *
 * Documentation and usage examples here
 */
?>

<div class="bfg-notice-banner">
    <span class="bfg-notice-icon">
        <svg>...</svg>
    </span>
    <p><slot /></p>
</div>
```

**Component syntax:**
- `<t>varname</t>` - Translatable text, compiles to `<?php esc_html_e('value', 'text-domain'); ?>`
- `:varname` - Raw attribute value replacement
- `<slot/>` - Inserts inner content from component tag
- `<if :varname>...</if>` - Conditional rendering based on attribute existence
- `<else>...</else>` - Optional else block for conditionals
- Unmatched attributes automatically pass through to root element
- **IMPORTANT:** Self-closing component tags are NOT supported. Always use `</tag>` closing tags, not `<tag />`

### 5. Run tests
```bash
php bin/test-compiler.php
```

- Tests should initially fail (red)
- Fix any compilation issues
- Adjust expected output if DOM parser formatting differs
- Tests should pass (green)

### 6. Verify
- ✅ All tests passing
- ✅ Component template has proper documentation
- ✅ Test cases cover typical usage

## Example: Notice Component

**Input:** `tests/input/notice.bfg.php`
```php
<bfg-notice>This is a test notice</bfg-notice>
```

**Expected:** `tests/expected/notice.php`
```php
<div class="bfg-notice-banner">
    <span class="bfg-notice-icon">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
    </span>
    <p>This is a test notice</p>
</div>
```

**Component:** `src/components/notice.bfgc.php`
```php
<?php
/**
 * BFG Notice Component
 */
?>

<div class="bfg-notice-banner">
    <span class="bfg-notice-icon">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
    </span>
    <p><slot /></p>
</div>
```

## Component Naming Convention

| Tag Name | Component File |
|----------|----------------|
| `<bfg-box>` | `box.bfgc.php` |
| `<bfg-box.header>` | `box.header.bfgc.php` |
| `<bfg-box.section>` | `box.section.bfgc.php` |
| `<bfg-notice>` | `notice.bfgc.php` |
| `<bfg-field.text>` | `field.text.bfgc.php` |
| `<bfg-badge.completed>` | `badge.completed.bfgc.php` |

**Rule:** Remove `bfg-` prefix, add `.bfgc.php` extension.

**Nested components:** Use dot notation (e.g., `box.header`) for sub-components that belong to a parent component.

## Common Issues

1. **Self-closing component tags NOT supported:**
   - ❌ `<bfg-box.header title="Test" />` - Will break compilation, rest of content is lost
   - ✅ `<bfg-box.header title="Test"></bfg-box.header>` - Always use closing tags
   - This applies to component tags, not HTML elements inside components
2. **Self-closing SVG/HTML tags:** DOM parser expands `<path ... />` to `<path ...></path>` in expected output
3. **Whitespace differences:** Test normalizer removes empty lines and trims - focus on actual content differences
4. **Missing attributes:** Unmatched attributes automatically pass through to root element
5. **Conditionals:** Use `<if :varname>` to check attribute existence, not value comparison
6. **Multi-line text in `<bfg-t>` tags:** Keep text on single line to avoid line breaks in translation strings
   - ❌ `<bfg-t>Full Width\n\t\t\tButton</bfg-t>` - Creates broken translation
   - ✅ `<t>Full Width Button</t>` - Keep on one line

## Reference Files & Tools

- Strategy: `COMPONENT-COMPILER-STRATEGY.md`
- Compiler: `bin/compile-templates.php`
- Test runner: `bin/test-compiler.php`
- Component lister: `bin/list-components.php` (use `--detailed` for descriptions)
- Kitchen sink: `src/templates/admin/pages/kitchen-sink.bfg.php`