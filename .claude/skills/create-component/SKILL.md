---
name: create-component
description: Create a new BFG component using TDD approach. Creates component template (.bfgc.php), test input (.bfg.php), and expected output (.php), then runs tests to verify compilation works correctly.
---

# Create Component Skill

Create BFG components using TDD.

Before deciding to create a component you should consider if any existing component can be used instead. If you determine that there is an existing component, you should ask the user what to do, pushing back on creating the component.

Ask the user what the name of the component should be.
You can give the user suggestions, but you do not have authority to decide the name of the component. Try to suggest names that are descriptive of the look and apperance of the component, rather than the function.

When the user has confirmed, you may continue.

## Workflow

1. **Create test input:** `tests/input/{name}.bfg.php`
2. **Create component:** `src/components/{name}.bfgc.php`
3. **Generate expected output:**
   ```bash
   php bin/compile-templates.php tests/input/{name}.bfg.php
   cp build/templates/{name}.php tests/expected/{name}.php
   ```
4. **Run tests:** `./bin/test`
5. **Add component to kitchen sink:** `src/templates/admin/pages/kitchen-sink.bfg.php`

## Component Syntax

- `<t>text</t>` - Translatable text → `<?php esc_html_e('text', 'domain'); ?>`
- `:attr` - Attribute value replacement
- `<slot/>` - Insert component inner content
- `<if :attr>...</if>` - Conditional rendering if attribute exists
- `<else>...</else>` - Else block for conditionals
- Unmatched attributes pass through to root element

## Naming Convention

| Component Tag | File |
|--------------|------|
| `<bfg-box>` | `box.bfgc.php` |
| `<bfg-box.header>` | `box.header.bfgc.php` |
| `<bfg-field.text>` | `field.text.bfgc.php` |

**Rule:** Remove `bfg-` prefix, add `.bfgc.php` extension. Use dots for sub-components.

## Critical Rules

1. **❌ Self-closing component tags NOT supported**
   - ❌ `<bfg-box />` - Breaks compilation
   - ✅ `<bfg-box></bfg-box>` - Always use closing tags
2. **Keep `<bfg-t>` text on single line** - Multi-line breaks translations
3. **DOM parser expands self-closing HTML tags** - `<path />` becomes `<path></path>` in output

## Useful Commands

```bash
php bin/list-components.php           # List all components
php bin/test-compiler.php             # Run tests
```

## Reference

- Kitchen sink example: `src/templates/admin/pages/kitchen-sink.bfg.php`
- Compiled output: `build/templates/admin/pages/kitchen-sink.php`
