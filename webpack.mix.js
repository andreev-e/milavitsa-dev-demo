const mix = require('laravel-mix');
const path = require('path');

require('dotenv').config({path: path.join(__dirname, '.env')});
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

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .alias({
       '@': path.join(__dirname, 'resources/js')
    })
    .sass('resources/sass/app.scss', 'public/css');
