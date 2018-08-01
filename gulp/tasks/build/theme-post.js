//-- Dependencies
var gulp = require('gulp')
var merge = require('merge-stream')
var rename = require('gulp-rename')
var print = require('gulp-print').default
var gulpif = require('gulp-if')
var header = require('gulp-header')

//-- Configuration
var config = require('../../config/default')
var configTasks = require('../../config/tasks')
var log = configTasks.log
var isProduction = configTasks.isProduction()
var themeName = configTasks.theme()
var settings = configTasks.settings
var banner = configTasks.banner.css

module.exports = function (callback)
{
    //-- Copy files in assets
    var assets = gulp.src(config.paths.semantic + '/themes/**/assets/**/*.*')
        .pipe(rename(configTasks.settings.renameThemeAssets))
        .pipe(gulp.dest(config.paths.build + '/themes'))

    //-- Copy CSS file
    var css = gulp.src(config.paths.semantic + (isProduction ? '/semantic.min.css' : '/semantic.css'))
        .pipe(rename(themeName + '.css'))
        .pipe(gulpif(isProduction, header(banner, settings.header)))
        .pipe(gulp.dest(config.paths.build + '/themes'))
        .pipe(print(log.copied))

    //-- Copy JS file
    var js = gulp.src(config.paths.semantic + (isProduction ? '/semantic.min.js' : '/semantic.js'))
        .pipe(rename('semantic.js'))
        .pipe(gulp.dest(config.paths.build + '/resources'))
        .pipe(print(log.copied))

    return merge(assets, css, js)
}
