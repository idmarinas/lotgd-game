var
	//-- Dependencias
	webpack = require('webpack'),
	path = require('path'),
	merge = require('webpack-merge'),
	ExtractTextPlugin = require('extract-text-webpack-plugin'),
	OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin'),

	//-- Configuración
	utils = require('./utils'),
	baseWebpackConfig = require('./webpack.base.conf')
;

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
		new OptimizeCSSPlugin(),

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
		})
	]
});
