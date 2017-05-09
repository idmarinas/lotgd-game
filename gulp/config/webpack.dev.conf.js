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

		// split vendor js into its own file
		new webpack.optimize.CommonsChunkPlugin({
			name: 'vendor',
			minChunks: function (module, count) {
				// any required modules inside node_modules are extracted to vendor
				return (
					module.resource &&
					/\.js$/.test(module.resource) &&
					module.resource.indexOf(
						path.join(__dirname, '../../node_modules')
					) === 0
				)
			}
		}),
		// extract webpack runtime and module manifest to its own file in order to
		// prevent vendor hash from being updated whenever app bundle is updated
		new webpack.optimize.CommonsChunkPlugin({
			name: 'manifest',
			chunks: ['vendor']
		}),
		// https://github.com/ampedandwired/html-webpack-plugin
		new FriendlyErrors()
	]
})
