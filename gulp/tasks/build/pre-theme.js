var
	//-- Dependencies
	gulp = require('gulp'),
	replace = require('gulp-replace'),
	rename = require('gulp-rename'),
	print = require('gulp-print'),
	plumber = require('gulp-plumber'),
	del = require('del'),

	//-- Configuration
	config = require('../../config/default'),
	configTasks = require('../../config/tasks'),

	log = configTasks.log,
	isProduction = configTasks.isProduction(),
	themeName = configTasks.theme()
;


module.exports = function(callback)
{
	del('semantic/src/theme.config');

	return gulp.src('semantic/src/theme.config.default')
		.pipe(replace('default', themeName))
		.pipe(plumber())
		.pipe(rename('theme.config'))
		.pipe(gulp.dest('semantic/src'))
		.pipe(print(log.copied))
	;
}