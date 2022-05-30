/** *****************************
                Copy main files
***************************** **/
//-- Dependencias
const gulp = require('gulp')

//-- Configuration
const config = require('../../config/default')
const configTasks = require('../../config/tasks')

const isProduction = configTasks.isProduction()

module.exports = function (_callback)
{
    const destFolder = isProduction ? config.paths.build.prod : config.paths.build.dev

    return gulp.src(config.files.main, { base: '.' })
        .pipe(gulp.dest(destFolder))
}
