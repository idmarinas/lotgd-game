var gulp = require('gulp');
var del = require('del');
var variables = require('../variables');

//-- Borrar los archivos de la carpeta CSS
gulp.task('build-empty', function () {
	return del([
		variables.build_dir
	]);
});