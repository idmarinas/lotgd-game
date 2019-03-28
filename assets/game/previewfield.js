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
    Lotgd.previewfield = function (target, player, maxchars, talkline, youhave)
    {
        target = jQuery(target)
        let message = target.val()
        let length = message.length
        let charsleft = maxchars - length
        let color = (charsleft > 0) ? 'green' : (charsleft === 0) ? 'orange' : 'red'
        let charsLeftText = youhave.replace('%s', charsleft)
        let playerName = Lotgd.appoencode(player)

        if (length === 0)
        {
            jQuery('#previewtext-commentary-form').parent().addClass('hidden').removeClass('red orange green')
            jQuery('#commentary-form input').attr('style', '')
            jQuery('#commentary-form button').removeClass('top attached')

            return
        }

        jQuery('#charsleft-commentary-form').html(charsLeftText)

        let text = '<i class="eye icon"></i>'

        if (message.substr(0, 2) === '::')
        {
            text += '<span class="colLtWhite">' + playerName + '</span> '
            text += '<span class="colLtBlack">' + Lotgd.appoencode(message.replace(message.substr(0, 2), '')) + '</span>'
        }
        else if (message.substr(0, 1) === ':')
        {
            text += '<span class="colLtWhite">' + playerName + '</span> '
            text += '<span class="colLtBlack">' + Lotgd.appoencode(message.replace(message.substr(0, 1), '')) + '</span>'
        }

        else if (message.substr(0, 3) === '/me')
        {
            text += '<span class="colLtWhite">' + playerName + '</span> '
            text += '<span class="colLtBlack">' + Lotgd.appoencode(message.replace(message.substr(0, 3), '')) + '</span>'
        }
        else if (message.substr(0, 5) === '/game') { text += '<span class="colDkMagenta">' + Lotgd.appoencode(message.replace(message.substr(0, 5), '')) + '</span>' }
        else
        {
            text += '<span class="colLtWhite">' + playerName + '</span> '
            text += '<span class="colDkCyan">' + Lotgd.appoencode(talkline) + ',</span> "' + Lotgd.appoencode(message) + '"</span>'
        }

        jQuery('#previewtext-commentary-form').html(text).parent().removeClass('hidden').removeClass('red orange green').addClass(color)
        jQuery('#commentary-form input').attr('style', 'border-bottom-left-radius: 0 !important;')
        jQuery('#commentary-form button').addClass('top attached')
    }

    return Lotgd
})
