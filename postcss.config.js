module.exports = {
    plugins: {
        'tailwindcss/nesting': {},
        // include whatever plugins you want
        // but make sure you install these via yarn or npm!
        tailwindcss: {},
        // add browserslist config to package.json (see below)
        autoprefixer: {},
        ...(process.env.NODE_ENV === 'production' ? { cssnano: {} } : {}),
    }
}
