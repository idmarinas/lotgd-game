//-- Task

module.exports = function (gulp)
{
    const remove = require('../delete')
    const composer = require('../composer')
    const optimize = require('../optimize')
    const skeleton = require('../skeleton')
    const zip = require('../zip')

    gulp.task('delete', remove)
    gulp.task('composer', composer)
    gulp.task('optimize', optimize)
    gulp.task('skeleton', skeleton)
    gulp.task('zip', zip)
}
