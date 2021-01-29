module.exports = function (Encore)
{
    //-- Reset config in each call
    Encore.reset()

    // Manually configure the runtime environment if not already configured yet by the "encore" command.
    // It's useful when you use tools that rely on webpack.config.js file.
    if (!Encore.isRuntimeEnvironmentConfigured())
    {
        Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
    }

    Encore
        //-- Configure how CSS/JS/Images/Fonts files are exported
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
            'sweetalert2.css$': 'sweetalert2/src/sweetalert2.scss',
            'tagify.scss$': '@yaireo/tagify/src/tagify.scss'
        })

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

        .configureBabel(config =>
        {
            config.plugins.push('@babel/plugin-proposal-class-properties')
        })

        // enables @babel/preset-env polyfills
        .configureBabelPresetEnv(config =>
        {
            config.useBuiltIns = 'usage'
            config.corejs = 3
        })

        // autoprefixer
        .enablePostCssLoader()

        // uncomment to get integrity="..." attributes on your script & link tags
        // requires WebpackEncoreBundle 1.4 or higher
        .enableIntegrityHashes(Encore.isProduction())

        // uncomment if you're having problems with a jQuery plugin
        .autoProvidejQuery()

    return Encore
}
