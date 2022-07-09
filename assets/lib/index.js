//-- To allow use jQuery and $ in inline script page
const $ = require('jquery')
global.$ = global.jQuery = $

define([
    './core',

    //-- Functions used in LOTGD
    './game/md5passregister',
    './game/md5passlogin',
    './game/previewfield',
    './game/loadnewchat',
    './game/recommended-modules',

    //-- Components
    './components/confirmation',
    './components/notify',
    './components/numeral',
    './components/swal',
    './components/tagify',

    //-- Modules
    // ...

    //-- Tools
    './tools/appoencode',
    './tools/escapeRegex',

    //-- Exports
    './exports/amd',
    './exports/global',

    //-- Extra
    'sweetalert2.css',
    'tagify.scss',
    './css/tagify.css'
    // 'toastr.css'
], function (Lotgd)
{
    'use strict'

    return Lotgd
})
