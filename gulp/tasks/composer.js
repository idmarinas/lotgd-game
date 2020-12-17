/**
 * Removes PHP dependencies that are only used in a development environment
 */

//-- Dependencies
const composer = require('gulp-composer')

//-- Configuration
const config = require('../config/default')
const configTasks = require('../config/tasks')

const isProduction = configTasks.isProduction()

module.exports = function (callback)
{
    if (isProduction)
    {
        return composer({
            'working-dir': config.paths.build.prod,
            'no-dev': true,
            'no-suggest': true
        })
    }
    else
    {
        return callback()
    }
}
