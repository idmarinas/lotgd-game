//-- Dependencies
var gulp = require('gulp')
var merge = require('merge-stream')
var print = require('gulp-print')
var gulpif = require('gulp-if')
var uglify = require('gulp-uglify')
var gutil = require('gulp-util')

//-- Configuration
var config = require('../../config/default')
var configTasks = require('../../config/tasks')

var log = configTasks.log
var isProduction = configTasks.isProduction()
var settings = configTasks.settings

module.exports = function (callback)
{
    /** ******************** **/
    /** Copy files JS **/
    /** ******************** **/

    //-- Resources folder - Only JS
    var resourceJS = gulp.src('resources/**/*.js')
        .pipe(gulpif(isProduction, uglify(settings.uglify.some)))
        .on('error', function (err) { gutil.log(gutil.colors.red('[Error]'), err.toString()) })
        .pipe(gulp.dest(config.paths.build + '/resources'))
        .pipe(print(log.copied))

    var resourceOther = gulp.src([
        'resources/**/**',
        '!resources/**/*.js'
    ])
        .pipe(gulp.dest(config.paths.build + '/resources'))

    return merge(resourceJS, resourceOther)
}
