const mix = require('laravel-mix');
const purgeCss = require('@fullhuman/postcss-purgecss');
const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const source = 'platform/plugins/' + directory;
const dist = 'public/vendor/core/plugins/' + directory;

const filterCss = [];

if (mix.inProduction()) {
    filterCss.push(
        purgeCss({
            content: [
                source + "/resources/views/**/*.blade.php",
                source + "/resources/assets/js/components/*.vue",
                source + "/resources/assets/js/components/**/*.vue",
            ],
            defaultExtractor: content => content.match(/[\w-/.:]+(?<!:)/g) || [],
            safelist: [
                /active/,
                /show/,
                /down/,
                /loading/,
                /^bb-dialog/

            ]
        })
    )
}

mix
    .js(source + '/resources/assets/js/comment.js', dist + '/js')
    .sass(source + '/resources/assets/sass/comment.scss', dist + '/css', {}, filterCss)

    .js(source + '/resources/assets/js/comment-setting.js', dist + '/js')

    .copyDirectory(dist + '/js', source + '/public/js')
    .copyDirectory(dist + '/css', source + '/public/css');
