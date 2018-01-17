/**
 * Removes PHP dependencies that are only used in a development environment
 */

//-- Dependencies
var composer = require('gulp-composer')

//-- Configuration
var config = require('../config/default')
var configTasks = require('../config/tasks')

var isProduction = configTasks.isProduction()

module.exports = function (callback)
{
    if (isProduction)
    {
        return composer({
            'working-dir': config.paths.build,
            'no-dev': true,
            'no-suggest': true,
            'optimize-autoloader': true
        })
    }
    else
    {
        return callback()
    }
}
