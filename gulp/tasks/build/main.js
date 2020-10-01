/** *****************************
                Copy main files
***************************** **/
//-- Dependencias
const gulp = require('gulp')
const merge = require('merge-stream')

//-- Configuration
const config = require('../../config/default')
const configTasks = require('../../config/tasks')

const isProduction = configTasks.isProduction()

module.exports = function (callback)
{
    const destFolder = isProduction ? config.paths.build.prod : config.paths.build.dev

    const main = gulp.src(config.files.main, { base: '.' })
        .pipe(gulp.dest(destFolder))

    if (config.files.core_files.length)
    {
        const core = gulp.src(config.files.core_files)
            .pipe(gulp.dest(destFolder))

        return merge(main, core)
    }

    return main
}
