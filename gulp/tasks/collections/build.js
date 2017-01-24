/*******************************
				Define Sub-Tasks
*******************************/

module.exports = function(gulp)
{
	var
		//-- Tasks
		main = require('../build/main'),
		assets = require('../build/assets'),
		application = require('../build/app'),
		theme = require('../build/theme'),
		preTheme = require('../build/pre-theme'),
		semantic = require('../../../semantic/tasks/build')
		// semanticCss = require('../../../semantic/tasks/build/css'),
		// semanticJs = require('../../../semantic/tasks/build/javascript'),
		// semanticAssets = require('../../../semantic/tasks/build/assets')
	;

	gulp.task('main', 'Copy main files of application', main);
	gulp.task('application', 'Create JS of LOTGD', application);
	gulp.task('assets', 'Copy sources for application', assets);
	gulp.task('pre-theme', false, preTheme);
	gulp.task('theme', 'Create themes of aplication', theme);
	gulp.task('semantic-ui', 'Build Semantic UI', semantic);
	// gulp.task('semantic-ui-css', 'Create only CSS of Semantic UI', semanticCss);
	// gulp.task('semantic-ui-js', 'Create only javascript of Semantic UI', buildJS);
	// gulp.task('semantic-ui-assets', 'Create only assets of Semantic UI', buildAssets);

};