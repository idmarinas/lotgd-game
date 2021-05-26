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
            //-- All files including subdirectories
            '**{,/**}',
            //-- Ignore files/folders of development
            '!{assets,docs,gulp,gulpfile.js,release,entity}{,/**}',
            //-- Include .files
            '{/**/.gitkeep,/.env,/.htaccess}',
            '!_core_files{,/**}', //-- Moved here for ignore .gitkeep files in this folder
            //-- Ignore all dist folders/files
            '!{*.,}dist{,/**}',
            //-- Ignore dependency directories
            '!{bower_components,node_modules,vendor,var}{,/**}'
        ],
        core_files: [], //-- This is used in LoTGD Skeleton
        skeleton: [
            //-- All files including subdirectories
            '**{,/**}',
            //-- Ignore some extension and files
            '!*.{txt,TXT,csv,phar}',
            '!{README.md,TODO.md,phpdoc.dist.xml}',
            //-- Ignore files of development
            '!{docs,release}{,/**}',
            //-- Ignore content of
            '!data/installer{,/**}',
            'data/installer/README.md', //-- But include README.md
            //-- Ignore folders not need for skeleton
            '!{error_docs,lib,migrations}{,/**}',
            '!assets{,/**}',
            '!bin{,/**}',
            '!config{,/**}',
            //-- Ignore files of core
            '!cronjob/*.php',
            '!public{,/**}',
            '!modules{,/**}',
            '!src/core{,/**}',
            '!src/ajax/core{,/**}',
            '!src/ajax/pattern/core{,/**}',
            '!templates_core{,/**}',
            '!templates/lotgd{,/**}', //-- No need in skeleton Can see examples in copy that you need have in "_core_files/templates/lotgd" folder
            '!themes{,/**}', //-- No need in skeleton Can see examples in copy that you need have in "_core_files/themes" folder
            '!translations/en{,/**}', //-- No need in skeleton Can see examples in copy that you need have in "_core_files/translations/en" folder
            '!src/{Controller,Entity,Repository}{,/**}', //-- Not need for now this folders
            //-- Include .files
            '{/**/.gitkeep,/.env,/.codeclimate.yml,/.editorconfig,/.eslintignore,/.eslintrc.js,/.gitignore,/.php_cs,/.stickler.yml,/.watchmanconfig}',
            //-- Ignore all dist folders/files
            '!{*.,}dist{,/**}',
            '!src/{functions,functions_old}.php',
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
