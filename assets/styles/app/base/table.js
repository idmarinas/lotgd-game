module.exports = function (theme, e)
{
    return {
        'table:not(.unstyle)': {
            '> thead': {
                backgroundColor: theme('colors.lotgd.900'),

                '&.striped > tr:nth-child(2n)': {
                    backgroundColor: 'rgba(255,255,255,.15)',
                },

                '> tr': {
                    '> th': {
                        paddingLeft: theme('padding.6'),
                        paddingRight: theme('padding.6'),
                        paddingTop: theme('padding.3'),
                        paddingBottom: theme('padding.3'),
                        textAlign: 'left',
                        fontSize: theme('fontSize.xs'),
                        lineHeight: theme('fontSize').xs[1].lineHeight,
                        textTransform: 'uppercase',
                        letterSpacing: theme('letterSpacing.wider'),
                        color: theme('colors.lotgd.300')
                    }
                }
            },
            '> tbody': {
                backgroundColor: theme('colors.lotgd.800'),

                '&.striped > tr:nth-child(2n)': {
                    backgroundColor: 'rgba(0,0,0,.15)',
                },

                '> tr': {
                    '> td': {
                        paddingLeft: theme('padding.6'),
                        paddingRight: theme('padding.6'),
                        paddingTop: theme('padding.3'),
                        paddingBottom: theme('padding.3'),
                        whiteSpace: 'nowrap',
                        fontSize: theme('fontSize.sm'),
                        lineHeight: theme('fontSize').sm[1].lineHeight,
                    }
                }
            }
        }
    }
}
