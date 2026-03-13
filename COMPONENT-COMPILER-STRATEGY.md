# Component Compiler Strategy

## Overview

A simple, DOM-based template compilation system for WordPress admin pages. Compiles `.bfg.php` source files using `.bfgc.php` component templates into standard `.php` output files.

**Key principle: Simple, explicit, DOM-parseable.**

---

## Syntax

### Component Template (`.bfgc.php`)

```php
// field.text.bfgc.php
<div class="bfg-field">
    <label for=":id"><t>label</t></label>
    <input
        type="text"
        id=":id"
        name=":name"
        placeholder=":placeholder"
    />
    <p class="description"><t>description</t></p>
</div>
```

### Source File (`.bfg.php`)

```php
<bfg-field.text
    id="demo-text"
    name="demo-text"
    label="Text Input"
    placeholder="Placeholder text"
    description="Help text goes here" />
```

### Compiled Output (`.php`)

```php
<div class="bfg-field">
    <label for="demo-text"><?php esc_html_e('Text Input', 'bring-fraktguiden-for-woocommerce'); ?></label>
    <input
        type="text"
        id="demo-text"
        name="demo-text"
        placeholder="Placeholder text"
    />
    <p class="description"><?php esc_html_e('Help text goes here', 'bring-fraktguiden-for-woocommerce'); ?></p>
</div>
```

---

## Replacement Rules

### 1. Text Replacement: `<t>varname</t>`

**Translatable text content.**

```php
// Template:
<h2><t>title</t></h2>

// Usage:
<bfg-box title="My Title">

// Compiled:
<h2><?php esc_html_e('My Title', 'bring-fraktguiden-for-woocommerce'); ?></h2>
```

- Always wrapped with `esc_html_e()`
- For translatable strings only

---

### 2. Slot Replacement: `<slot/>`

**Raw content injection.**

```php
// Template:
<div class="content">
    <slot/>
</div>

// Usage:
<bfg-box>
    <p>Inner content</p>
</bfg-box>

// Compiled:
<div class="content">
    <p>Inner content</p>
</div>
```

- No translation or escaping
- Replaced with inner content of component tag

---

### 3. Attribute Replacement: `:varname`

**Raw attribute values.**

```php
// Template:
<input id=":id" name=":name" placeholder=":placeholder">

// Usage:
<bfg-field.text id="demo" name="demo" placeholder="Enter text">

// Compiled:
<input id="demo" name="demo" placeholder="Enter text">
```

- Colon `:` prefix indicates variable
- Raw value (no translation)
- Used in attribute values

---

### 4. Conditional Rendering: `<if :varname>`

**Conditional blocks with optional else.**

```php
// Template:
<if :description>
    <p><t>description</t></p>
<else>
    <p>No description provided</p>
</else>
</if>

// Usage with description:
<bfg-box title="Title" description="Some text">

// Compiled:
<p><?php esc_html_e('Some text', 'bring-fraktguiden-for-woocommerce'); ?></p>

// Usage without description:
<bfg-box title="Title">

// Compiled:
<p>No description provided</p>
```

**Rules:**
- `<if :varname>` - Renders content only if attribute exists and is not empty
- `<else>` - Optional, renders if condition is false
- `:varname` syntax indicates which attribute to check
- Compiler removes the entire `<if>` block if condition is false
- Compiler removes the `<else>` block if condition is true

**Without else:**
```php
// Template:
<if :description>
    <p><t>description</t></p>
</if>

// If no description attribute, entire block is removed from output
```

---

### 5. Unmatched Attributes

**Automatic passthrough to root element.**

```php
// Template:
<div class="bfg-box">
    <h2><t>title</t></h2>
    <slot/>
</div>

// Usage:
<bfg-box title="Title" class="custom-class" data-id="123">
    Content
</bfg-box>

// Compiled:
<div class="bfg-box custom-class" data-id="123">
    <h2><?php esc_html_e('Title', 'bring-fraktguiden-for-woocommerce'); ?></h2>
    Content
</div>
```

**Rules:**
- Attributes not referenced in template (`class`, `data-id`) → Passed to root element
- `class` attribute → Merged with existing classes
- Other attributes → Added as-is

---

## Compilation Algorithm

### Step 1: Parse Source File

```php
$sourceDoc = Dom\HTMLDocument::createFromString($sourceContent);

// Find all <bfg-*> tags
foreach ($sourceDoc->getElementsByTagName('bfg-*') as $componentTag) {
    $componentName = $componentTag->tagName; // e.g., "bfg-field.text"
    $attributes = $componentTag->attributes;
    $slotContent = $componentTag->innerHTML;
}
```

### Step 2: Load Component Template

