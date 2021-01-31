//-- Task

module.exports = function (gulp)
{
    const remove = require('../delete')
    const optimize = require('../optimize')
    const skeleton = require('../skeleton')

    gulp.task('delete', remove)
    gulp.task('optimize', optimize)
    gulp.task('skeleton', skeleton)
}
