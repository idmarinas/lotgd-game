var gulp = require('gulp');
var uglify = require('gulp-uglify');
var variables = require('../variables');

//-- Comprimir todos los archivos .js
gulp.task('prod-js-min', function () {
	return gulp.src(variables.build_dir + '/resources/*.js')
		.pipe(uglify({ preserveComments: 'license' }).on('error', console.error.bind(console)))
		.pipe(gulp.dest(variables.build_dir + '/resources/'))
	;
});