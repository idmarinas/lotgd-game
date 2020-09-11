//-- Dependencies
var gulp = require('gulp')
var print = require('gulp-print').default
var gulpif = require('gulp-if')
var uglify = require('gulp-uglify')
var fancyLog = require('fancy-log')
var colors = require('ansi-colors')
var del = require('del')
const normalize = require('normalize-path')

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
        (async () =>
        {
            const deletedPaths = await del([
                normalize(config.paths.build + '/cli-config.php'),
                normalize(config.paths.build + '/**/*.{dist,md,lock}{,/**}'),
                normalize(config.paths.build + '/*.{json,yml,yaml,xml,txt,TXT,csv,phar,js,php}{,/**}'),
                normalize(config.paths.build + '/config/development{,/**}'),
                normalize(config.paths.build + '/config/local/dbconnect.php'),
                normalize(config.paths.build + '/config{,/**}/development{,.*}.*'),
                normalize('!' + config.paths.build + '/lotgd-check-requeriments-*.php'), //-- Ignore all files: check requeriments
                normalize('!' + config.paths.build + '/composer.{json,lock}') //-- Can use in prod server to optimize-autoloader
            ])

            fancyLog.info('Deleted files and directories:\n', deletedPaths.join('\n'))
        })()
    }

    //-- Resources folder - Only JS
    return gulp.src(config.paths.build + '/public/js/*.js')
        .pipe(gulpif(isProduction, uglify(settings.uglify.some)))
        .on('error', function (err) { fancyLog(colors.red('[Error]'), err.toString()) })
        .pipe(gulp.dest(config.paths.build + '/public/js'))
        .pipe(print(log.copied))
}
