module.exports = function (theme, e)
{
    const base = {
        width: theme('width.full'),
        paddingTop: theme('padding.2'),
        paddingBottom: theme('padding.2'),
        paddingLeft: theme('padding.3'),
        paddingRight: theme('padding.3'),
        borderWidth: theme('borderWidth.DEFAULT'),
        borderRadius: theme('borderRadius.DEFAULT'),
        backgroundColor: theme('colors.lotgd.700'),
        borderColor: theme('colors.lotgd.600'),
    }

    const disabledBase = {
        opacity: theme('opacity.40'),
        cursor: theme('cursor.not.allowed')
    }

    const hoverBase = {
        backgroundColor: theme('colors.lotgd.600'),
        borderColor: theme('colors.lotgd.700'),
    }

    const focusBase = {
        backgroundColor: theme('colors.lotgd.500'),
        borderColor: theme('colors.lotgd.400'),
    }

    const input = {
        ...base,
        color: theme('colors.lotgd.gray.100'),
        '&:placeholder': {
            color: theme('colors.lotgd.gray.50')
        },
        '&:hover': {
            ...hoverBase,
            color: theme('colors.lotgd.gray.200')
        },
        '&:focus': {
            ...focusBase,
            color: theme('colors.lotgd.gray.300')
        },
        '&:disabled': { ...disabledBase }
    }

    const button = {
        ...base,
        color: theme('colors.lotgd.gray.50'),
        cursor: theme('cursor.pointer'),
        '&:hover': {
            ...hoverBase,
            color: theme('colors.lotgd.gray.100')
        },
        '&:focus': {
            ...focusBase,
            color: theme('colors.lotgd.gray.200')
        },
        '&:disabled': { ...disabledBase }
    }
    const fieldset = {
        padding: theme('padding.1'),
        '&.required': {
            '> label:after': {
                display: 'inline-block',
                color: theme('colors.lotgd-red.500'),
                content: `"*"`,
                margin: "-.2em 0 0 .2em",
            }
        }
    }

    const radioCheckboxBase = (uncheck, checked) => {
        return {
            border: 0,
            border: 0,
            clip: "rect(0 0 0 0)",
            height: "1px",
            margin: "-1px",
            overflow: 'hidden',
            padding: 0,
            position: 'absolute',
            width: "1px",

            '+ label:before': {
                fontFamily: `"Font Awesome 5 Free"`,
                display: 'inline-block',
                content: `"\\${uncheck}"`,
                letterSpacing: "10px"
            },

            '&:checked + label:before': {
                content: `"\\${checked}"`,
            },

            '&:focus + label:before': {
                fontWeight: "bold"
            }
        }
    }

    const inputRangeTrack = {
        width: '100%',
        height: theme('height.4'),
        cursor: 'pointer',
        animate: '0.2s',
        boxShadow: `1px 1px 1px ${theme('colors.lotgd.800')}`,
        background: theme('colors.lotgd.700'),// '#205928',
        borderRadius: theme('borderRadius.lg'),
        border: `1px solid ${theme('colors.lotgd.600')}`,
    }

    const inputRangeThumb = {
        boxShadow: `1px 1px 1px ${theme('colors.lotgd.200')}`,
        border: `2px solid ${theme('colors.lotgd.400')}`,
        height: theme('height.6'),
        width: theme('height.6'),
        borderRadius: '50%',
        background: theme('colors.lotgd.500'),
        cursor: 'pointer',
    }

    return {
        // <input type="color">
        // <input type="file">
        // <input type="image">

        //- Inputs fields
        "[type='text']": input,
        "[type='email']": input,
        "[type='url']": input,
        "[type='password']": input,
        "[type='number']": input,
        "[type='date']": input,
        "[type='datetime-local']": input,
        "[type='month']": input,
        "[type='search']": input,
        "[type='tel']": input,
        "[type='time']": input,
        "[type='week']": input,
        'textarea': input,
        'select': input,

        //-- Range input - https://www.cssportal.com/style-input-range/
        "[type='range']": {
            height: theme('height.7'),
            appearance: 'none',
            width: '100%',
            backgroundColor: 'transparent',

            '&:focus': {
                outline: 'none',

                // '&::-webkit-slider-runnable-track': {
                //     background: '#205928'
                // },

                // '&::-ms-fill-lower, &::-ms-fill-upper': {
                //     background: '#205928'
                // }
            },

            '&::-webkit-slider-runnable-track': inputRangeTrack,

            '&::-webkit-slider-thumb': {
                ...inputRangeThumb,
                '-webkit-appearance': 'none',
                marginTop: '-5px',
            },

            '&::-moz-range-track': inputRangeTrack,

            '&::-moz-range-thumb': inputRangeThumb,

            '&::-ms-track': {
                width: '100%',
                height: '12px',
                cursor: 'pointer',
                animate: '0.2s',
                background: 'transparent',
                borderColor: 'transparent',
                color: 'transparent',
            },

            '&::-ms-fill-lower, &::-ms-fill-upper': {
                backgroundColor: '#205928',
                border: '1px solid #18D501',
                borderRadius: '2px',
                boxShadow: '1px 1px 1px #002200',
            },

            '&::-ms-thumb': {
                marginTop: '1px',
                boxShadow: '3px 3px 3px #00AA00',
                border: '2px solid #83E584',
                height: '23px',
                width: '23px',
                borderRadius: '23px',
                backgroundColor: '#439643',
                cursor: 'pointer',
            },
        },

        //-- Radio
        "[type='radio']:not(.unstyle)": radioCheckboxBase('f111', 'f058'),

        //-- Checkbox
        "[type='checkbox']:not(.unstyle)": radioCheckboxBase('f0c8', 'f14a'),

        //-- Checkbox toggle style
        '.toggle-path': {
            transition: 'background 0.3s ease-in-out'
        },
        '.toggle-circle': {
            top: '0.2rem',
            left: '0.25rem',
            transition: 'all 0.3s ease-in-out'
        },
        "[type='checkbox']:checked": {
            '~ .toggle-circle': {
                transform: 'translateX(100%)'
            },
            '~ .toggle-path': {
                backgroundColor: theme('colors.lotgd.500')
            }
        },
        "[type='checkbox']:disabled": {
            '~ .toggle-circle': {
                backgroundColor: theme('colors.gray.300')
            },
            '~ .toggle-path': {
                backgroundColor: theme('colors.gray.300')
            }
        },

        //-- Buttons
        "[type='button']:not(.unstyle)": button,
        "[type='submit']": button,
        "[type='reset']": button,

        //-- Fields
        form: {
            fieldset: fieldset,
            '.field': fieldset,
        }
    }
}
