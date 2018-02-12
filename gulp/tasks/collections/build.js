/** *****************************
                Define Sub-Tasks
*******************************/

module.exports = function (gulp)
{
        //-- Tasks
    var main = require('../build/main')
    var assets = require('../build/assets')
    var lotgdJs = require('../build/lotgd-js')
    var themePre = require('../build/theme-pre')
    var themePost = require('../build/theme-post')
    var themeEnd = require('../build/theme-end')
    var semantic = require('../../../semantic/tasks/build')

    gulp.task('main', 'Copy main files of application', main)
    gulp.task('lotgd-js', 'Create JS of LOTGD', lotgdJs)
    gulp.task('assets', 'Copy sources for application', assets)
    gulp.task('theme-pre', false, themePre)
    gulp.task('theme-post', false, themePost)
    gulp.task('theme-end', false, themeEnd)
    gulp.task('semantic-ui', 'Build Semantic UI', semantic)
}
