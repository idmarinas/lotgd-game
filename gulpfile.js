//-- Dependencies
var gulp = require('gulp-help')(require('gulp'))

//-- Tasks
var build = require('./gulp/tasks/build')
var theme = require('./gulp/tasks/theme')
var del = require('./gulp/tasks/delete')
var clean = require('./gulp/tasks/clean')
var composer = require('./gulp/tasks/composer')

require('./gulp/tasks/collections/copy')(gulp)

/**
 * Task
 */
gulp.task('default', false, [
    'build'
])

gulp.task('build', 'Builds all files from source', build)

gulp.task('theme', 'Create theme for LOTGD', theme)

gulp.task('clean', 'Clean dist application source', clean)

gulp.task('delete', 'Delete dist folder', del)

gulp.task('composer', 'Removes PHP dependencies for development environment', composer)
