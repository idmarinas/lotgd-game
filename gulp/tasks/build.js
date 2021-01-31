const { series } = require('gulp')

module.exports = function (cb)
{
    console.info('Building application')

    return series('delete', 'core', 'main', 'optimize')(cb)
}
