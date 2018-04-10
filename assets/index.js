define([
    './core',

    //-- Functions used in LOTGD
    './game/md5passregister',
    './game/md5passlogin',
    './game/datacache',
    './game/previewfield',
    './game/loadnewchat',
    './game/recommended-modules',

    //-- Components
    './components/redirect-post',
    './components/swal',
    './components/notify',
    './components/confirmation',

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
    'sweetalert2.css',
    'toastr.css'
], function (Lotgd)
{
    'use strict'

    return Lotgd
})
