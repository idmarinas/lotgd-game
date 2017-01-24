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
		'pre-theme',
		'semantic-ui',
		'theme',
		'assets',
		'application',
		'clean',

		callback
	);
};