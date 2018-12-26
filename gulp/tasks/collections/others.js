//-- Task

module.exports = function (gulp)
{
    const remove = require('../delete')
    const composer = require('../composer')
    const clean = require('../clean')
    const theme = require('../theme')

    gulp.task('delete', remove)
    gulp.task('composer', composer)
    gulp.task('clean', clean)
    gulp.task('theme', theme)
}
