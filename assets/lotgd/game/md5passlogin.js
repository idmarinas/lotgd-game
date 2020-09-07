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
     * @name Lotgd.md5PassLogin
     * @kind function
     *
     * @description Function for encode pass in register form
     *
     * @param {Object} type
     */
    Lotgd.md5PassLogin = function ()
    {
        //-- Load script
        jQuery.getScript('js/md5.js', function ()
        {
            // encode passwords before submission to protect them even from network sniffing attacks.
            const passbox = document.getElementById('password')
            if (passbox.value.substring(0, 5) !== '!md5!')
            {
                passbox.value = '!md5!' + hex_md5(passbox.value)
            }
        })
    }

    return Lotgd
})
