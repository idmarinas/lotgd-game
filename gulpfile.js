//-- Dependencies
var gulp = require('gulp-help')(require('gulp'))

//-- Tasks
require('./gulp/tasks/collections/build')(gulp)
require('./gulp/tasks/collections/others')(gulp)

gulp.task('default', gulp.series('build'))
