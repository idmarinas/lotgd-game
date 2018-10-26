//-- Dependencies
var del = require('del')

//-- Configuration
var config = require('../config/default')
var configTasks = require('../config/tasks')

var isProduction = configTasks.isProduction()

var themeName = configTasks.theme()

module.exports = function (callback)
{
    //-- Move html of them to correct directory
    // var assetsMove = gulp.src(config.paths.build + '/themes/' + themeName + '/' + themeName + '.html')
    //     .pipe(gulp.dest(config.paths.build + '/themes'))

    del(config.paths.build + '/themes/' + themeName + '/' + themeName + '.html')

    //-- This files not is necesary in production
    if (isProduction)
    {
        del([
            config.paths.build + '/**/*.{dist,md,lock,json,.yml}',
            config.paths.build + '/config/development/{**,*}',
            config.paths.build + '/config/{**,*}/development{,.*}.*'
        ])
    }

    return callback()
}
