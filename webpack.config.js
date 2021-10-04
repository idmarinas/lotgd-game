const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured())
{
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    .configureFilenames({
        js: 'js/[name].[contenthash].js',
        css: 'css/[name].[contenthash].css'
    })
    .configureImageRule({
        type: 'asset',
        maxSize: 4 * 1024,
        filename: 'images/[name].[hash:8].[ext]'
    })
    .configureFontRule({
        filename: 'fonts/[name].[hash:8].[ext]'
    })

    //-- Useful alias for some files
    .addAliases({
        'sweetalert2.css$': 'sweetalert2/dist/sweetalert2.min.css',
        'tagify.scss$': '@yaireo/tagify/dist/tagify.css'
    })

    //-- Copy files of images
    .copyFiles({
        from: './assets/images/',
        // optional target path, relative to the output dir
        // if versioning is enabled, add the file hash too
        to: 'images/[path][name].[hash:8].[ext]'
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    //-- This is the global entry used in all pages
    .addEntry('app', './assets/lotgd/lib/index.js')

    //-- For custom theme creation, use tailwind.config.js to change style
    .addEntry('app_theme', './assets/styles/app.css') //-- This is the default theme

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')


    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // autoprefixer
    .enablePostCssLoader()

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
    ;

module.exports = Encore.getWebpackConfig();
