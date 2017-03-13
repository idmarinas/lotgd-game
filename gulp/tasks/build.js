var
	//-- Dependencies
	gulp = require('gulp-help')(require('gulp')),
	runSequence = require('run-sequence')
;

require('./collections/build')(gulp);

module.exports = function(callback)
{
  	console.info('Building application');

	runSequence(
		'delete',
		'main',
		'theme-pre',
		'semantic-ui',
		'theme-post',
		'assets',
		'lotgd-js',
		'clean',

		callback
	);
};
