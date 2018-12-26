const { series, parallel } = require('gulp')

module.exports = function (cb)
{
    console.info('Building application')

    return series('delete', parallel('main', 'theme'), 'assets', 'composer', 'clean', 'lotgd-js')(cb)
}
