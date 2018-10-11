/** *****************************
                Copy main files
***************************** **/
//-- Dependencias
var gulp = require('gulp')

//-- Configuration
var config = require('../../config/default')

module.exports = function (callback)
{
    return gulp.src(config.files.main, { base: '.' })
        .pipe(gulp.dest(config.paths.build))
}
