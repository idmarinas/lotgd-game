var gulp = require('gulp');
var runSequence = require('run-sequence');

gulp.task('development', function (callback) {
	runSequence(
		'build',

		//-- Copia todos los archivos a la carpeta de desarrollo
		'dev-copy',

		function (error) {
			if (error) {
				console.log(error.message);
			} else {
				console.log('SE HAN TERMINADO LAS TAREAS DE DESARROLLO CON ÉXITO');
			}
			callback(error);
		}
	);
});

gulp.task('test-development', function (callback) {
	runSequence(
		'build',

		//-- Copia todos los archivos a la carpeta de desarrollo (versión test)
		'test-copy-dev',

		function (error) {
			if (error) {
				console.log(error.message);
			} else {
				console.log('SE HAN TERMINADO LAS TAREAS DE DESARROLLO CON ÉXITO');
			}
			callback(error);
		}
	);
});