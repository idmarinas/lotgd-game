//-- Dependencias
const path = require('path')

exports.assetsPath = function (_path)
{
    return path.posix.join('public', _path)
}
