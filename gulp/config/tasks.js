var
	//-- Dependencies
	console = require('better-console'),
	release = require('./project/release'),
	minimist = require('minimist'),
	gutil = require('gulp-util'),

	//-- Options
	themeOptions =  { theme: gutil.env.theme || 'jade' },
	envOptions = { env: gutil.env.env || 'development' },
	options = Object.assign(gutil.env, envOptions, themeOptions)
;

module.exports = {

	banner : release.banner,

	log: {
		created: function(file) {
			return 'Created: ' + file;
		},
		modified: function(file) {
			return 'Modified: ' + file;
		},
		copied: function(file) {
			return 'Copied: ' + file;
		}
	},
	//-- Determinate if is a enviroment of DEVELOPMENT or PRODUCTION
	//-- By default is development
	isProduction : function ()
	{
		if (options.env === 'production') return true;
		else if (options.env === 'prod') return true;
		else return false;
	},

	theme: function ()
	{
		return options.theme;
	},

	settings: {

		/* Remove Files in Clean */
		del: {
			silent : true
		},

		configJsonSync: {
			src: 'composer.json',
			fields: [
				'version',
				'description',
				'homepage',
				'time',
				'authors'
			],
			space: '	',
		},

		/* Comment Banners */
		header: {
			title : release.title,
			version : release.version,
			year : release.year()
		},

		/* Minified JS Settings */
		uglify: {
			some: {
				mangle : true,
				preserveComments : 'some'
			},
			noComments: {
				mangle : true,
				preserveComments : false
			}
		},

		/* Rename folders of assets */
		renameThemeAssets : function (path) {
			path.dirname = path.dirname.replace('\\assets', '');
		},

		removeCode : {
			production: true
		}
	}
};
