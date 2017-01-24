/*******************************
			       Delete task
*******************************/

var
	//-- Dependencies
	del = require('del'),

	//-- Configuration
	config = require('../config/default'),
	configTasks  = require('../config/tasks')
;

//-- Delete distribution files
module.exports = function(callback)
{
	return del([config.paths.build], configTasks.settings.del, callback);
};