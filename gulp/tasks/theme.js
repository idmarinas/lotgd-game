const { series } = require('gulp')

module.exports = function (cb)
{
    console.info('Building theme for LOTGD')

    return series('theme-pre', 'semantic-ui', 'theme-post')(cb)
}
