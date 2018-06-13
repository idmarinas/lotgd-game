//-- Dependencias
var path = require('path')

exports.assetsPath = function (_path)
{
    return path.posix.join('resources', _path)
}
