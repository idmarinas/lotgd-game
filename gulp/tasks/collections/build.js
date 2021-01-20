//-- Tasks

module.exports = function (gulp)
{
    const build = require('../build')
    const main = require('../build/main')
    const core = require('../build/core')

    gulp.task('build', build)
    gulp.task('build').description = 'Builds all files from source'

    gulp.task('main', main)
    gulp.task('main').description = 'Copy main files of application'

    gulp.task('core', core)
    gulp.task('core').description = 'Copy core files of application'
}
