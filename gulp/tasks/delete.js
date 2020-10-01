/**
 * Delete conten of dist folder
 */

//-- Dependencies
var del = require('del')

//-- Configuration
var config = require('../config/default')
var configTasks = require('../config/tasks')
var isProduction = configTasks.isProduction()

//-- Delete distribution files
module.exports = function (callback)
{
    const destFolder = isProduction ? config.paths.build.prod : config.paths.build.dev

    return del([destFolder], configTasks.settings.del, callback)
}
