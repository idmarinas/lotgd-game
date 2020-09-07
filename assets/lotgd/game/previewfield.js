define([
    '../core',
    '../external/jquery',
    'lodash/escape'
], function (Lotgd, jQuery, escape)
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
        const message = escape(target.val())
        const length = message.length
        const charsleft = maxchars - length
        const color = (charsleft > 0) ? 'green' : (charsleft === 0) ? 'orange' : 'red'
        const charsLeftText = youhave.replace('%s', escape(charsleft))
        const playerName = Lotgd.appoencode(player)

        if (length === 0)
        {
            jQuery('#previewtext-commentary-form').parent().addClass('hidden').removeClass('red orange green')
            jQuery('#commentary-form input').attr('style', '')
            jQuery('#commentary-form button').removeClass('top attached')

            return
        }

        jQuery('#charsleft-commentary-form').html(charsLeftText)

        const commandMe1 = message.substr(0, 2)
        const commandMe2 = message.substr(0, 1)
        const commandMe3 = message.substr(0, 3)
        const commandGame = message.substr(0, 5)

        let text = '<i class="eye icon"></i>'

        if (commandMe1 === '::' || commandMe2 === ':' || commandMe3 === '/me')
        {
            text += '<span class="colLtWhite">' + playerName + '</span> '
            let colorizeMenssage = ''

            if (commandMe1 === '::')
            {
                colorizeMenssage = Lotgd.appoencode(message.replace(commandMe1, ''))
            }
            else if (commandMe2 === ':')
            {
                colorizeMenssage = Lotgd.appoencode(message.replace(commandMe2, ''))
            }
            else
            {
                colorizeMenssage = Lotgd.appoencode(message.replace(commandMe3, ''))
            }

            text += '<span class="colLtBlack">' + colorizeMenssage + '</span>'
        }
        else if (commandGame === '/game') { text += '<span class="colDkMagenta">' + Lotgd.appoencode(message.replace(commandGame, '')) + '</span>' }
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
