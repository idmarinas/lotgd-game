/**
 * Concat lotgd JS files
 */
var
	//-- Dependencies
	gulp = require('gulp'),
	concat = require('gulp-concat'),
	print = require('gulp-print'),
	gulpif = require('gulp-if'),
	uglify = require('gulp-uglify'),
	plumber = require('gulp-plumber'),
	header = require('gulp-header'),

	//-- Configuration
	config = require('../../config/default'),
	configTasks = require('../../config/tasks'),

	log = configTasks.log,
	isProduction = configTasks.isProduction(),
	settings = configTasks.settings,
	banner = configTasks.banner.js
;

module.exports = function(callback)
{
	return gulp.src([
			'assets/lotgd.js',
			'assets/components/*.js'
		])
		.pipe(plumber())
        .pipe(concat('lotgd.js'))
		.pipe(gulpif(isProduction, uglify(configTasks.settings.uglify.noComments)))
		.pipe(gulpif(isProduction, header(banner, settings.header)))
        .pipe(gulp.dest(config.paths.build + '/resources'))
		.pipe(print(log.created))
	;
}