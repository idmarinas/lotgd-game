const Encore = require('@symfony/webpack-encore')

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured())
{
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/lotgd/')
    // public path used by the web server to access the output path
    .setPublicPath('/build/lotgd')

    //-- Configure how CSS/JS/Images/Fonts files are exported
    .configureFilenames({
        js: 'js/[name].[contenthash].js',
        css: 'css/[name].[contenthash].css',
        images: 'images/[name].[hash:8].[ext]',
        fonts: 'fonts/[name].[hash:8].[ext]'
    })

    // Copy files of images
    .copyFiles({
        from: './assets/lotgd/css/core/assets/images/',
        // optional target path, relative to the output dir
        // if versioning is enabled, add the file hash too
        to: 'images/[path][name].[hash:8].[ext]'
    })

    //-- Add alias for some files
    .addAliases({
        'sweetalert2.css$': 'sweetalert2/src/sweetalert2.scss',
        'tagify.scss$': '@yaireo/tagify/src/tagify.scss',
        '../../theme.config$': require('path').join(
            __dirname,
            './assets/lotgd/theme.config'
        )
    })

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    //-- This is the global entry used in all pages
    .addEntry('lotgd', './assets/lotgd/lib/index.js')
    .addEntry('lotgd_theme', './assets/lotgd/lotgd.less')//-- Default theme
    .addEntry('cookie_guard', './assets/lotgd/js/cookie/index.js')
    .addEntry('semantic_ui', './node_modules/fomantic-ui/dist/semantic.js')

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv(config =>
    {
        config.useBuiltIns = 'usage'
        config.corejs = 3
    })

    // Image files with a weight <= 4kb are processed as base64 in the CSS file
    .configureUrlLoader({
        images: { limit: 4096 }
    })

    // enables Sass/SCSS support
    .enableSassLoader()
    // enables Less support
    .enableLessLoader()
    // autoprefixer
    .enablePostCssLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

const LotgdConfig = Encore.getWebpackConfig()
LotgdConfig.name = 'lotgd' //-- This allow use "encore dev --config-name lotgd" to only build this

Encore.reset() //-- Always call this, or Encore mixed all configs

//-- Import custom Encore config
let configCustom = []
try
{
    configCustom = require('./webpack.config.custom')
}
catch (error)
{
    console.log('Not find custom Encore config')
}

module.exports = [LotgdConfig, ...configCustom]
