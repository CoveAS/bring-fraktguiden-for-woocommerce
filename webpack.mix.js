const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.setPublicPath('./');
mix.js('resources/js/bring-fraktguiden-settings.js', 'assets/js')
	.vue({ version: 3 })
	.js('resources/js/bring-fraktguiden-checkout.js', 'assets/js')
	.js('pro/resources/js/booking.js', 'pro/assets/js')
    .sass('resources/sass/pro/admin.scss', 'pro/assets/css')
    .sass('resources/sass/bring-fraktguiden.scss', 'assets/css')
    .sass('resources/sass/bring-fraktguiden-admin.scss', 'assets/css');
