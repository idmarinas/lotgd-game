module.exports = {
    important: true,
    content: [
        './assets/**/*.js',
        './src/**/*.php',
        './templates/**/*.{html,twig}',
        './themes/**/*.{html,twig}',
    ],
    darkMode: 'media', // or 'media' or 'class'
    theme: {
        extend: {},
    },
    plugins: [
        //-- Default App Theme (Can change this theme for your own)
        require('./assets/styles/tailwind.theme.app') //-- Can use as example for generate your theme
    ],
}
