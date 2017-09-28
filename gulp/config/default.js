//-- Modify with your data

var path = require('path')

module.exports = {
    paths: {
        /** ***********************************************
         * Directory for diferents test server
         * Only need if you want copy to your localserver
         ********************************************** **/
        development: {
            final: 'D:/xampp/htdocs/lotgd', //-- Directory for final version
            beta: 'D:/xampp/htdocs/lotgd-beta', //-- Directory for beta version
            alpha: 'D:/xampp/htdocs/lotgd-alpha' //-- Directory for alpha version
        },
        semantic: 'semantic/dist', //-- Directory of compiled files of Semantic UI
        jQuery: 'bower_components/jQuery/dist', //-- Directory of jQuery
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
            '!dist{,/**}',
            '!node_modules{,/**}',
            '!bower_components{,/**}',
            '!**/*.{dist,md,lock,json}',
            '!semantic{,/**}',
            //-- Ignore because then we process files for min in production
            '!resources{,/**}',
            //-- Other files
            '!{CHANGELOG.txt,QUICKINSTALL.TXT,README_FIRST.txt,README.txt,INSTALL.TXT}'
        ]
    }
};
