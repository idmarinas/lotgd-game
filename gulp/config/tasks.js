//-- Dependencies
const release = require('./project/release')
const env = require('minimist')(process.argv.slice(2))

//-- Options
const themeOptions = { theme: env.theme || 'jade' }
const envOptions = { env: env.env || 'development' }
const options = Object.assign(env, envOptions, themeOptions)

module.exports = {
    banner: release.banner,
    version: release.version,
    log: {
        created: function (file)
        {
            return 'Created: ' + file
        },
        modified: function (file)
        {
            return 'Modified: ' + file
        },
        copied: function (file)
        {
            return 'Copied: ' + file
        },
        deleted: function (file)
        {
            return 'Deleted: ' + file
        }
    },
    //-- Determinate if is a enviroment of DEVELOPMENT or PRODUCTION
    //-- By default is development
    isProduction: function ()
    {
        if (options.env === 'production') return true
        else if (options.env === 'prod') return true
        else return false
    },

    createRelease: function ()
    {
        return Boolean(options.zip)
    },

    theme: function ()
    {
        return options.theme
    },

    settings: {

        /* Remove Files in Clean */
        del: {
            silent: true
        },

        configJsonSync: {
            src: 'composer.json',
            fields: [
                'version',
                'description',
                'homepage',
                'time',
                'authors'
            ],
            space: '	'
        },

        /* Comment Banners */
        header: {
            title: release.title,
            version: release.version,
            year: release.year()
        },

        /* Minified JS Settings */
        uglify: {
            some: {
                mangle: true,
                output: {
                    comments: 'some'
                }
            },
            noComments: {
                mangle: true,
                output: {
                    comments: false
                }
            }
        },

        /* Rename folders of assets */
        renameThemeAssets: function (path)
        {
            path.dirname = path.dirname.replace('\\assets', '')
        },

        removeCode: {
            production: true
        }
    }
}
