//-- Dependencies
var webpack = require('webpack')
var merge = require('webpack-merge')
var FriendlyErrors = require('friendly-errors-webpack-plugin')

//-- Configuration
var baseWebpackConfig = require('./webpack.base.conf')

module.exports = merge(baseWebpackConfig, {
    mode: 'development',
    // cheap-module-eval-source-map is faster for development
    devtool: '#cheap-module-eval-source-map',
    plugins: [
        new webpack.optimize.OccurrenceOrderPlugin(),
        new webpack.HotModuleReplacementPlugin(),
        new webpack.NoEmitOnErrorsPlugin(),

        // https://github.com/ampedandwired/html-webpack-plugin
        new FriendlyErrors()
    ]
})
