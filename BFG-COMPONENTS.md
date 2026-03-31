# BFG Component System

A **build-time template compilation system** for WordPress admin pages that transforms clean, component-based syntax into standard PHP/HTML with zero runtime overhead.

## Quick Start

```bash
# Build all templates
./bin/build

# Run tests
php bin/test-compiler.php
```

## How It Works

### 1. Write Source Templates (`.bfg.php`)
Clean, component-based syntax with auto-translation:

```php
<bfg-section title="Settings" description="Configure your options">
    <bfg-field.text id="api-key" name="api_key" label="API Key" />
</bfg-section>
```

### 2. Compile to Standard PHP
Run `./bin/build` to compile to `build/templates/`:

```php
<div class="bfg-section">
    <div class="bfg-section__header">
        <h2><?php esc_html_e('Settings', 'bring-fraktguiden-for-woocommerce'); ?></h2>
        <p><?php esc_html_e('Configure your options', 'bring-fraktguiden-for-woocommerce'); ?></p>
    </div>
    <div class="bfg-section__section">
        <div class="bfg-field">
            <label for="api-key"><?php esc_html_e('API Key', 'bring-fraktguiden-for-woocommerce'); ?></label>
            <input type="text" id="api-key" name="api_key">
        </div>
    </div>
</div>
```

### 3. WordPress Includes Compiled Output
Admin pages include from `build/templates/`:

```php
require_once dirname(__DIR__, 3) . '/build/templates/admin/pages/settings.php';
```

## Project Structure

```
.
├── bin/
│   ├── build                      ← Build script (runs compiler)
│   ├── compile-templates.php      ← Compiler implementation
│   └── test-compiler.php          ← Test runner
├── src/
│   ├── components/                ← Component templates (.bfgc.php)
│   │   ├── section.bfgc.php
│   │   ├── notice.bfgc.php
│   │   ├── field.text.bfgc.php
│   │   ├── field.select.bfgc.php
│   │   ├── step.completed.bfgc.php
│   │   └── ...
│   └── templates/                 ← Source files (.bfg.php)
│       └── admin/pages/
│           ├── home.bfg.php
│           ├── settings.bfg.php
│           ├── booking.bfg.php
│           └── kitchen-sink.bfg.php
├── build/
│   └── templates/                 ← Compiled output (.php) [gitignored]
│       └── admin/pages/
│           ├── home.php
│           ├── settings.php
│           └── ...
└── tests/
    ├── input/                     ← Test input files
    └── expected/                  ← Expected output files
```

## Component Template Syntax

### Basic Template Structure

Component templates use simple placeholders:

```php
<!-- src/components/notice.bfgc.php -->
<div class="bfg-notice bfg-notice--:type">
    <t>slot</t>
</div>
```

### Placeholders

- `<t>varname</t>` - Translatable text (wraps with `esc_html_e()`)
- `:varname` - Attribute value substitution
- `<slot/>` - Inject inner content
- `<if :varname>...</if>` - Conditional rendering
- `<else>...</else>` - Else branch

### Dynamic Values

Use `:` prefix for PHP variables (no translation):

```php
<bfg-section :title="$dynamic_title">  <!-- Uses variable, not translated -->
<bfg-section title="Static Title">     <!-- Translated string -->
```

## Key Features

- ✅ **PHP tag preservation** - Existing `<?php ?>` tags pass through unchanged
- ✅ **Auto-translation** - String literals wrapped with `esc_html_e()`
- ✅ **Build-time compilation** - Zero runtime overhead
- ✅ **Component reusability** - Define once, use everywhere
- ✅ **TDD workflow** - Test-driven component development
- ✅ **Warning system** - Alerts for plain `.php` files in source directory

## Available Components

- **Layout**: `section`, `notice`, `progress`
- **Fields**: `field.text`, `field.number`, `field.select`, `field.checkbox`
- **Steps**: `step.completed`, `step.in-progress`, `step.pending`, `step-desc`
- **Badges**: `badge.completed`, `badge.in-progress`, `badge.progress`
- **Cards**: `status-card`, `status-item`
- **Lists**: `feature-list`
- **Utilities**: `t` (translation wrapper)

See `src/templates/admin/pages/kitchen-sink.bfg.php` for usage examples.

## Creating New Components

Use the `/create-component` skill for TDD workflow:

```bash
# Creates component template, test input, and expected output
# Runs tests to verify compilation
```

Or manually:

1. Create component template in `src/components/my-component.bfgc.php`
2. Create test input in `tests/input/my-component.bfg.php`
3. Create expected output in `tests/expected/my-component.php`
4. Run `php bin/test-compiler.php` to verify

## Build Process

The build process:

1. Scans `src/templates/` for `.bfg.php` files
2. Warns about plain `.php` files (should be `.bfg.php`)
3. Compiles each file using component templates
4. Outputs to `build/templates/` (preserving directory structure)
5. Reports success/failure for each file

## Benefits

- **Cleaner source files** - 50% less boilerplate than raw PHP
- **Translation-ready** - Automatic WordPress i18n wrapping
- **Build-time processing** - Zero runtime performance impact
- **Type safety** - Attributes validated during compilation
- **Testable** - TDD workflow with automated tests
- **No dependencies** - Pure PHP, uses native DOM parser
