var gulp = require('gulp');
var runSequence = require('run-sequence');

gulp.task('development-beta', function (callback) {
	runSequence(
		'build',
		'copy-beta',

		function (error) {
			if (error) {
				console.log(error.message);
			} else {
				console.log('DEVELEPMENT: BETA - FINISHED');
			}
			callback(error);
		}
	);
});

gulp.task('development-alpha', function (callback) {
	runSequence(
		'build',
		'copy-alpha',

		function (error) {
			if (error) {
				console.log(error.message);
			} else {
				console.log('DEVELEPMENT: ALPHA - FINISHED');
			}
			callback(error);
		}
	);
});