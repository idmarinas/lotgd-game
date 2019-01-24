//-- Tasks

module.exports = function (gulp)
{
    const build = require('../build')
    const main = require('../build/main')
    const lotgdJs = require('../build/lotgd-js')
    const themePre = require('../build/theme-pre')
    const themePost = require('../build/theme-post')
    require('../../../semantic/tasks/collections/build')(gulp)
    const semantic = require('../../../semantic/tasks/build')

    gulp.task('build', build)
    gulp.task('build').description = 'Builds all files from source'

    gulp.task('main', main)
    gulp.task('main').description = 'Copy main files of application'

    gulp.task('lotgd-js', lotgdJs)
    gulp.task('lotgd-js').description = 'Create JS of LOTGD'

    gulp.task('theme-pre', themePre)

    gulp.task('theme-post', themePost)

    gulp.task('semantic-ui', semantic)
    gulp.task('semantic-ui').description = 'Build Semantic UI'
}
