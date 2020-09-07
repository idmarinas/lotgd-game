define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    var lotgdModalFormId = 0

    /**
     * @lotgd function
     * @name Lotgd.modalForm
     * @kind function
     *
     * @description Same as the modal, but this assumes that it has a form inside with its own buttons
     *
     * @param {string} message Text for modal
     * @param {string} title Title of modal
     * @param {string} id ID for modal
     * @param {string} options Options for modal
     */
    Lotgd.modalForm = function (message, title, id, options)
    {
        options = options || {}
        options = jQuery.extend({ size: '', denyButton: true }, options)

        let modalId = ''
        if (id) { modalId = id }
        else { modalId = lotgdModalFormId++ }

        modalId = 'modal-' + modalId
        if (!jQuery('#' + modalId).length)
        {
            const template = '<div id="' + modalId + '" class="ui modal ' + options.size + '"><i class="close icon"></i>' +
            (title !== undefined && title !== '' ? '<div class="header">' + title + '</div>' : '') +
            '<div class="content">' + message + '</div>' +
            '</div>'

            jQuery(template).appendTo('body')
        }

        jQuery('#' + modalId).modal(options).modal('show')
    }

    return Lotgd
})
