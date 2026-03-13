# Vite/Vue Migration Summary

## Original Problem
After migrating from Laravel Mix to Vite, needed to handle Vue runtime chunks that Vite automatically creates when multiple entry points share Vue dependencies.

## What We've Fixed ✅

### 1. Build Configuration
- **File:** `vite.config.js`
- **Changes:**
  - Configured predictable chunk names without hashes: `assets/js/shared/[name].js`
  - Explicit `manualChunks` for Vue runtime
  - Results in: `vue-runtime.js` and `_plugin-vue_export-helper.js` with stable names

### 2. Git Tracking & Build Cleanup
- **Files:** `.gitignore`, `package.json`
- **Changes:**
  - Excluded built assets from git tracking
  - Added `clean` script to remove old built files before building
  - Removed tracked built files from git with `git rm --cached`

### 3. SVN Publisher Integration
- **File:** `svn-publisher.php` (lines 95-119)
- **Changes:**
  - Added build step (`npm install && npm run production`) before copying to SVN
  - Verifies required build outputs exist before proceeding
  - Ensures SVN always gets fresh, clean builds

### 4. ES Module Loading
- **Files:** `classes/ResourceManagement/Scripts.php` (lines 21-28), `pro/BringFraktguidenPro.php` (lines 30-36)
- **Changes:**
  - Added `script_loader_tag` filter to add `type="module"` to Vite-built scripts
  - Fixed syntax errors: `Unexpected token 'export'` and `Cannot use import statement outside a module`
  - Scripts now load correctly as ES modules

### 5. Template Fixes
- **File:** `templates/service-field.php` (line 33)
- **Changes:**
  - Fixed closing tag typo: `</shippingproducts>` → `</shippingproduct>`

## What We Tried But Didn't Work ❌

### 1. Disable Chunking Completely
```js
// vite.config.js
manualChunks: undefined
```
**Result:** Vite still created chunks for Vue plugin helpers. The Vue plugin extracts runtime helpers regardless of manualChunks config.

### 2. Inline Dynamic Imports
```js
// vite.config.js
output: {
  inlineDynamicImports: true
}
```
**Error:** `Invalid value for option "output.inlineDynamicImports" - multiple inputs are not supported`

**Reason:** Rollup doesn't support this with multiple entry points.

### 3. Various manualChunks Functions
Tried returning `undefined` for all modules to prevent chunking - Vue plugin still extracted helpers automatically.

## What Remains 🔧

### Current Issue: Vue App Mounting But Rendering Empty

**Symptoms:**
- ✅ Vue app mounts successfully (confirmed by console logs)
- ✅ Data exists (`services_data`, `services_enabled`, etc.)
- ✅ Computed properties work (`services` array has 2 items)
- ❌ Template renders as empty: `<div id="shipping_services" data-v-app=""><!----></div>`

**Console Output Shows:**
```
Settings script loaded
Vue app mounted to #shipping_services
Computed services: Array(2)
```

**But DOM is empty** - the `<!---->` comment node suggests Vue is rendering but components aren't appearing.

## Debugging Steps

### 1. Check Browser Console
Look for Vue warnings:
```
[Vue warn] ...
```

### 2. Check for Component Registration Errors
Open browser console and inspect:
```js
// Check if components are registered
console.log(vm.$options.components)
```

### 3. Add Temporary Debug in Template
Edit `templates/service-field.php`:
```html
<div id="shipping_services">
    <p>Services count: {{ services.length }}</p>
    <pre>{{ services }}</pre>
    <!-- existing template -->
</div>
```

### 4. Check Component Props
Add logging to `resources/js/components/shipping-product.vue`:
```vue
<script>
export default {
  mounted() {
    console.log('ShippingProduct mounted', this.$props);
  }
}
</script>
```

### 5. Verify Child Components Load
Check if these files exist and are valid:
- `resources/js/components/override-toggle.vue`
- `resources/js/components/checkbox.vue`

### 6. Build in Development Mode
Run with source maps for better debugging:
```bash
npm run dev
```

## Relevant Files

**Configuration:**
- `vite.config.js` - Vite build config with chunk settings
- `package.json` - Build scripts with cleanup
- `.gitignore` - Excludes built assets

**PHP/WordPress:**
- `classes/ResourceManagement/Scripts.php` - Enqueues Vue runtime + settings script
- `pro/BringFraktguidenPro.php` - Enqueues Vue runtime + booking script
- `svn-publisher.php` - Builds before publishing
- `templates/service-field.php` - Vue template with shipping services

**JavaScript/Vue:**
- `resources/js/bring-fraktguiden-settings.js` - Vue app initialization
- `resources/js/components/shipping-product.vue` - Main component (not rendering)
- `resources/js/components/override-toggle.vue` - Child component
- `resources/js/components/checkbox.vue` - Child component

## Commands

```bash
# Build for production
npm run production

# Build for development (with source maps)
npm run dev

# Watch mode (rebuilds on changes)
npm run watch

# Clean + build
npm run build

# Check built files
ls -la assets/js/shared/
```

## Next Steps

1. **Check console for Vue warnings** - Most likely cause
2. **Verify child components** - `override-toggle.vue` and `checkbox.vue` might have errors
3. **Add debug output to template** - Confirm Vue data is accessible
4. **Check component lifecycle** - Add `mounted()` hooks to see if components instantiate
5. **Simplify template** - Try removing `<shippingproduct>` temporarily to see if `<select>` renders

## Technical Details

### Vite Output
```
./assets/css/compiled-styles.css                 11.09 kB │ gzip:  2.74 kB
./assets/js/shared/_plugin-vue_export-helper.js   0.09 kB │ gzip:  0.10 kB
./assets/js/bring-fraktguiden-checkout.js         1.24 kB │ gzip:  0.54 kB
./assets/js/bring-fraktguiden-settings.js         9.29 kB │ gzip:  2.80 kB
./pro/assets/js/booking.js                       26.78 kB │ gzip:  8.45 kB
./assets/js/shared/vue-runtime.js                62.15 kB │ gzip: 24.82 kB
```

### File Size Comparison
- **Before (Mix):** 675K for settings
- **After (Vite):** 70K total (9K + 62K chunk) = **90% reduction**

### Chunk Files Created
- `assets/js/shared/vue-runtime.js` - Main Vue runtime (62K)
- `assets/js/shared/_plugin-vue_export-helper.js` - Vue plugin helper (0.09K)

Both have predictable names without hashes for easy WordPress enqueuing.
