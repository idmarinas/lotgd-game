var
	//-- Dependencies
	gulp = require('gulp'),
	print = require('gulp-print'),
    vinylPaths = require('vinyl-paths'),
    del = require('del'),

	//-- Configuration
	config = require('../../config/default'),
	configTasks = require('../../config/tasks'),

	log = configTasks.log,
	isProduction = configTasks.isProduction(),
	themeName = configTasks.theme()

module.exports = function(callback)
{
    //-- Copy and delete themeName.html in correct folder
    return gulp.src(config.paths.build + '/themes/' + themeName + '/' + themeName + '.html')
        // .pipe(print(log.deleted))
        .pipe(vinylPaths(del))
        .pipe(print(log.deleted))
        .pipe(gulp.dest(config.paths.build + '/themes'))
        .pipe(print(log.copied))
}
