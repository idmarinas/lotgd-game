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
            '**{,/**}',
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
            //-- Include .files
            '{/**/.gitkeep,/.env,/.htaccess}',
            //-- Ignore dependency directories
            '!{bower_components,node_modules,vendor,var}{,/**}'
        ],
        core_files: [], //-- This is used in LoTGD Skeleton
        skeleton: [
            //-- All files includin subdirectories
            '**{,/**}',
            //-- Ignore files of development
            '!{docs,release}{,/**}',
            //-- Ignore folders not need for skeleton
            '!{error_docs,lib,skeleton}{,/**}',
            //-- Ignore files of core
            '!assets/lotgd/css/semantic{,/**}',
            '!config/autoload/global{,/**}',
            '!config/autoload/local{,/**}',
            '!config/lotgd.config.php',
            '!cronjob/*.php',
            '!data/form/core{,/**}',
            '!public{,/**}',
            '!modules{,/**}',
            '!src/module{,/**}',
            '!src/core{,/**}',
            '!src/ajax/core{,/**}',
            '!src/ajax/pattern/core{,/**}',
            '!templates_core{,/**}',
            '!templates/lotgd{,/**}', //-- No need in skeleton Can see examples in copy that you need have in "_core_files/templates/lotgd" folder
            '!themes{,/**}', //-- No need in skeleton Can see examples in copy that you need have in "_core_files/themes" folder
            '!translations/en{,/**}', //-- No need in skeleton Can see examples in copy that you need have in "_core_files/translations/en" folder
            '!src/{Controller,Entity,Repository}{,/**}', //-- Not need for now this folders
            //-- Ignore some files
            '!*.{txt,TXT,csv,phar}',
            '!{README.md,TODO.md,phpdoc.dist.xml}',
            //-- Include .files
            '{/**/.gitkeep,/.env,/.codeclimate.yml,/.editorconfig,/.eslintignore,/.eslintrc.js,/.gitignore,/.php_cs,/.stickler.yml,/.watchmanconfig}',
            //-- Ignore all dist folders
            '!{*.,}dist{,/**}',
            //-- Ignore dependency directories
            '!{bower_components,node_modules,vendor,var}{,/**}'
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