```php
// Convert tag name to file: "bfg-field.text" -> "field.text.bfgc.php"
$fileName = str_replace('bfg-', '', $componentName) . '.bfgc.php';
$templatePath = "src/components/{$fileName}";
$templateContent = file_get_contents($templatePath);

$templateDoc = Dom\HTMLDocument::createFromString($templateContent);
```

### Step 3: Replace `<t>` Elements

```php
foreach ($templateDoc->getElementsByTagName('t') as $textElement) {
    $varName = $textElement->textContent; // e.g., "label"
    $value = $attributes[$varName] ?? '';

    $phpCode = "<?php esc_html_e('{$value}', 'bring-fraktguiden-for-woocommerce'); ?>";
    $textNode = $templateDoc->createTextNode($phpCode);

    $textElement->parentNode->replaceChild($textNode, $textElement);
}
```

### Step 4: Replace `:var` in Attributes

```php
foreach ($templateDoc->getElementsByTagName('*') as $element) {
    foreach ($element->attributes as $attr) {
        if (str_starts_with($attr->value, ':')) {
            $varName = substr($attr->value, 1); // Remove ":"
            $value = $attributes[$varName] ?? '';
            $element->setAttribute($attr->name, $value);
        }
    }
}
```

### Step 5: Handle Conditionals `<if>`

```php
foreach ($templateDoc->getElementsByTagName('if') as $ifElement) {
    // Get the variable name from :varname attribute
    $condition = null;
    foreach ($ifElement->attributes as $attr) {
        if (str_starts_with($attr->name, ':')) {
            $condition = substr($attr->name, 1); // Remove ":"
            break;
        }
    }

    if (!$condition) continue;

    // Check if attribute exists and is not empty
    $conditionMet = !empty($attributes[$condition]);

    // Find next <else> sibling if exists
    $elseElement = null;
    $nextSibling = $ifElement->nextSibling;
    while ($nextSibling) {
        if ($nextSibling->nodeName === 'else') {
            $elseElement = $nextSibling;
            break;
        }
        $nextSibling = $nextSibling->nextSibling;
    }

    if ($conditionMet) {
        // Condition true: keep <if> content, remove <else>
        foreach ($ifElement->childNodes as $child) {
            $ifElement->parentNode->insertBefore($child->cloneNode(true), $ifElement);
        }
        if ($elseElement) {
            $elseElement->parentNode->removeChild($elseElement);
        }
    } else {
        // Condition false: keep <else> content if exists
        if ($elseElement) {
            foreach ($elseElement->childNodes as $child) {
                $ifElement->parentNode->insertBefore($child->cloneNode(true), $ifElement);
            }
            $elseElement->parentNode->removeChild($elseElement);
        }
    }

    // Remove <if> element
    $ifElement->parentNode->removeChild($ifElement);
}
```

### Step 6: Replace `<slot/>`

```php
foreach ($templateDoc->getElementsByTagName('slot') as $slotElement) {
    // Parse slot content
    $slotDoc = Dom\HTMLDocument::createFromString($slotContent);

    // Import and insert nodes
    foreach ($slotDoc->body->childNodes as $child) {
        $imported = $templateDoc->importNode($child, true);
        $slotElement->parentNode->insertBefore($imported, $slotElement);
    }

    // Remove slot element
    $slotElement->parentNode->removeChild($slotElement);
}
```

### Step 7: Handle Unmatched Attributes

```php
$usedAttributes = []; // Track which attributes were used in <t> or :var

foreach ($attributes as $name => $value) {
    if (!in_array($name, $usedAttributes)) {
        $rootElement = $templateDoc->documentElement;

        if ($name === 'class') {
            // Merge classes
            $existing = $rootElement->getAttribute('class');
            $rootElement->setAttribute('class', trim("$existing $value"));
        } else {
            // Add other attributes
            $rootElement->setAttribute($name, $value);
        }
    }
}
```

### Step 8: Output

```php
$compiled = $templateDoc->saveHTML();
return $compiled;
```

---

## File Structure

```
.
├── src/
│   ├── components/
│   │   ├── box.bfgc.php
│   │   ├── field.text.bfgc.php
│   │   ├── notice.bfgc.php
│   │   └── ...
│   └── templates/
│       └── admin/
│           └── pages/
│               ├── kitchen-sink.bfg.php
│               └── ...
└── build/
    └── templates/
        └── admin/
            └── pages/
                ├── kitchen-sink.php
                └── ...
```

---

## Component Naming Convention

| Tag Name | Component File |
|----------|----------------|
| `<bfg-box>` | `box.bfgc.php` |
| `<bfg-notice>` | `notice.bfgc.php` |
| `<bfg-field.text>` | `field.text.bfgc.php` |
| `<bfg-badge.completed>` | `badge.completed.bfgc.php` |

