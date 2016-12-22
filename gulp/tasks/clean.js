var gulp = require('gulp');
var del = require('del');
var variables = require('../variables');

//-- Delete content of directory build
gulp.task('build-empty', function () {
	return del([
		variables.build.dir
	]);
});