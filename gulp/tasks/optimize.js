//-- Dependencies
var gulp = require('gulp')
var print = require('gulp-print').default
var gulpif = require('gulp-if')
var uglify = require('gulp-uglify')
var fancyLog = require('fancy-log')
var colors = require('ansi-colors')
var del = require('del')

//-- Configuration
var config = require('../config/default')
var configTasks = require('../config/tasks')
var log = configTasks.log
var isProduction = configTasks.isProduction()
var settings = configTasks.settings

module.exports = function (callback)
{
    //-- This files not is necesary in production
    if (isProduction)
    {
        del([
            config.paths.build + '/cli-config.php',
            config.paths.build + '/**/*.{dist,md,lock,json,yml,xml}{,/**}',
            config.paths.build + '/config/development/{**,*}',
            config.paths.build + '/config/{**,*}/development{,.*}.*'
        ])
    }

    //-- Resources folder - Only JS
    return gulp.src(config.paths.build + '/public/js/*.js')
        .pipe(gulpif(isProduction, uglify(settings.uglify.some)))
        .on('error', function (err) { fancyLog(colors.red('[Error]'), err.toString()) })
        .pipe(gulp.dest(config.paths.build + '/public/js'))
        .pipe(print(log.copied))
}
