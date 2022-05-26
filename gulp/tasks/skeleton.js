//-- Dependencies
const gulp = require('gulp')
const print = require('gulp-print').default
const fancyLog = require('fancy-log')
const colors = require('ansi-colors')

//-- Configuration
const config = require('../config/default')
const configTasks = require('../config/tasks')
const log = configTasks.log

module.exports = function (_callback)
{
    return gulp.src(config.files.skeleton, { base: '.' })
        .on('error', function (err) { fancyLog(colors.red('[Error]'), err.toString()) })
        .pipe(gulp.dest(config.paths.build.skeleton))
        .pipe(print(log.copied))
}
