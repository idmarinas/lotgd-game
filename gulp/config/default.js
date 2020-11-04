//-- Modify with your data

const path = require('path')
const merge = require('lodash.merge')

const distFolder = '../../dist'

const config = {
    paths: {
        semantic: 'semantic/dist', //-- Directory of compiled files of Semantic UI
        //-- Directory for construct game
        build: {
            dist: path.resolve(__dirname, distFolder),
            prod: path.resolve(__dirname, `${distFolder}/prod`),
            dev: path.resolve(__dirname, `${distFolder}/dev`),
            skeleton: path.resolve(__dirname, `${distFolder}/skeleton`)
        }
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
            '!entity{,/**}', //-- Autogenerate entities from BD
            '!_core_files{,/**}', //-- This files are merge if need, in Custom local Skeleton
            //-- Ignore dependency directories
            '!{bower_components,node_modules,vendor}{,/**}'
        ],
        core_files: [], //-- This is used in LoTGD Skeleton
        skeleton: [
            //-- All files includin subdirectories
            '**{,/**}',
            //-- Include .files
            '{/**/.gitkeep,/.codeclimate.yml,/.editorconfig,/.eslintignore,/.eslintrc.js,/.gitignore,/.php_cs,/.stickler.yml,/.watchmanconfig}',
            //-- Ignore files of development
            '!{docs,release}{,/**}',
            //-- Ignore folders not need for skeleton
            '!{error_docs,lib,skeleton}{,/**}',
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
            '!templates_core{,/**}',
            //-- Ignore dependency directories
            '!{bower_components,node_modules,vendor}{,/**}',
            //-- Ignore all dist folders
            '!{*.,}dist{,/**}',
            //-- Ignore some files
            '!*.{txt,TXT,csv,phar}',
            '!{README.md,TODO.md,phpdoc.dist.xml}'
        ]
    }
}

let custom = {}
try
{
    custom = require('../custom/config/default')
}
catch (error)
{
    console.log('Not find custom config default')
}

module.exports = merge(config, custom)
