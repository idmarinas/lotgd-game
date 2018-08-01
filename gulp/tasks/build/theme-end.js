//-- Dependencies
var gulp = require('gulp')
var print = require('gulp-print').default
var vinylPaths = require('vinyl-paths')
var del = require('del')

//-- Configuration
var config = require('../../config/default')
var configTasks = require('../../config/tasks')
var log = configTasks.log
var themeName = configTasks.theme()

module.exports = function (callback)
{
    //-- Copy and delete themeName.html in correct folder
    return gulp.src(config.paths.build + '/themes/' + themeName + '/' + themeName + '.html')
        // .pipe(print(log.deleted))
        .pipe(vinylPaths(del))
        .pipe(print(log.deleted))
        .pipe(gulp.dest(config.paths.build + '/themes'))
        .pipe(print(log.copied))
}
