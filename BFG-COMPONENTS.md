# BFG Component System

## What We're Building

A **custom template compilation system** for WordPress admin pages that transforms clean, declarative syntax into standard PHP/HTML.

The system compiles `.bfg.php` source files with custom component notation into standard `.php` files using reusable `.bfgc.php` component templates.

## How It Works

### 1. Source Files (`.bfg.php`)
Custom notation with component tags:

```php
<bfg-box title="My Box" description="Description" class="extra-class">
    <p>Content</p>
</bfg-box>
```

### 2. Component Templates (`.bfgc.php`)
Simple templates with placeholders:

```php
<div class="bfg-box {{$class}}" {{ $attributes }}>
    <div class="bfg-box__header">
        <h2>{{ $title }}</h2>
        @if ($description)
        <p>{{ $description }}</p>
        @endif
    </div>
    <div class="bfg-box__section">
        {{ $slot }}
    </div>
</div>
```

### 3. Compiled Output (`.php`)
Standard WordPress PHP:

```php
<div class="bfg-box extra-class">
    <div class="bfg-box__header">
        <h2><?php esc_html_e('My Box', 'bring-fraktguiden-for-woocommerce'); ?></h2>
        <?php if (!empty($description)) : ?>
        <p><?php esc_html_e('Description', 'bring-fraktguiden-for-woocommerce'); ?></p>
        <?php endif; ?>
    </div>
    <div class="bfg-box__section">
        <p>Content</p>
    </div>
</div>
```

## Key Features

- **Class forwarding** - `{{$class}}` merges with base classes
- **Attribute passthrough** - `{{ $attributes }}` forwards unknown attributes
- **Conditional rendering** - `@if`/`@endif` for optional content
- **Auto-translation** - Compiler wraps strings with `esc_html_e()`
- **Slot content** - `{{ $slot }}` for inner content
- **No runtime overhead** - Compiled at build time

## Key Files

### Component System

**Component Templates:**
- `src/components/box.bfgc.php` - Box component with class/attribute support

**Documentation:**
- `src/components/COMPILER.md` - Detailed compilation algorithm and guide

### Template Files

**Source (Custom Notation):**
- `src/templates/admin/pages/kitchen-sink.bfg.php` - Demo page with `<bfg-box>`, `<bfg-notice>`, etc.

**Compiled Output:**
- `build/templates/admin/pages/kitchen-sink.php` - Standard WordPress PHP

### Project Structure

```
.
├── BFG-COMPONENTS.md              ← This file
├── src/
│   ├── components/
│   │   ├── box.bfgc.php           ← Component templates
│   │   └── COMPILER.md            ← Compilation guide
│   └── templates/
│       └── admin/
│           └── pages/
│               └── kitchen-sink.bfg.php  ← Source files
└── build/
    └── templates/
        └── admin/
            └── pages/
                └── kitchen-sink.php      ← Compiled output
```

## What's Missing

### Compiler Script

The main piece missing is the compiler itself - a PHP script (e.g., `bin/compile-templates.php`) that:

1. Finds all `.bfg.php` files in `src/templates/`
2. Parses custom tags (e.g., `<bfg-box>`)
3. Loads matching `.bfgc.php` component templates
4. Replaces placeholders with actual values
5. Handles `@if` directives
6. Wraps strings with WordPress translation functions
7. Outputs compiled `.php` files to `build/templates/`

## Current Status

**✅ Completed:**
- Component template format defined (`.bfgc.php`)
- Source file format defined (`.bfg.php`)
- Example component created (`box.bfgc.php`)
- Compilation algorithm documented
- Example source file created
- Example compiled output created (manually)

**🚧 In Progress:**
- Evaluating alternative approaches (Pug, Twig, etc.)
- Deciding on final implementation approach

**⏳ Todo:**
- Implement compiler script
- Add more component templates
- Integrate with build process
- Add validation/error handling
- Create additional components (notice, field, progress, etc.)

## Design Decisions

### Translation Handling (Option B)
The compiler handles translation by wrapping string literals in `esc_html_e()`:

```php
// Source:
<bfg-box title="My Title">

// Compiled:
<h2><?php esc_html_e('My Title', 'bring-fraktguiden-for-woocommerce'); ?></h2>
```

For dynamic values, use `:` prefix:
```php
<bfg-box :title="$page_title">  // Don't translate, use variable
```

### Reserved Attributes
- `title`, `description`, `class` - Extracted as named variables
- Everything else → Passed through via `{{ $attributes }}`

### Conditional Rendering
Uses Blade-like `@if`/`@endif` syntax in component templates:

```php
@if ($description)
<p>{{ $description }}</p>
@endif
```

If $description is present the template compiles to:
```php
<p><?php esc_html_e('Description', 'bring-fraktguiden-for-woocommerce'); ?></p>
```

## Benefits

1. **Cleaner source files** - Less boilerplate, more readable
2. **Reusable components** - Define once, use everywhere
3. **Translation-ready** - Automatic WordPress i18n wrapping
4. **Type safety potential** - Can validate attributes during compilation
5. **Build-time processing** - Zero runtime overhead
6. **Familiar syntax** - Blade/Vue-like for easy adoption
7. **No dependencies** - Pure PHP, no external libraries needed

## Next Steps

1. Decide on final approach (current Blade-like syntax vs. alternatives)
2. Implement compiler script
3. Test with real-world components
4. Integrate into build pipeline
5. Document component authoring guidelines
