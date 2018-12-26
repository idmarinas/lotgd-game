/**
 * Create lotgd.js
 */
//-- Dependencies
var webpack = require('webpack')
var ora = require('ora')

//-- Configuration
var configTasks = require('../../config/tasks')
var isProduction = configTasks.isProduction()

var webpackConfig = require('../../config/webpack.dev.conf')
var spinner = ora('building LOTGD JS App for development...')

if (isProduction)
{
    webpackConfig = require('../../config/webpack.prod.conf')
    spinner = ora('building LOTGD JS App for production...')
}

module.exports = function (callback)
{
    spinner.start()
    return webpack(webpackConfig, function (err, stats)
    {
        spinner.stop()
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
