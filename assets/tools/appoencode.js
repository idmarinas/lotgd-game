define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.appoencode
     * @kind function
     *
     * @description Function for add colors to text
     *
     * @param {Object} type
     */
    Lotgd.appoencode = function (message)
    {
        const colors = jQuery.parseJSON(Lotgd.get('colors'))

        for (var x in colors)
        {
            const re = '`' + x
            const pattern = new RegExp(Lotgd.escapeRegex(re), 'g')
            message = message.replace(pattern, '</span><span class="' + colors[x] + '">')
        }
        message = message.replace(/`0/g, '</span>')

        return message
    }

    return Lotgd
})
