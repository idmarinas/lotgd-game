//-- Dependencies
const gulp = require('gulp')
const print = require('gulp-print').default
const fancyLog = require('fancy-log')
const colors = require('ansi-colors')
const zip = require('gulp-zip')

//-- Configuration
const config = require('../config/default')
const configTasks = require('../config/tasks')
const log = configTasks.log
const isProduction = configTasks.isProduction()
const createRelease = configTasks.createRelease()

module.exports = function (callback)
{
    if (isProduction && createRelease)
    {
        //-- Resources folder - Only JS
        return gulp.src(config.paths.build.prod + '/**')
            .pipe(zip(configTasks.version + ' IDMarinas Edition.zip'))
            .on('error', function (err) { fancyLog(colors.red('[Error]'), err.toString()) })
            .pipe(gulp.dest('release/'))
            .pipe(print(log.copied))
    }

    return callback()
}
