//-- Modify with your data

var path = require('path')

module.exports = {
    paths: {
        semantic: 'semantic/dist', //-- Directory of compiled files of Semantic UI
        //-- Directory for construct game
        build: path.resolve(__dirname, '../../dist'),
        skeleton: path.resolve(__dirname, '../../skeleton')
    },
    files: {
        //-- Files to copy
        main: [
            //-- All files includin subdirectories
            '**{,/**,/.htaccess,/.gitkeep}',
            //-- Ignore files of development
            '!gulp{,/**}',
            '!gulpfile.js',
            '!config/autoload/local/dbconnect.php', //-- Ignore local dbconnect file
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
        ],
        skeleton: [
            //-- All files includin subdirectories
            '**{,/**}',
            //-- Include .files
            '{/**/.gitkeep,/.codeclimate.yml,/.editorconfig,/.eslintignore,/.eslintrc.js,/.gitignore,/.php_cs,/.stickler.yml,/.watchmanconfig,/.whitesource}',
            //-- Ignore files of development
            '!{docs,release}{,/**}',
            //-- Ignore folders not need for skeleton
            '!{error_docs,lib,modules}{,/**}',
            //-- Ignore files of core
            '!config/autoload/global{,/**}',
            '!cronjob/*.php',
            '!data/form/core{,/**}',
            '!public{,/**}',
            'public/**/.gitkeep', //-- Only copy .gitkeep
            '!src/module{,/**}',
            '!src/core{,/**}',
            '!src/ajax/core{,/**}',
            '!src/ajax/pattern/core{,/**}',
            '!templates/{base,core,jaxon,semantic}{,/**}',
            //-- Ignore dependency directories
            '!{bower_components,node_modules,vendor}{,/**}',
            //-- Ignore all dist folders
            '!{*.,}dist{,/**}',
            //-- Ignore some files
            '!*.{txt,TXT,csv}',
            '!README.md'
        ]
    }
}
