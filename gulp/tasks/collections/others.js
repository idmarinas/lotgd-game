//-- Task

module.exports = function (gulp)
{
    const remove = require('../delete')
    const composer = require('../composer')
    const optimize = require('../optimize')
    const theme = require('../theme')
    const zip = require('../zip')

    gulp.task('delete', remove)
    gulp.task('composer', composer)
    gulp.task('optimize', optimize)
    gulp.task('theme', theme)
    gulp.task('zip', zip)
}
