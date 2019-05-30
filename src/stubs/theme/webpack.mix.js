const cssImport = require('postcss-import')
const cssNesting = require('postcss-nesting')
const mix = require('laravel-mix')
require('laravel-mix-purgecss');
const path = require('path')
const tailwindcss = require('tailwindcss')

mix.postCss('resources/css/main.css', 'public/css', [
  cssImport(),
  cssNesting(),
  require('tailwindcss'),
])
.js('resources/js/app.js', 'public/js').webpackConfig({
    output: { chunkFilename: 'js/[name].[contenthash].js' },
    resolve: {
      alias: {
        'vue$': 'vue/dist/vue.runtime.js',
        '@': path.resolve('resources/js'),
      },
    },
  })
  .purgeCss()
  .version()
  .sourceMaps()
  .browserSync('laravel-preset-test.test')