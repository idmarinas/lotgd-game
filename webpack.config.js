const Encore = require('@symfony/webpack-encore')

//-- Import base configuration
const LotgdEncore = require('./webpack.encore.config')(Encore)

LotgdEncore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/lotgd/')
    // public path used by the web server to access the output path
    .setPublicPath('/build/lotgd')
    //-- Add alias for theme
    .addAliases({
        '../../theme.config$': require('path').join(
            __dirname,
            './assets/lotgd/theme.config'
        )
    })
    // Copy files of images
    .copyFiles({
        from: './assets/lotgd/css/core/assets/images/',
        // optional target path, relative to the output dir
        // if versioning is enabled, add the file hash too
        to: 'images/[path][name].[hash:8].[ext]'
    })

    /*
    * Each entry will result in one JavaScript file (e.g. app.js)
    * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
    */
    //-- This is the global entry used in all pages
    .addEntry('lotgd', './assets/lotgd/lib/index.js')
    //-- This is the default theme
    .addEntry('lotgd_theme', './assets/styles/lotgd.css') //-- If not want generate this theme, comment/eliminate this line
    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    // .enableStimulusBridge('./assets/controllers.json')

    // enables Sass/SCSS support
    // .enableSassLoader()
    // enables Less support
    // .enableLessLoader()

const LotgdConfig = LotgdEncore.getWebpackConfig()
LotgdConfig.name = 'lotgd'

/**
 * Custom themes/configs/entries
 */

/*
const CustomEncore = require('./webpack.encore.config')(Encore)

CustomEncore
    .setOutputPath('public/build/DIR_NAME/')
    .setPublicPath('/build/DIR_NAME')
    .addEntry('KEY_NAME_FOR_THEME', './assets/DIR_NAME/lotgd.less')
    .addAliases({ //-- Alias for your "theme.config" file
        '../../theme.config$': require('path').join(
            __dirname,
            './assets/DIR_NAME/theme.config'
        )
    })
    // Other custom entries (pages js/css)
    .addEntry('example', './assets/DIR_NAME/path/to/file.js')

const CustomConfig = LotgdEncore.getWebpackConfig()
CustomConfig.name = 'custom'
*/
module.exports = [LotgdConfig]
