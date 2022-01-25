//-- Dependencies
const gulp = require('gulp')
const print = require('gulp-print').default
const uglify = require('gulp-uglify')
const fancyLog = require('fancy-log')
const colors = require('ansi-colors')
const del = require('del')
const normalize = require('normalize-path')
const fs = require('fs')

//-- Configuration
const config = require('../config/default')
const configTasks = require('../config/tasks')
const log = configTasks.log
const isProduction = configTasks.isProduction()
const settings = configTasks.settings

module.exports = function (callback)
{
    //-- This files not is necesary in production
    if (isProduction)
    {
        (async () =>
        {
            const file = config.paths.build.prod + '/.env'

            fs.readFile(file, 'utf8', function (err, data)
            {
                if (err)
                {
                    return console.log(err)
                }

                const result = data.replace(/APP_ENV=dev/g, 'APP_ENV=prod')

                fs.writeFile(file, result, 'utf8', function (error)
                {
                    if (error) return console.log(error)
                })
            })
        })();

        (async () =>
        {
            const deletedPaths = await del([
                normalize(config.paths.build.prod + '/sonar-project.properties'),
                normalize(config.paths.build.prod + '/**/*.{dist,md,lock}{,/**}'),
                normalize(config.paths.build.prod + '/*.{json,yml,yaml,xml,txt,TXT,csv,phar,js,php}{,/**}'),
                normalize(config.paths.build.prod + '/config/development{,/**}'),
                normalize(config.paths.build.prod + '/config/local/dbconnect.php'),
                normalize(config.paths.build.prod + '/config{,/**}/development{,.*}.*'),
                normalize('!' + config.paths.build.prod + '/lotgd-check-requeriments.php'), //-- Ignore file: check requeriments
                normalize('!' + config.paths.build.prod + '/{composer,symfony}.{json,lock}'), //-- Can use in prod server to optimize-autoloader
                normalize('!' + config.paths.build.prod + '/data/installer/README.md') //-- Not delete this file
            ])

            fancyLog.info('Deleted files and directories:\n', deletedPaths.join('\n'))
        })()
    }

    const destFolder = isProduction ? config.paths.build.prod : config.paths.build.dev

    //-- Resources folder - Only JS
    let src = gulp.src(`${destFolder}/public/js/*.js`)

    if (isProduction)
    {
        src.pipe(uglify(settings.uglify.some))
    }

    return src.on('error', function (err) { fancyLog(colors.red('[Error]'), err.toString()) })
        .pipe(gulp.dest(`${destFolder}/public/js`))
        .pipe(print(log.copied))
}
