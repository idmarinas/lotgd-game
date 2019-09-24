/**
 * Create lotgd.js
 */
//-- Dependencies
var webpack = require('webpack')

//-- Configuration
var configTasks = require('../../config/tasks')
var isProduction = configTasks.isProduction()

var webpackConfig = require('../../config/webpack.dev.conf')

if (isProduction)
{
    webpackConfig = require('../../config/webpack.prod.conf')
}

module.exports = function (callback)
{
    return webpack(webpackConfig, function (err, stats)
    {
        if (err) throw err
        process.stdout.write(stats.toString({
            colors: true,
            modules: false,
            children: false,
            chunks: false,
            chunkModules: false
        }) + '\n')

        callback()
    })
}
