//-- Dependencies
const webpack = require('webpack')
const { merge } = require('webpack-merge')
const OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin')
const TerserPlugin = require('terser-webpack-plugin')

//-- Configuration
const baseWebpackConfig = require('./webpack.base.conf')

module.exports = merge(baseWebpackConfig, {
    mode: 'production',
    devtool: false,
    plugins: [
        new webpack.optimize.OccurrenceOrderPlugin(),

        // Compress extracted CSS. We are using this plugin so that possible
        // duplicated CSS from different components can be deduped.
        new OptimizeCSSPlugin()
    ],
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                terserOptions: {
                    ecma: 2016
                }
            })
        ]
    }
})