**Rule:** Remove `bfg-` prefix, add `.bfgc.php` extension.

---

## Complete Example

### Component Template

```php
// box.bfgc.php
<div class="bfg-box">
    <div class="bfg-box__header">
        <h2><t>title</t></h2>
        <if :description>
            <p><t>description</t></p>
        </if>
    </div>
    <div class="bfg-box__section">
        <slot/>
    </div>
</div>
```

### Source File

```php
// kitchen-sink.bfg.php
<bfg-box
    title="Boxes & Containers"
    description="Primary container component"
    class="custom-box"
    data-test="123">
    <p><strong>Classes:</strong> <code>.bfg-box</code></p>
</bfg-box>
```

### Compiled Output

```php
// kitchen-sink.php
<div class="bfg-box custom-box" data-test="123">
    <div class="bfg-box__header">
        <h2><?php esc_html_e('Boxes & Containers', 'bring-fraktguiden-for-woocommerce'); ?></h2>
        <p><?php esc_html_e('Primary container component', 'bring-fraktguiden-for-woocommerce'); ?></p>
    </div>
    <div class="bfg-box__section">
        <p><strong>Classes:</strong> <code>.bfg-box</code></p>
    </div>
</div>
```

---

## Testing Strategy

### Test-Driven Development Approach

The compiler uses a simple TDD approach: write test input files and expected output files, then compare compiled results.

### Test File Structure

```
.
├── bin/
│   ├── compile-templates.php      ← Main compiler
│   └── test-compiler.php          ← Test runner script
│
├── src/
│   └── components/
│       ├── box.bfgc.php
│       ├── field.text.bfgc.php
│       └── notice.bfgc.php
│
└── tests/
    ├── input/                     ← Test source files (.bfg.php)
    │   ├── box.bfg.php
    │   ├── field.text.bfg.php
    │   └── notice.bfg.php
    └── expected/                  ← Expected compiled output (.php)
        ├── box.php
        ├── field.text.php
        └── notice.php
```

### Test Runner Script

**`bin/test-compiler.php`** - Simple standalone script that:

1. Finds all test files in `tests/input/*.bfg.php`
2. Compiles each one using the compiler
3. Compares result to corresponding file in `tests/expected/*.php`
4. Reports pass/fail with diffs

**Usage:**
```bash
php bin/test-compiler.php
```

**Output:**
```
Running compiler tests...

✅ PASS: box
✅ PASS: field.text
❌ FAIL: notice

--------------------------------------------------
Total: 3 | Passed: 2 | Failed: 1

==================================================
FAILURE DETAILS:
==================================================

Test: notice
--------------------------------------------------
Expected:
<div class="bfg-notice">...</div>

Actual:
<div class="bfg-notice warning">...</div>
```

### Test Runner Implementation

```php
#!/usr/bin/env php
<?php

/**
 * Simple test runner for component compiler
 */

require_once __DIR__ . '/compile-templates.php';

$testsDir = __DIR__ . '/../tests';
$inputDir = $testsDir . '/input';
$expectedDir = $testsDir . '/expected';

$passed = 0;
$failed = 0;
$failures = [];

echo "Running compiler tests...\n\n";

// Find all test input files
$inputFiles = glob($inputDir . '/*.bfg.php');

foreach ($inputFiles as $inputFile) {
    $testName = basename($inputFile, '.bfg.php');
    $expectedFile = $expectedDir . '/' . $testName . '.php';

    if (!file_exists($expectedFile)) {
        echo "⚠️  SKIP: {$testName} (no expected output file)\n";
        continue;
    }

    // Compile the input file
    try {
        $compiled = compileTemplate($inputFile);
        $expected = file_get_contents($expectedFile);

        // Normalize whitespace for comparison
        $compiledNormalized = normalizeWhitespace($compiled);
        $expectedNormalized = normalizeWhitespace($expected);

        if ($compiledNormalized === $expectedNormalized) {
            echo "✅ PASS: {$testName}\n";
            $passed++;
        } else {
            echo "❌ FAIL: {$testName}\n";
            $failed++;
            $failures[] = [
                'name' => $testName,
                'expected' => $expected,
                'actual' => $compiled
            ];
        }
    } catch (Exception $e) {
        echo "❌ ERROR: {$testName} - {$e->getMessage()}\n";
        $failed++;
        $failures[] = [
            'name' => $testName,
            'error' => $e->getMessage()
        ];
    }
}

// Summary
echo "\n" . str_repeat('-', 50) . "\n";
echo "Total: " . ($passed + $failed) . " | ";
echo "Passed: {$passed} | ";
echo "Failed: {$failed}\n";

// Show failure details
if (!empty($failures)) {
    echo "\n" . str_repeat('=', 50) . "\n";
    echo "FAILURE DETAILS:\n";
    echo str_repeat('=', 50) . "\n\n";

    foreach ($failures as $failure) {
        echo "Test: {$failure['name']}\n";
        echo str_repeat('-', 50) . "\n";

        if (isset($failure['error'])) {
            echo "Error: {$failure['error']}\n\n";
        } else {
            echo "Expected:\n{$failure['expected']}\n\n";
            echo "Actual:\n{$failure['actual']}\n\n";
        }
    }
}

// Exit with error code if any tests failed
exit($failed > 0 ? 1 : 0);

function normalizeWhitespace($content) {
    // Trim each line and remove empty lines
    $lines = array_filter(
        array_map('trim', explode("\n", $content)),
        fn($line) => $line !== ''
    );
    return implode("\n", $lines);
}
```

