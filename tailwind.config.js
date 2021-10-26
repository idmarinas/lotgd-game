module.exports = {
    important: true,
    purge: [
        './themes/**/*.html',
        './themes/**/*.twig',
        './assets/**/*.js',
    ],
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
