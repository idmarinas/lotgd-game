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
                normalize(config.paths.build.prod + '/cli-config.php'),
                normalize(config.paths.build.prod + '/**/*.{dist,md,lock}{,/**}'),
                normalize(config.paths.build.prod + '/*.{json,yml,yaml,xml,txt,TXT,csv,phar,js,php}{,/**}'),
                normalize(config.paths.build.prod + '/config/development{,/**}'),
                normalize(config.paths.build.prod + '/config/local/dbconnect.php'),
                normalize(config.paths.build.prod + '/config{,/**}/development{,.*}.*'),
                normalize('!' + config.paths.build.prod + '/lotgd-check-requeriments-*.php'), //-- Ignore all files: check requeriments
                normalize('!' + config.paths.build.prod + '/composer.{json,lock}') //-- Can use in prod server to optimize-autoloader
            ])

            fancyLog.info('Deleted files and directories:\n', deletedPaths.join('\n'))
        })()
    }

    const destFolder = isProduction ? config.paths.build.prod : config.paths.build.dev

    //-- Resources folder - Only JS
    return gulp.src(`${destFolder}/public/js/*.js`)
        .pipe(gulpif(isProduction, uglify(settings.uglify.some)))
        .on('error', function (err) { fancyLog(colors.red('[Error]'), err.toString()) })
        .pipe(gulp.dest(`${destFolder}/public/js`))
        .pipe(print(log.copied))
}
