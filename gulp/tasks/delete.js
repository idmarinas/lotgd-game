/**
 * Delete conten of dist folder
 */

//-- Dependencies
var del = require('del')

//-- Configuration
var config = require('../config/default')
var configTasks = require('../config/tasks')

//-- Delete distribution files
module.exports = function (callback)
{
    return del([config.paths.build], configTasks.settings.del, callback)
}
