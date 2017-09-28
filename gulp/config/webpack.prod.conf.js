//-- Dependencias
var webpack = require('webpack')
var merge = require('webpack-merge')
var ExtractTextPlugin = require('extract-text-webpack-plugin')
var OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin')

//-- Configuración
var utils = require('./utils')
var baseWebpackConfig = require('./webpack.base.conf')

module.exports = merge(baseWebpackConfig, {
    module: {
        rules: utils.styleLoaders({ sourceMap: false, extract: true })
    },
    devtool: false,
    output: {
        filename: utils.assetsPath('js/[name].js'),
        chunkFilename: utils.assetsPath('js/[id].js')
    },
    plugins: [
        // http://vuejs.github.io/vue-loader/en/workflow/production.html
        new webpack.DefinePlugin({
            'process.env': { NODE_ENV: '"production"' }
        }),
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false
            }
        }),
        new webpack.optimize.OccurrenceOrderPlugin(),
        // extract css into its own file
        new ExtractTextPlugin(utils.assetsPath('css/[name].css')),

        // Compress extracted CSS. We are using this plugin so that possible
        // duplicated CSS from different components can be deduped.
        new OptimizeCSSPlugin()
    ]
})
