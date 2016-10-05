var gulp = require('gulp');
var runSequence = require('run-sequence');

gulp.task('build', function (callback) {
	runSequence(
		'build-empty',
		'js-lotgd',
		'js-uikit',
		'js-jquery',

		function (error) {
			if (error) {
				console.log(error.message);
			} else {
				console.log('SE HAN TERMINADO LAS TAREAS DE CONTRUCCIÓN DEL PROYECTO CON ÉXITO');
			}
			callback(error);
		}
	);
});