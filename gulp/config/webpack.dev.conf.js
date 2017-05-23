var
	//-- Dependencias
	webpack = require('webpack'),
	path = require('path'),
	merge = require('webpack-merge'),
	ExtractTextPlugin = require('extract-text-webpack-plugin'),
	FriendlyErrors = require('friendly-errors-webpack-plugin'),

	//-- Configuración
	utils = require('./utils'),
	baseWebpackConfig = require('./webpack.base.conf')
;

module.exports = merge(baseWebpackConfig, {
	module: {
		rules: utils.styleLoaders({ sourceMap: false, extract: true })
	},
	// cheap-module-eval-source-map is faster for development
 	devtool: '#cheap-module-eval-source-map',
	output: {
		filename: utils.assetsPath('js/[name].js'),
		chunkFilename: utils.assetsPath('js/[id].js')
	},
	plugins: [
		new webpack.DefinePlugin({
			'process.env': { NODE_ENV: '"development"' }
		}),
		// new webpack.optimize.OccurrenceOrderPlugin(),
		new webpack.HotModuleReplacementPlugin(),
		new webpack.NoEmitOnErrorsPlugin(),
		// extract css into its own file
		new ExtractTextPlugin(utils.assetsPath('css/[name].css')),

		// https://github.com/ampedandwired/html-webpack-plugin
		new FriendlyErrors()
	]
})
