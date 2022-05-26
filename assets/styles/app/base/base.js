/**
 * Base document
 *
 * @param function theme
 * @param function e
 *
 * @returns object
 */
module.exports = function (theme, _e)
{
    return {
        html: {
            fontSize: theme('fontSize.sm'),
            color: theme('colors.gray.200'),
            backgroundColor: theme('colors.lotgd.DEFAULT')
        },
        body: {
            padding: theme('padding.1'),
        },
        'h1': { fontSize: theme('fontSize.2xl') },
        'h2': { fontSize: theme('fontSize.xl') },
        'h3': { fontSize: theme('fontSize.lg') },
        'h4': { fontSize: theme('fontSize.base') },
        'h5': { fontSize: theme('fontSize.sm') },
        'h6': { fontSize: theme('fontSize.xs') },
    }
}
