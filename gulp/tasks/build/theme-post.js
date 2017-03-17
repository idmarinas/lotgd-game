var
	//-- Dependencies
	gulp = require('gulp'),
	merge = require('merge-stream'),
	replace = require('gulp-replace'),
	rename = require('gulp-rename'),
	print = require('gulp-print'),
	gulpif = require('gulp-if'),
    header = require('gulp-header'),

	//-- Configuration
	config = require('../../config/default'),
	configTasks = require('../../config/tasks'),

	log = configTasks.log,
	isProduction = configTasks.isProduction(),
	themeName = configTasks.theme(),
	settings = configTasks.settings,
	banner = configTasks.banner.css
;

module.exports = function(callback)
{
	//-- Copy files in assets
	var assets = gulp.src(config.paths.semantic + '/themes/**/assets/**/*.*')
		.pipe(rename(configTasks.settings.renameThemeAssets))
		.pipe(gulp.dest(config.paths.build + '/themes'))
	;

	//-- Copy CSS file
	var css = gulp.src(config.paths.semantic + (isProduction ? '/semantic.min.css' : '/semantic.css'))
		.pipe(rename(themeName + '.css'))
		.pipe(gulpif(isProduction, header(banner, settings.header)))
		.pipe(gulp.dest(config.paths.build + '/themes'))
		.pipe(print(log.copied))
	;

	//-- Copy JS file
	var js = gulp.src(config.paths.semantic + (isProduction ? '/semantic.min.js' : '/semantic.js'))
		.pipe(rename('semantic.js'))
        .pipe(gulp.dest(config.paths.build + '/resources'))
		.pipe(print(log.copied))
	;

	return merge(assets, css, js);
}
