var
	//-- Dependencies
	gulp = require('gulp'),
	del = require('del'),

	//-- Configuration
	config = require('../config/default'),
	configTasks = require('../config/tasks'),

	themeName = configTasks.theme()
;


module.exports = function(callback)
{
	//-- Move html of them to correct directory
	var assetsMove = gulp.src(config.paths.build + '/themes/' + themeName + '/' + themeName + '.html')
		.pipe(gulp.dest(config.paths.build + '/themes'))
	;
	del(config.paths.build + '/themes/' + themeName + '/' + themeName + '.html');

	return callback;
}