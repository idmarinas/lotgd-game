define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    let timer = {}

    /**
     * @lotgdDoc function
     * @name Lotgd.loadnewchat
     * @kind function
     *
     * @description Function for preview a field
     *
     * @param {Object} type
     */
    Lotgd.loadnewchat = function (target, section, message, limit, talkline, returnlink, timeout)
    {
        if (timer[section] === undefined) timer[section] = 0
        else timer[section]++

        if (timer[section] >= 100)
        {
            jQuery('#ajaxcommentarynoticediv' + section).text(timeout)

            return
        }

        jQuery.get('ajaxcommentary.php?section=' + section + '&message=' + message + '&limit=' + limit + '&talkline=' + talkline + '&returnlink=' + returnlink)
            .done(function (data)
            {
                jQuery('#' + target).html(data)
            })
    }

    return Lotgd
})
