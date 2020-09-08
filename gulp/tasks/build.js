const { series, parallel } = require('gulp')

module.exports = function (cb)
{
    console.info('Building application')

    return series('delete', parallel('main'), 'composer', 'optimize', 'zip')(cb)
}
