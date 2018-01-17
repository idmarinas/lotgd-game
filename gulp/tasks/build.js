//-- Dependencies
var gulp = require('gulp-help')(require('gulp'))
var runSequence = require('run-sequence')

require('./collections/build')(gulp)

module.exports = function (callback)
{
    console.info('Building application')

    runSequence(
        'delete',
        ['main', 'theme'],
        'assets',
        'lotgd-js',
        'clean',

        callback
    )
}
