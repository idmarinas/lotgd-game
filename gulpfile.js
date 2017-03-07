var
	gulp = require('gulp-help')(require('gulp')),

	//-- Tasks
	build = require('./gulp/tasks/build'),
	theme = require('./gulp/tasks/theme'),
	del = require('./gulp/tasks/delete'),
	clean = require('./gulp/tasks/clean')

;

require('./gulp/tasks/collections/copy')(gulp);

/**
 * Task
 */
gulp.task('default', false, [
  'build'
]);

gulp.task('build' , 'Builds all files from source', build);

gulp.task('theme' , 'Create theme for LOTGD', theme);

gulp.task('clean' , 'Clean dist application source', clean);

gulp.task('delete' , 'Delete dist folder', del);
