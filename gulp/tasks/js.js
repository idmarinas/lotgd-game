var gulp = require('gulp');
var concat = require('gulp-concat');
var concat = require('gulp-concat');
var strip = require('gulp-strip-comments');
var variables = require('../variables');

//-- Construir el archivo de LOTGD
gulp.task('js-lotgd', function () {
	return gulp.src([
		'assets/lotgd.js',
		'assets/components/*.js'
	])
		.pipe(concat('lotgd.js'))
		.pipe(strip({ safe: true }))
		.pipe(gulp.dest(variables.build_dir + '/resources/'));
});

//-- Construir el archivo UIKIT
gulp.task('js-uikit', function () {
	return gulp.src([
		'bower_components/uikit/js/uikit.js',
		// 'bower_components/uikit/js/core/tab.js',
		// 'bower_components/uikit/js/core/switcher.js',
		// 'bower_components/uikit/js/core/dropdown.js',
		// 'bower_components/uikit/js/core/nav.js',
		'bower_components/uikit/js/components/tooltip.js',
		'bower_components/uikit/js/components/notify.js',
		'bower_components/uikit/js/components/grid.js',
		// 'bower_components/uikit/js/components/modal.js',
		// 'bower_components/uikit/js/components/alert.js',
	])
		.pipe(concat('uikit.js'))
		.pipe(gulp.dest(variables.build_dir + '/resources/'));
});

//-- Copiar el archivo jquery
gulp.task('js-jquery', function () {
	return gulp.src('bower_components/jquery/dist/jquery.js')
		.pipe(gulp.dest(variables.build_dir + '/resources/'))
		;
});