### Example Test Files

**Input: `tests/input/box.bfg.php`**
```php
<bfg-box
    title="Test Box"
    description="Test description"
    class="custom-class"
    data-id="123">
    <p>Test content</p>
</bfg-box>
```

**Expected: `tests/expected/box.php`**
```php
<div class="bfg-box custom-class" data-id="123">
    <div class="bfg-box__header">
        <h2><?php esc_html_e('Test Box', 'bring-fraktguiden-for-woocommerce'); ?></h2>
        <p><?php esc_html_e('Test description', 'bring-fraktguiden-for-woocommerce'); ?></p>
    </div>
    <div class="bfg-box__section">
        <p>Test content</p>
    </div>
</div>
```

### TDD Workflow

1. **Create component template** → `src/components/box.bfgc.php`
2. **Write test input** → `tests/input/box.bfg.php` (component usage)
3. **Write expected output** → `tests/expected/box.php` (what you expect to get)
4. **Run tests** → `php bin/test-compiler.php` (will fail initially)
5. **Implement/fix compiler** → Update `bin/compile-templates.php`
6. **Re-run tests** → Keep iterating until ✅ PASS

### Test Coverage Strategy

**Start Simple:**
- One baseline test per component
- Test typical usage with common attributes
- Verify `<t>`, `<slot/>`, and `:var` replacements work

**Expand Later:**
- Add edge case tests as bugs are discovered
- Test class passthrough
- Test multiple unmatched attributes
- Test nested components
- Test self-closing components
- Test conditional rendering with `<if>` and `<else>`
- Test missing optional attributes

**Naming Convention for Multiple Tests:**
```
tests/
  ├── input/
  │   ├── box.bfg.php              ← Basic test
  │   ├── box-with-class.bfg.php   ← Edge case: class passthrough
  │   └── box-no-slot.bfg.php      ← Edge case: no slot content
  └── expected/
      ├── box.php
      ├── box-with-class.php
      └── box-no-slot.php
```

### Validation

Tests validate:
- ✅ `<t>` elements are replaced with `<?php esc_html_e() ?>`
- ✅ `:var` attributes are replaced with raw values
- ✅ `<slot/>` is replaced with inner content
- ✅ `<if>` conditionals render correctly based on attribute presence
- ✅ `<else>` blocks render when condition is false
- ✅ Unmatched attributes pass through to root element
- ✅ `class` attributes are merged correctly
- ✅ Compiled output is valid PHP/HTML

Tests **do not** validate:
- ❌ Runtime behavior of compiled templates
- ❌ WordPress translation function execution
- ❌ CSS styling or visual appearance

---

## Benefits

1. ✅ **Simple syntax** - Only 4 replacement types to learn (`<t>`, `<slot/>`, `:var`, `<if>`)
2. ✅ **DOM-based** - Uses PHP 8.4's `Dom\HTMLDocument` for robust parsing
3. ✅ **Valid HTML** - Templates are valid HTML, work in any editor
4. ✅ **Translation-ready** - Automatic `esc_html_e()` wrapping via `<t>`
5. ✅ **No runtime overhead** - Compiled at build time
6. ✅ **Flexible** - Unmatched attributes automatically pass through
7. ✅ **Conditional rendering** - Clean `<if>`/`<else>` syntax for optional content
8. ✅ **Clear intent** - `<t>` = translate, `:var` = raw, `<slot/>` = content, `<if>` = conditional
9. ✅ **TDD-friendly** - Simple test structure with clear pass/fail

---

## Next Steps

1. Implement compiler script: `bin/compile-templates.php`
2. Create test runner: `bin/test-compiler.php`
3. Write initial tests for each component
4. Implement compiler using TDD approach
5. Add more component templates
6. Integrate into build process
7. Add validation and error handling
