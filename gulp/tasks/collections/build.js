/*******************************
				Define Sub-Tasks
*******************************/

module.exports = function(gulp)
{
	var
		//-- Tasks
		main = require('../build/main'),
		assets = require('../build/assets'),
		lotgdJs = require('../build/lotgd-js'),
		themePre = require('../build/theme-pre'),
		themePost = require('../build/theme-post'),
		semantic = require('../../../semantic/tasks/build')
	;

	gulp.task('main', 'Copy main files of application', main);
	gulp.task('lotgd-js', 'Create JS of LOTGD', lotgdJs);
	gulp.task('assets', 'Copy sources for application', assets);
	gulp.task('theme-pre', false, themePre);
	gulp.task('theme-post', false, themePost);
	gulp.task('semantic-ui', 'Build Semantic UI', semantic);
};