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
            '!docs{,/**}', //-- Ignore the "docs/" folder, you don't need to publish it on your server.
            '!release{,/**}', //-- These are the compiled files of the different versions ready to use in production.
            '!semantic{,/**}',
            '!entity{,/**}', //-- Autogenerate entities from BD
            '!node_modules{,/**}',
            '!bower_components{,/**}',
            //-- Ignore because using composer to update all packages
            '!vendor{,/**}'
        ]
    }
}
