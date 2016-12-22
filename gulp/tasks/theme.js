var gulp = require('gulp');
var merge = require('merge-stream');
var rename = require('gulp-regex-rename');
var concat = require('gulp-concat');
var autoprefixer = require('gulp-autoprefixer');
var variables = require('../variables.js');

//-- Copy theme files
gulp.task('copy-theme', function () {
	//## CSS
	//#######
	let cssFiles = [];
	variables.themes.names.forEach(function(item, index) {
		cssFiles[index] = variables.themes.dir + '/dist/css/uikit.' + item + '.css';
	});

	var css = gulp.src(cssFiles)
		.pipe(autoprefixer({
			browsers: ['last 15 versions'],
			cascade: false
		}))
		.pipe(rename(/uikit\./, ''))
		.pipe(gulp.dest(variables.build.dir + '/themes'))
	;

	//## JS UIkit
	//###########
	let uikit = [variables.themes.dir + '/dist/js/uikit.js'];
	let components = [];
	let exclude = [];

	//-- Add Base DIR
	variables.themes.uikit.components.forEach(function (item, index) {
		components[index] = variables.themes.dir + item;
	});
	variables.themes.uikit.exclude.forEach(function (item, index) {
		exclude[index] = '!' + variables.themes.dir + item;
	});

	//-- Merge arrays
	uikit = uikit.concat(components, exclude);

	var js = gulp.src(uikit)
        .pipe(concat('uikit.js'))
        .pipe(gulp.dest(variables.build.dir + '/resources'))
	;

	//## HTML
	//########
	let htmlFiles = [];
	variables.themes.names.forEach(function(item, index) {
		htmlFiles[index] = variables.themes.dir + '/custom/' + item + '/' + item + '.html';
	});

	var html = gulp.src(htmlFiles)
		.pipe(gulp.dest(variables.build.dir + '/themes'))
	;

	//## Templates
	//#############
	var templates = [];
	variables.themes.names.forEach(function (item, index) {
		let temp = gulp.src([
				variables.themes.dir + '/custom/' + item + '/templates{,/**}',
				variables.themes.dir + '/custom/' + item + '/images{,/**}'
			])
			.pipe(gulp.dest(variables.build.dir + '/themes/' + item))
		;
		templates = merge(templates, temp);
	});

	//## jQuery
	//#########
	var jQuery = gulp.src(variables.themes.dir + '/bower_components/jquery/dist/jquery.js')
		.pipe(gulp.dest(variables.build.dir + '/resources'))
	;

	return merge(css, js, html, templates, jQuery);
});
