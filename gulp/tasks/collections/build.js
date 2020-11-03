//-- Tasks

module.exports = function (gulp)
{
    const build = require('../build')
    const main = require('../build/main')

    gulp.task('build', build)
    gulp.task('build').description = 'Builds all files from source'

    gulp.task('main', main)
    gulp.task('main').description = 'Copy main files of application'
}
