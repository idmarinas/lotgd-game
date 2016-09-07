var gulp = require('gulp');
var runSequence = require('run-sequence');

gulp.task('production', function (callback) {
	runSequence(
		'build-empty',
		'build',
		'js-lotgd',
		'js-uikit',


		'prod-js-min',
		//-- Copia todos los archivos a la carpeta de producción
		'prod-copy',

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