var
	//-- Dependencies
	gulp = require('gulp'),
	merge = require('merge-stream'),
	rename = require('gulp-rename'),
	print = require('gulp-print'),
	gulpif = require('gulp-if'),
	uglify = require('gulp-uglify'),

	//-- Configuration
	config = require('../../config/default'),
	configTasks = require('../../config/tasks'),


	log = configTasks.log,
	isProduction = configTasks.isProduction(),
	settings = configTasks.settings
;

module.exports = function(callback)
{
	/************************/
	/** Copy files JS **/
	/************************/

	//-- Resources folder - Only JS
	var resourceJS = gulp.src('resources/**/*.js')
		.pipe(gulpif(isProduction, uglify(settings.uglify.some)))
		.pipe(gulp.dest(config.paths.build + '/resources'))
		.pipe(print(log.copied))
	;

	var resourceOther = gulp.src([
		'resources/**/**',
		'!resources/**/*.js'
	])
		.pipe(gulp.dest(config.paths.build + '/resources'))
	;

	return merge(resourceJS, resourceOther);
};
