var
	//-- Dependencies
	gulp = require('gulp-help')(require('gulp')),
	runSequence = require('run-sequence')
;

module.exports = function(callback)
{
  	console.info('Building theme for LOTGD');

	runSequence(
		'theme-pre',
		'semantic-ui',
		'theme-post',
		'theme-end',

		callback
	);
};