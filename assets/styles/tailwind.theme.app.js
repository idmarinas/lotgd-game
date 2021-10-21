const plugin = require('tailwindcss/plugin')

// addUtilities(), for registering new utility styles
// addComponents(), for registering new component styles
// addBase(), for registering new base styles
// addVariant(), for registering custom variants
// e(), for escaping strings meant to be used in class names
// prefix(), for manually applying the user's configured prefix to parts of a selector
// theme(), for looking up values in the user's theme configuration
// variants(), for looking up values in the user's variants configuration
// config(), for looking up values in the user's Tailwind configuration
// postcss, for doing low-level manipulation with PostCSS directly

module.exports = plugin(({addComponents, addBase, e, theme}) => {
    addBase(require('./app/base/index')(theme, e))
    addComponents(require('./app/components/index')(theme, e))
}, {
    theme: {
        extend: {
            colors: {
                'lotgd-gray': {
                    50: "#f2f3f2",
                    100: "#e5e6e5",
                    200: "#caceca",
                    300: "#b0b5b0",
                    400: "#959d95",
                    500: "#7b847b",
                    600: "#626a62",
                    700: "#4a4f4a",
                    800: "#313531",
                    900: "#191a19"
                },
                'lotgd-green': {
                    50: "#e5ffe5",
                    100: "#ccffcc",
                    200: "#99ff99",
                    300: "#66ff66",
                    400: "#33ff33",
                    500: "#00ff00",
                    600: "#00cc00",
                    700: "#009900",
                    800: "#006600",
                    900: "#003300"
                },
                'lotgd-teal': {
                    50: "#e5fff2",
                    100: "#ccffe6",
                    200: "#99ffcc",
                    300: "#66ffb3",
                    400: "#33ff99",
                    500: "#00ff80",
                    600: "#00cc66",
                    700: "#00994d",
                    800: "#006633",
                    900: "#00331a"
                },
                'lotgd-cyan': {
                    50: "#e5ffff",
                    100: "#ccffff",
                    200: "#99ffff",
                    300: "#66ffff",
                    400: "#33ffff",
                    500: "#00ffff",
                    600: "#00cccc",
                    700: "#009999",
                    800: "#006666",
                    900: "#003333"
                },
                'lotgd-blue': {
                    50: "#e5f2ff",
                    100: "#cce5ff",
                    200: "#99ccff",
                    300: "#66b2ff",
                    400: "#3399ff",
                    500: "#007fff",
                    600: "#0066cc",
                    700: "#004c99",
                    800: "#003366",
                    900: "#001933"
                },
                'lotgd-indigo': {
                    50: "#e5e5ff",
                    100: "#ccccff",
                    200: "#9999ff",
                    300: "#6666ff",
                    400: "#3333ff",
                    500: "#0000ff",
                    600: "#0000cc",
                    700: "#000099",
                    800: "#000066",
                    900: "#000033"
                },
                'lotgd-violet': {
                    50: "#f2e5ff",
                    100: "#e5ccff",
                    200: "#cc99ff",
                    300: "#b266ff",
                    400: "#9933ff",
                    500: "#7f00ff",
                    600: "#6600cc",
                    700: "#4c0099",
                    800: "#330066",
                    900: "#190033"
                },
                'lotgd-purple': {
                    50: "#ffe5ff",
                    100: "#ffccff",
                    200: "#ff99ff",
                    300: "#ff66ff",
                    400: "#ff33ff",
                    500: "#ff00ff",
                    600: "#cc00cc",
                    700: "#990099",
                    800: "#660066",
                    900: "#330033"
                },
                'lotgd-pink': {
                    50: "#ffe5f2",
                    100: "#ffcce6",
                    200: "#ff99cc",
                    300: "#ff66b3",
                    400: "#ff3399",
                    500: "#ff0080",
                    600: "#cc0066",
                    700: "#99004d",
                    800: "#660033",
                    900: "#33001a"
                },
                'lotgd-red': {
                    50: "#ffe5e5",
                    100: "#ffcccc",
                    200: "#ff9999",
                    300: "#ff6666",
                    400: "#ff3333",
                    500: "#ff0000",
                    600: "#cc0000",
                    700: "#990000",
                    800: "#660000",
                    900: "#330000"
                },
                'lotgd-orange': {
                    50: "#fff2e5",
                    100: "#ffe6cc",
                    200: "#ffcc99",
                    300: "#ffb366",
                    400: "#ff9933",
                    500: "#ff8000",
                    600: "#cc6600",
                    700: "#994c00",
                    800: "#663300",
                    900: "#331a00"
                },
                'lotgd-yellow': {
                    50: "#ffffe5",
                    100: "#ffffcc",
                    200: "#ffff99",
                    300: "#ffff66",
                    400: "#ffff33",
                    500: "#ffff00",
                    600: "#cccc00",
                    700: "#999900",
                    800: "#666600",
                    900: "#333300"
                },
                'lotgd-lime': {
                    50: "#f2ffe5",
                    100: "#e6ffcc",
                    200: "#ccff99",
                    300: "#b3ff66",
                    400: "#99ff33",
                    500: "#80ff00",
                    600: "#66cc00",
                    700: "#4d9900",
                    800: "#336600",
                    900: "#1a3300"
                },
                lotgd:
                {
                    50: '#dbffdb',
                    100: '#bdffbd',
                    200: '#70ff70',
                    300: '#00eb00',
                    400: '#00c700',
                    500: '#00a300',
                    600: '#007a00',
                    700: '#006100',
                    800: '#005200',
                    900: '#004700',
                    DEFAULT: '#003800',
                },
                //-- Colors of format text
                'col-dk-blue': '#0000FF',
                'col-dk-green': '#00B000',
                'col-dk-cyan': '#00B0B0',
                'col-dk-red': '#BB0000',
                'col-dk-magenta': '#B000CC',
                'col-dk-yellow': '#B0B000',
                'col-dk-white': '#B0B0B0',
                'col-lt-blue': '#6262FF',
                'col-lt-green': '#00FF00',
                'col-lt-cyan': '#00FFFF',
                'col-lt-red': '#FF2222',
                'col-lt-magenta': '#FF00FF',
                'col-lt-yellow': '#FFFF00',
                'col-lt-white': '#FFFFFF',
                'col-lt-black': '#777777',
                'col-dk-orange': '#995500',
                'col-lt-orange': '#FF9900',
                'col-blue': '#0070FF',
                'col-lime': '#DDFFBB',
                'col-black': '#000000',
                'col-rose': '#EEBBEE',
                'col-blueviolet': '#9A5BEE',
                'col-iceviolet': '#AABBEE',
                'col-lt-brown': '#F8DB83',
                'col-dk-brown': '#6b563f',
                'col-x-lt-green': '#aaff99',
                'col-lt-link-blue': '#0099FF',
                'col-dk-link-blue': '#006BB3',
                'col-dk-rust': '#8D6060',
                'col-lt-rust': '#B07878',
                'col-md-blue': '#0000F0',
                'col-md-grey': '#DDDDDD',
                'col-beige': '#F5F5DC',
                'col-khaki': '#F0E68C',
                'col-darkkhaki': '#BDB76B',
                'col-aquamarine': '#7FFFD4',
                'col-darkseagreen': '#8FBC8F',
                'col-lightsalmon': '#FFA07A',
                'col-salmon': '#FA8072',
                'col-wheat': '#F5DEB3',
                'coltan': '#D2B48C',
                'col-burlywood': '#DEB887',
            }
        },
    },
    variants: {
        extend: {
            opacity: ['disabled'],
            cursor: ['disabled'],
            borderWidth: ['first'],
            margin: ['first'],
            ringWidth: ['hover']
        },
    },
})
