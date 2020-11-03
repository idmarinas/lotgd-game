/**
 * Delete conten of dist folder
 */

//-- Dependencies
const del = require('del')

//-- Configuration
const config = require('../config/default')
const configTasks = require('../config/tasks')
const isProduction = configTasks.isProduction()

//-- Delete distribution files
module.exports = function (callback)
{
    const destFolder = isProduction ? config.paths.build.prod : config.paths.build.dev

    return del([destFolder], configTasks.settings.del, callback)
}
