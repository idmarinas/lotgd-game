/**
                Define Sub-Tasks
*******************************/

module.exports = function (gulp)
{
    //-- Tasks
    var alpha = require('./copy/alpha')
    var beta = require('./copy/beta')
    var final = require('./copy/final')

    gulp.task('copy-alpha', 'Copy files to alpha folder server', alpha)
    gulp.task('copy-beta', 'Copy files to beta folder server', beta)
    gulp.task('copy-final', 'Copy files to final folder server', final)
}
