var gulp = require('gulp');
var runSequence = require('run-sequence');

gulp.task('production', function (callback) {
	runSequence(
		'build',
		'min',

		//-- Copia todos los archivos a la carpeta de producción
		'prod-copy',

		function (error) {
			if (error) {
				console.log(error.message);
			} else {
				console.log('PRODUCTION: FINISHED');
			}
			callback(error);
		}
	);
});