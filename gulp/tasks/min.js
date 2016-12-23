var gulp = require('gulp');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var merge = require('merge-stream');
var header = require('gulp-header');
var variables = require('../variables');
var pkg = require('../../composer.json');

var banner = ['/**',
	' * This file is part of the web "Legend of the Green Dragon"',
	' * Based on UIkit | http://www.getuikit.com | (c) 2014 YOOtheme',
	' *',
	' * @version v<%= pkg.version %>',
	' * @link <%= pkg.homepage %>',
	' */',
	''].join('\n');

//-- MIN files
gulp.task('min', function () {
	//## JS
	//#####
	var js = gulp.src(variables.build.dir + '/resources/*.js')
		.pipe(uglify({ preserveComments: 'license' }).on('error', console.error.bind(console)))
		.pipe(gulp.dest(variables.build.dir + '/resources'))
	;

	//## CSS
	//#######
	var css = gulp.src(variables.build.dir + '/themes/*.css')
		.pipe(uglifycss({
				uglyComments: true
			}))
		.pipe(header(banner, { pkg: pkg }))
		.pipe(gulp.dest(variables.build.dir + '/themes'))
	;

	return merge(js, css);
});