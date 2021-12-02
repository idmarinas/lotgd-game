module.exports = {
    important: true,
    purge: {
        // enabled: true,
        content: [
            './themes/**/*.html',
            './themes/**/*.twig',
            './assets/**/*.js',
        ],
        safelist: []
    },
    darkMode: 'media', // or 'media' or 'class'
    theme: {
        extend: {},
    },
    variants: {
        extend: {},
    },
    plugins: [
        //-- Default App Theme (Can change this theme for your own)
        require('./assets/styles/tailwind.theme.app') //-- Can use as example for generate your theme
    ],
}
