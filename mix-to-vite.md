Migration Complete! 🎉
Successfully migrated from Laravel Mix to Vite. Here's what changed:

✅ Completed Changes
1. Dependencies

Removed: Laravel Mix, resolve-url-loader, sass-loader, cross-env, @vue/compiler-sfc, vue-loader
Added: Vite 6.4.1, @vitejs/plugin-vue 5.2.4
Kept: Vue 3.2.31, Sass, @heroicons/vue, vue-select
2. Build Configuration

Created vite.config.js with multi-entry support
Updated npm scripts: dev, build, watch, production
Deleted webpack.mix.js
3. Code Updates

Fixed Vue component imports to include .vue extensions
Replaced lodash with native JavaScript (debounce, forEach, some)
Changed dynamic require() to static import statements
4. SVN Publisher

Updated svn-publisher.php:140 to remove:
vite.config.js
resources/ and pro/resources/ source directories
Fixed typo: package.lock → package-lock.json
📊 Build Results
File Sizes (Production):

Settings: 9.0K + 61K (chunk) = 70K total (was 675K with Mix!) ✨
Checkout: 1.2K
Booking: 26K
CSS: 11K (extracted to assets/css/compiled-styles.css)
Benefits:

90% smaller bundles due to better tree-shaking
Faster build times
Modern build tooling
⚠️ Action Required: Chunk File
Vite created a shared chunk file for Vue runtime:

assets/js/_plugin-vue_export-helper-[hash].js (61K)
This file needs to be enqueued in WordPress before the settings and booking scripts. You'll need to update:

classes/ResourceManagement/Scripts.php:29 - Add Vue chunk before settings script
pro/BringFraktguidenPro.php:38 - Add Vue chunk before booking script
Example enqueue:


wp_enqueue_script(
    'bring-vue-runtime',
    plugins_url('assets/js/_plugin-vue_export-helper-*.js', dirname(__DIR__)),
    [],
    Bring_Fraktguiden::VERSION,
    true
);
Alternatively, we could build each entry separately to avoid chunk files entirely. Let me know if you'd prefer that approach!