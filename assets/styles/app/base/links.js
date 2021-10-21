/**
 * Links
 *
 * @param function theme
 * @param function e
 *
 * @returns object
 */
module.exports = function (theme, e)
{
    return {
        'a:not(.unstyle)': {
            color: theme('colors.lotgd.50'),
            '&:hover': {
                color: theme('colors.lotgd.200')
            },
        },
        '.navigation': {
            'a': {
                display: 'inline-block',
                paddingLeft: theme('padding.1'),
                width: theme('width.full'),
                borderWidth: theme('borderWidth.DEFAULT'),
                borderColor: 'transparent',
                borderTopRightRadius: theme('borderRadius.DEFAULT'),
                borderBottomRightRadius: theme('borderRadius.DEFAULT'),
                '&:hover': {
                    backgroundColor: theme('colors.lotgd.800'),
                    borderColor: `${theme('colors.lotgd.500')} ${theme('colors.lotgd.700')} ${theme('colors.lotgd.700')} ${theme('colors.lotgd.500')}`
                }
            },
            '> span.header': {
                display: 'inline-block',
                width: theme('width.full'),
                color: theme('colors.lotgd.gray.50'),
                fontVariant: 'small-caps',
                fontWeight: 700,
                lineHeight: theme('lineHeight.5'),
                padding: theme('padding.1'),
                textAlign: 'center',
                '&:before': { content: `"\\2014"` },
                '&:after': { content: `"\\2014"` }
            },
            '> span.nav': {
                display: 'inline-block',
                width: theme('width.full'),
                color: theme('colors.lotgd.gray.200'),
                paddingLeft: theme('padding.1'),
            }
        }
    }
}
