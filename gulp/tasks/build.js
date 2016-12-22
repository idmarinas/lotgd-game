var gulp = require('gulp');
var runSequence = require('run-sequence');

gulp.task('build', function (callback) {
	runSequence(
		'build-empty',
		'copy-main',
		'lotgd',
		'copy-theme',

		function (error) {
			if (error) {
				console.log(error.message);
			}
			callback(error);
		}
	);
});