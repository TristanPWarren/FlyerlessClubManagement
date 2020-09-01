const mix = require('laravel-mix');

mix.setPublicPath('./public');

mix.js('resources/js/module.js', 'public/modules/flyerless-club-management/js')
    .sass('resources/sass/module.scss', 'public/modules/flyerless-club-management/css');
