module.exports = function (theme, e)
{
    return {
        //-- Pagination nav
        '.pagination': {
            backgroundColor: theme('colors.lotgd.900'),
            paddingRight: theme('padding.4'),
            paddingLeft: theme('padding.4'),
            paddingTop: theme('padding.3'),
            paddingBottom: theme('padding.3'),

            'nav': {
                zIndex: 0,
                position: 'relative',
                display: 'inline-flex',
                borderRadius: theme('borderRadius.md'),

                '.current': {
                    backgroundColor: theme('colors.lotgd.500'),
                    borderColor: theme('colors.lotgd.600'),
                    color: theme('colors.lotgd.100'),
                    zIndex: 10
                }
            },

            'a': {
                position: 'relative',
                display: 'inline-flex',
                paddingRight: theme('padding.4'),
                paddingLeft: theme('padding.4'),
                paddingTop: theme('padding.2'),
                paddingBottom: theme('padding.2'),
                borderWidth: theme('borderWidth.DEFAULT'),
                backgroundColor: theme('colors.lotgd.600'),
                borderColor: theme('colors.lotgd.700'),
                color: theme('colors.lotgd.50'),
                fontSize: theme('fontSize.sm'),
                lineHeight: theme('fontSize').sm[1].lineHeight,
                fontWeight: theme('fontWeight.medium'),

                '&:hover': {
                    backgroundColor: theme('colors.lotgd.500'),
                    borderColor: theme('colors.lotgd.600'),
                    color: theme('colors.lotgd.100')
                }
            },
        }
    }
}
