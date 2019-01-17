//-- Modify with your data

var path = require('path')

module.exports = {
    paths: {
        semantic: 'semantic/dist', //-- Directory of compiled files of Semantic UI
        //-- Directory for construct game
        build: path.resolve(__dirname, '../../dist')
    },
    files: {
        //-- Files to copy
        main: [
            //-- All files includin subdirectories
            '**{,/**,/.htaccess}',
            //-- Ignore files of development
            '!gulp{,/**}',
            '!gulpfile.js',
            '!assets{,/**}',
            '!{*.,}dist{,/**}', //-- Ignore all dist folders
            '!node_modules{,/**}',
            '!bower_components{,/**}',
            '!semantic{,/**}',
            //-- Ignore because using composer to update all packages
            '!vendor{,/**}'
        ]
    }
}
