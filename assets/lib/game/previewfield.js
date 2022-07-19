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
        let color = (charsleft > 0) ? 'border-green-500' : 'border-red-500'
        color = (charsleft === 0) ? 'border-orange-500' : color
        const charsLeftText = youhave.replace('%s', escape(charsleft))
        const playerName = Lotgd.appoencode(player)

        if (length === 0)
        {
            jQuery('#previewtext-commentary-form').parent().addClass('hidden').removeClass('border-red-500 border-orange-500 border-green-500')

            return
        }

        jQuery('#charsleft-commentary-form').html(charsLeftText)

        const commandMe1 = message.substr(0, 2)
        const commandMe2 = message.substr(0, 1)
        const commandMe3 = message.substr(0, 3)
        const commandGame = message.substr(0, 5)

        let text = '<i class="far fa-eye"></i>'

        if (commandMe1 === '::' || commandMe2 === ':' || commandMe3 === '/me')
        {
            text += '<span class="text-col-lt-black">' + playerName + '</span> '
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

            text += '<span class="text-col-lt-black">' + colorizeMenssage + '</span>'
        }
        else if (commandGame === '/game') { text += '<span class="text-col-dk-magenta">' + Lotgd.appoencode(message.replace(commandGame, '')) + '</span>' }
        else
        {
            text += '<span class="text-col-lt-black">' + playerName + '</span> '
            text += '<span class="text-col-dk-cyan">' + Lotgd.appoencode(talkline) + ',</span> "' + Lotgd.appoencode(message) + '"</span>'
        }

        jQuery('#previewtext-commentary-form').html(text).parent().removeClass('hidden').removeClass('border-red-500 border-orange-500 border-green-500').addClass(color)
    }

    return Lotgd
})
