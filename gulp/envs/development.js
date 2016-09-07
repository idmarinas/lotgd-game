var gulp = require('gulp');
var runSequence = require('run-sequence');

gulp.task('development', function (callback) {
	var DEVELOPMENT_DIR = '/Users/Ivan/Sites/ignis/';

	runSequence(
		'build-empty',
		'build',
		'js-lotgd',
		'js-uikit',

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