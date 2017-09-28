//-- Dependencias
var webpack = require('webpack')
var path = require('path')
var utils = require('./utils')

//-- Configuración
var config = require('./default')

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
            jquery: 'jquery/src/jquery.js',
            'sweetalert2.css$': 'sweetalert2/src/sweetalert2.scss',
            'toastr.css$': 'toastr/toastr.scss'
        }
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                loader: 'eslint-loader',
                enforce: 'pre',
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
    },
    plugins: [
        new webpack.optimize.CommonsChunkPlugin({
            name: 'vendor',
            minChunks: function (module)
            {
                // this assumes your vendor imports exist in the node_modules directory
                return module.context && module.context.indexOf('node_modules') !== -1
            }
        }),
        // CommonChunksPlugin will now extract all the common modules from vendor and main bundles
        new webpack.optimize.CommonsChunkPlugin({
            name: 'manifest' // But since there are no more common modules between them we end up with just the runtime code included in the manifest file
        })
    ]
}
