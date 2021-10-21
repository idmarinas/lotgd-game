/* global hex_md5 */
define([
    '../core',
    '../external/jquery',
    '../var/document'
], function (Lotgd, jQuery, document)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.md5PassRegister
     * @kind function
     *
     * @description Function for encode pass in register form
     *
     * @param {Object} type
     */
    Lotgd.md5PassRegister = function ()
    {
        //-- Load script
        jQuery.getScript('js/md5.js', function ()
        {
            // encode passwords
            const plen = document.getElementById('passlen')
            const pass1 = document.getElementById('pass1')
            plen.value = pass1.value.length

            if (pass1.value.substring(0, 5) !== '!md5!')
            {
                pass1.value = '!md5!' + hex_md5(pass1.value)
            }

            const pass2 = document.getElementById('pass2')
            if (pass2.value.substring(0, 5) !== '!md5!')
            {
                pass2.value = '!md5!' + hex_md5(pass2.value)
            }
        })
    }

    return Lotgd
})
