module.exports = function (Encore)
{
    Encore
        //-- This is the default theme
        .addEntry('lotgd_theme', './assets/lotgd/lotgd.less') //-- If not want generate this theme, comment/eliminate this line

    //-- Here add your custom entries (pages js/css)
    // .addEntry('example', './assets/example/path/to/file.js')

    const LotgdConfig = Encore.getWebpackConfig()
    LotgdConfig.name = 'lotgd'

    Encore.reset() //-- Reset config for your custom THEME

    // This is necesary
    if (!Encore.isRuntimeEnvironmentConfigured())
    {
        Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
    }

    //-- Use this for your theme only
    Encore
        //-- Set output and public path inside build/ dir
        .setOutputPath('public/build/example/')
        .setPublicPath('/build/example')

        //-- Recomended how export CSS/JS/Images/Fonts files (Same structure as core)
        .configureFilenames({
            js: 'js/[name].[contenthash].js',
            css: 'css/[name].[contenthash].css',
            images: 'images/[name].[hash:8].[ext]',
            fonts: 'fonts/[name].[hash:8].[ext]'
        })

        /**
         * Configure your THEME
         * Only use for your THEME.
         *
         * DIR_NAME: Is name of your custom assets inside "./assets/" folder
         */
        //-- Configure your THEME
        .addEntry('KEY_NAME_FOR_THEME', './assets/DIR_NAME/lotgd.less')
        .addAliases({ //-- Alias for your "theme.config" file
            '../../theme.config$': require('path').join(
                __dirname,
                './assets/DIR_NAME/theme.config'
            )
        })

        //-- FEATURE CONFIG
        .splitEntryChunks()
        .enableSingleRuntimeChunk()
        .cleanupOutputBeforeBuild()
        .enableBuildNotifications()
        .enableSourceMaps(!Encore.isProduction())
        .enableVersioning(Encore.isProduction())

        .configureBabelPresetEnv(config =>
        {
            config.useBuiltIns = 'usage'
            config.corejs = 3
        })

        .configureUrlLoader({
            images: { limit: 4096 }
        })

        // .enableSassLoader() //-- Optional
        .enableLessLoader() //-- This is required for Theme
        .enablePostCssLoader()

        .enableIntegrityHashes(Encore.isProduction())
        // .autoProvidejQuery()

    const CustomTheme = Encore.getWebpackConfig()
    CustomTheme.name = 'custom_theme'

    Encore.reset()

    return [LotgdConfig] //-- Add here config for your theme
}
