const mix = require('laravel-mix');

mix.setPublicPath('./public');

mix.js('resources/js/module.js', 'public/modules/template/js')
    .sass('resources/sass/module.scss', 'public/modules/template/css');
