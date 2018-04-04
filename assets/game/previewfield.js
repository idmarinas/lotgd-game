define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.previewfield
     * @kind function
     *
     * @description Function for preview a field
     *
     * @param {Object} type
     */
    Lotgd.previewfield = function (target, maxchars, name, startdiv, talkline, showcharsleft, youhave)
    {
        target = jQuery(target)
        let message = target.val()
        let length = message.length
        let charsleft = maxchars - length

        if (length === 0)
        {
            jQuery('#previewtext' + name).addClass('hidden')
            jQuery('#charsleft' + name).addClass('hidden').removeClass('red orange green')

            return
        }

        if (showcharsleft === 1)
        {
            let color = (charsleft > 0) ? 'green' : (charsleft === 0) ? 'orange' : 'red'
            let charsLeftText = '<span class="' + color + '">' + youhave.replace('%s', charsleft) + '</span>'

            jQuery('#charsleft' + name).removeClass('hidden red orange green').addClass(color).html(charsLeftText)
        }

        let text = '<i class="eye icon"></i> '
        if (startdiv !== '') { text += '<span class="colLtWhite">' + Lotgd.appoencode(startdiv) + '</span> ' }

        if (message.substr(0, 2) === '::') { text += '<span class="colLtBlack">' + Lotgd.appoencode(message.replace(message.substr(0, 2), '')) + '</span>' }
        else if (message.substr(0, 1) === ':') { text += '<span class="colLtBlack">' + Lotgd.appoencode(message.replace(message.substr(0, 1), '')) + '</span>' }
        else if (message.substr(0, 3) === '/me') { text += '<span class="colLtBlack">' + Lotgd.appoencode(message.replace(message.substr(0, 3), '')) + '</span>' }
        else { text += '<span class="colDkCyan">' + Lotgd.appoencode(talkline) + ', </span>' + Lotgd.appoencode(message) + '</span>' }

        jQuery('#previewtext' + name).removeClass('hidden').html(text)
    }

    return Lotgd
})
