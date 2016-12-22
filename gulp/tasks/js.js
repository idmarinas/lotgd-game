var gulp = require('gulp');
var concat = require('gulp-concat');
var concat = require('gulp-concat');
var strip = require('gulp-strip-comments');
var variables = require('../variables');

//-- Construir el archivo de LOTGD
gulp.task('lotgd', function () {
	return gulp.src([
			'assets/lotgd.js',
			'assets/components/*.js'
		])
		.pipe(concat('lotgd.js'))
		.pipe(strip({ safe: true }))
		.pipe(gulp.dest(variables.build.dir + '/resources/'))
	;
});