/*******************************
				Copy main files
*******************************/
var
	//-- Dependencias
	gulp = require('gulp'),
	del = require('del'),

	//-- Configuration
	config = require('../../config/default')
;

module.exports = function(callback)
{
	return gulp.src(config.files.main, {base : '.'})
		.pipe(gulp.dest(config.paths.build))
	;
};