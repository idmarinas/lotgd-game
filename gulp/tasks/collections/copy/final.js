var
	//-- Dependencies
	gulp = require('gulp'),

	//-- Configuration
	config = require('../../../config/default')
;

module.exports = function(callback)
{
	console.info('Copy to final folder server');

	return gulp.src(config.paths.build + '/**{,/.*}')
		.pipe(gulp.dest(config.paths.development.final));
	;
}