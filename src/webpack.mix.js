const mix = require('laravel-mix');

const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');

    var webpackConfig = {
        plugins: [
            new VuetifyLoaderPlugin()
        ]
    }
    mix.webpackConfig(webpackConfig);

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps()
    .browserSync({
        host: 'http://127.0.0.1:8000',
        open: false,
        injectChanges: true,
        logSnippet: true,
        watchOptions: {
            usePolling: true,
            interval: 500,
            poll: true,
            ignored: /node_modules/
        },
        files: [
            'app/**/*.php',
            'resources/views/**/*.php',
            'resources/js/app.js',
            'resources/js/components/*.vue',
            'packages/mixdinternet/frontend/src/**/*.php',
            'public/js/**/*.js',
            'public/css/**/*.css'
        ]
    })
