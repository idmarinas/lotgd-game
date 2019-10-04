const { series, parallel } = require('gulp')

module.exports = function (cb)
{
    console.info('Building application')

    return series('delete', parallel('main', 'theme'), 'composer', 'optimize', 'lotgd-js', 'zip')(cb)
}
