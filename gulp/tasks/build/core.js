/** ****************
    Copy core files
*******************/
//-- Dependencias
const gulp = require('gulp')

//-- Configuration
const config = require('../../config/default')
const configTasks = require('../../config/tasks')

const isProduction = configTasks.isProduction()

module.exports = function (callback)
{
    if (config.files.core_files.length)
    {
        const destFolder = isProduction ? config.paths.build.prod : config.paths.build.dev

        return gulp.src(config.files.core_files)
            .pipe(gulp.dest(destFolder))
    }

    return callback()
}
