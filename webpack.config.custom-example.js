/**
 * If you want that Encore use this for your custom config delete sufix -example from name
 * Them need add your own code this is only a example
 *
 * THIS IS THE RECOMENDED CONFIG FOR ENCORE
 *
 * If you have many Encore config can explit in diferents files them import and export and array with all configs
 *
 */
var Encore = require('@symfony/webpack-encore')

// This is necesary
if (!Encore.isRuntimeEnvironmentConfigured())
{
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore
    .setOutputPath('public/build/example/')
    .setPublicPath('/build/example')

    .configureFilenames({
        js: 'js/[name].[contenthash].js',
        css: 'css/[name].[contenthash].css',
        images: 'images/[name].[hash:8].[ext]',
        fonts: 'fonts/[name].[hash:8].[ext]'
    })

    /*
     * ENTRY CONFIG
     */
    //-- This is the global entry used in all pages
    .addEntry('example', './paht/to/file')

    /*
     * FEATURE CONFIG
     */
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) =>
    {
        config.useBuiltIns = 'usage'
        config.corejs = 3
    })

    .configureUrlLoader({
        images: { limit: 4096 }
    })

    .enableSassLoader()
    .enableLessLoader()
    .enablePostCssLoader()

    .enableIntegrityHashes(Encore.isProduction())

    .autoProvidejQuery()

const ExampleConfig = Encore.getWebpackConfig()
ExampleConfig.name = 'example'

Encore.reset() //-- Always call this, or Encore mixed all configs

// This file always return an array
module.exports = [ExampleConfig]
