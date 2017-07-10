define([
    './core',

    //-- Components
    './components/redirect-post',
    './components/recommended-modules',
    './components/swal',
    './components/datacache',
    './components/previewfield',
    './components/loadnewchat',

    //-- Modules
    // ...

    //-- Tools
    './tools/appoencode',
    './tools/escapeRegex',

    //-- Others
    './ready/semantic',

    './exports/amd',
    './exports/global',

    //-- Extra
    'sweetalert2.css'
], function (Lotgd)
{
    'use strict'

    return Lotgd
})
