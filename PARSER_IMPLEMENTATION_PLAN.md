# Custom Template Parser Implementation Plan

## Problem Statement

The `Dom\HTMLDocument` parser is too strict for our component system—it removes custom tags like `<slot>` from inside `<select>` elements due to HTML5 validation rules. We need a custom parser that preserves our template structure.

## Requirements

The parser must handle:
- Standard HTML tags with attributes: `<tag attr="value" boolean-attr>`
- Self-closing tags: `<tag/>`
- Preserve custom tags anywhere in the tree: `<slot>`, `<if>`, `<else>`
- Handle dynamic attributes: `:attr="php_expression"`
- Preserve PHP tags as-is: `<?php ... ?>`
- Build a DOM-like tree structure for traversal and manipulation

**Important**: The parser should ONLY tokenize and build the tree structure—it should NOT evaluate PHP expressions or resolve dynamic attributes. That's handled by the existing compiler processors.

## Output Structure

The parser should produce a simple node tree with:
- **Element nodes**: tag name, attributes, children
- **Text nodes**: raw text content
- **PHP nodes**: raw PHP code (preserved as-is)

We can keep the existing processor architecture—just swap out `Dom\HTMLDocument` with our custom parser.

---

## Implementation Considerations

### 1. Core Parsing Strategy

**Decision needed**: Regex-based (simpler, may have edge cases) vs character-by-character (more control, verbose)

**Question**: Do we need to handle malformed HTML gracefully or can we assume valid input from our templates?

### 2. Token Types to Handle

```php
- Opening tags: <div>, <bfg-button>
- Closing tags: </div>
- Self-closing: <input/>, <img/>
- Attributes: name="value", :dynamic="expr", boolean-attr
- Text content: Everything between tags
- PHP blocks: <?php ... ?>
- HTML comments: <!-- ... --> (preserve or strip?)
```

### 3. Attribute Parsing Edge Cases

```php
- Quoted values: attr="value" attr='value'
- Unquoted: attr=value (do we support this?)
- Boolean: disabled checked
- Special chars in values: attr="it's \"quoted\""
- Dynamic prefix: :attr="1 + 1"
- Whitespace: attr = "value" vs attr="value"
```

### 4. Nesting & Tree Building

- How to track open/close tag matching
- How to handle orphaned closing tags: `</div>` with no opening
- Void elements that don't need closing: `<br>`, `<input>`

### 5. Whitespace Handling

```html
<div>
  <span>text</span>
</div>
```

**Decision needed**: Do we preserve the newlines/indentation or normalize whitespace?

### 6. PHP Tag Preservation

```php
<div><?php echo $var; ?></div>
<div attr="<?php echo $val; ?>">
```

PHP can appear in:
- Text content
- Attribute values
- Standalone between tags

### 7. Node Structure

Proposed interface:

```php
interface BFG_Node {
    public function nodeType(): string; // 'element', 'text', 'php'
}

class BFG_ElementNode implements BFG_Node {
    public string $tagName;
    public array $attributes; // ['name' => 'value']
    public array $children; // BFG_Node[]
    public bool $selfClosing;
    public ?BFG_ElementNode $parentNode;
}

class BFG_TextNode implements BFG_Node {
    public string $content;
    public ?BFG_ElementNode $parentNode;
}

class BFG_PhpNode implements BFG_Node {
    public string $code; // Raw PHP including <?php ?>
    public ?BFG_ElementNode $parentNode;
}

class BFG_Document {
    public array $children; // Root-level nodes
    // ... traversal and manipulation methods
}
```

### 8. API Design

The parser should provide a similar interface to `Dom\HTMLDocument`:

```php
// Parsing
$parser = new BFG_TemplateParser();
$doc = $parser->parse($htmlString);

// Traversal
foreach ($doc->children as $node) { }
$nodes = $doc->getElementsByTagName('slot');
$nodes = $doc->getElementsByTagName('*'); // All elements

// Manipulation
$element->setAttribute('class', 'value');
$element->getAttribute('class');
$element->removeAttribute('class');
$parent->appendChild($newNode);
$parent->insertBefore($newNode, $refNode);
$parent->removeChild($oldNode);
$parent->replaceChild($newNode, $oldNode);

// Serialization
$html = $doc->toHTML(); // Or saveHTML()
$html = $doc->saveHTML($node); // Save specific node
```

### 9. Performance Considerations

- We parse multiple files on every compile
- Should we cache parsed trees?
- Lazy vs eager parsing?
- Memory footprint for large templates?

### 10. Testing Strategy

Required test cases:

```php
// Basic structure
- Nested tags: <div><span><slot/></span></div>
- Siblings: <div></div><span></span>
- Self-closing: <slot/> vs <slot></slot>

// Attributes
- Multiple: <div class="a" id="b" disabled>
- Dynamic: <div :id="$var" :class="'test'">
- Mixed: <div class="static" :id="$dynamic" disabled>
- Quotes: <div title='single' alt="double">

// PHP preservation
- In text: <div><?php echo "hi"; ?></div>
- In attributes: <div class="<?php echo $class; ?>">
- Standalone: <?php if ($x) { ?><div></div><?php } ?>

// Edge cases
- Custom tags in select: <select><slot/></select>
- Mixed content: text <tag/> more text
- Empty tags: <div></div> vs <div/>
- Comments: <!-- comment --> (preserve or strip?)
- Whitespace: preserve indentation?

// Error handling
- Unclosed tags: <div><span></div>
- Orphaned closing: </div>
- Invalid syntax: <div class=">
```

## Recommended Implementation Approach

Start minimal and iterate:

1. **Phase 1: Tokenizer**
   - Regex-based tag/text splitter
   - Identify: opening tags, closing tags, self-closing, text, PHP blocks
   - Extract tag names and raw attribute strings

2. **Phase 2: Attribute Parser**
   - Parse attribute strings into name-value pairs
   - Handle quoted/unquoted values
   - Identify dynamic attributes (`:` prefix)
   - Support boolean attributes

3. **Phase 3: Tree Builder**
   - Stack-based tag matching
   - Build parent-child relationships
   - Handle self-closing tags
   - Create ElementNode, TextNode, PhpNode objects

4. **Phase 4: Simple API**
   - Implement only what the compiler needs:
     - `getElementsByTagName()`
     - `setAttribute()` / `getAttribute()`
     - `appendChild()` / `insertBefore()` / `removeChild()`
     - `saveHTML()`

5. **Phase 5: Integration**
   - Replace `Dom\HTMLDocument::createFromString()` calls
   - Update compiler to use new API
   - Run existing tests to verify compatibility

6. **Phase 6: Iterate**
   - Add features as needed
   - Optimize performance bottlenecks
   - Handle edge cases discovered in testing

## Integration Points

Files that need updating:

- `bin/compile-templates.php` (lines 69, 153)
  - Replace `Dom\HTMLDocument::createFromString()` with `BFG_TemplateParser::parse()`
  - Update `$doc->body` references
  - Update `$doc->saveHTML()` calls

- Processor files (if they use DOM-specific APIs)
  - `src/Compiler/Processors/*.php`
  - May need to update to use our custom node interface

## Success Criteria

- Parser preserves `<slot>` and other custom tags inside `<select>` and other restricted elements
- All existing tests pass with the new parser
- No performance regression (compile time within 10% of current)
- Maintainable code with clear separation of concerns

## Open Questions

1. Should we handle malformed HTML gracefully or assume valid input?
2. How should we handle whitespace (preserve vs normalize)?
3. Should HTML comments be preserved or stripped?
4. Do we need to support unquoted attribute values?
5. What error reporting/debugging should the parser provide?
