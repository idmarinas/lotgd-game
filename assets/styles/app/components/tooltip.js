module.exports = function (theme, e)
{
    return {
        '[data-tooltip]': {
            position: 'relative',
            borderBottom: '1px dotted black',

            //-- Arrow
            '&:hover::before': {
                pointerEvents: 'none',
                position: 'absolute',
                content: `""`,
                fontSize: theme('fontSize.base'),
                width: theme('width.5'),
                height: theme('height.5'),
                transform: 'rotate(45deg)',
                backgroundColor: theme('colors.lotgd.500'),
                zIndex: theme('zIndex.100'),

                //-- Position
                top: 'auto',
                right: 'auto',
                bottom: '100%',
                left: '50%',
            },

            //-- Content
            '&:hover::after': {
                pointerEvents: 'none',
                content: 'attr(data-tooltip)',
                position: 'absolute',
                textTransform: 'none',
                maxWidth: theme('width.80'),
                minWidth: theme('width.40'),
                width: 'auto',
                backgroundColor: theme('colors.lotgd.500'),
                borderRadius: theme('borderRadius.DEFAULT'),
                padding: theme('padding.2'),
                zIndex: theme('zIndex.100'),
                whiteSpace: 'break-spaces',

                //-- Position
                left: '50%',
                bottom: '100%',
                transform: 'translateX(-50%)',
            },
        }
    }
}
