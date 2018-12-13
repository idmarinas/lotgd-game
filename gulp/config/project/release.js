/** *****************************
                 Release Config
***************************** **/
var npmPackage
var version

/** *****************************
                 Derived Values
***************************** **/
try
{
    npmPackage = require('../../../package.json')
}
catch (error)
{
    // generate fake package
    npmPackage = {
        name: 'Unknown',
        version: 'x.x'
    }
}

// looks for version in config or package.json (whichever is available)
version = (npmPackage && npmPackage.version !== undefined && npmPackage.name === 'idmarinas-lotgd')
    ? npmPackage.version
    : 'x.y.z'

/** *****************************
                         Export
***************************** **/

module.exports = {

    title: npmPackage.title,
    url: npmPackage.homepage,
    year: function ()
    {
        var date = new Date()

        return date.getFullYear()
    },

    banner: {
        js: '' +
            ' /*' + '\n' +
            ' * This file is part of the web "<%= title %>"' + '\n' +
            ' *' + '\n' +
            ' * @copyright Game Design and Code: Copyright © 2002-2005, Eric Stevens & JT Traub,' + '\n' +
            '				 © 2006-2007 Dragonprime Development Team,' + '\n' +
            '				 © 2007-2014 Oliver Brendel remodelling and enhancing,' + '\n' +
            '				 © 2015-<%= year %> IDMarinas remodelling and enhancing ' + '\n' +
            ' * @version   <%= version %>' + '\n' +
            ' * ' + '\n' +
            ' */' + '\n',

        css: '' +
            ' /*' + '\n' +
            ' * This file was created for the web "<%= title %>"' + '\n' +
            ' *' + '\n' +
            ' * @copyright Game Design and Code: Copyright © 2002-2005, Eric Stevens & JT Traub,' + '\n' +
            '				 © 2006-2007 Dragonprime Development Team,' + '\n' +
            '				 © 2007-2014 Oliver Brendel remodelling and enhancing,' + '\n' +
            '				 © 2015-<%= year %> IDMarinas remodelling and enhancing ' + '\n' +
            ' * @version   <%= version %>' + '\n' +
            ' * ' + '\n' +
            ' */' + '\n'
    },
    version: version
}
