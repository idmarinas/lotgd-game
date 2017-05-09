var
	//-- Dependencias
	path = require('path'),
	utils = require('./utils'),

	//-- Configuración
	config = require('./default')
;

function resolve (dir) {
  return path.join(__dirname, '..', dir)
}

module.exports = {
	entry: {
		lotgd: './assets/index.js'
	},
    output: {
        path: config.paths.build,
        filename: '[name].js',
        chunkFilename: utils.assetsPath('js/[id].js')
    },
    resolve: {
        extensions: ['.js', '.json'],
        alias: {
            jquery: 'jquery/src/jquery.js'
        }
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                loader: 'eslint-loader',
                enforce: "pre",
                include: [path.resolve(__dirname, '../../assets')],
                options: {
                    formatter: require('eslint-friendly-formatter')
                }
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                include: [path.resolve(__dirname, '../../assets')]
            },
            {
                test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
                loader: 'url-loader',
                query: {
                    limit: 10000,
                    name: utils.assetsPath('img/[name].[hash:7].[ext]')
                }
            },
            {
                test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
                loader: 'url-loader',
                query: {
                    limit: 10000,
                    name: utils.assetsPath('fonts/[name].[hash:7].[ext]')
                }
            }
        ]
    }
}
