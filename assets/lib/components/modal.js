define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    let lotgdModalId = 0

    /**
     * @lotgd function
     * @name Lotgd.modal
     * @kind function
     *
     * @description Show a modal
     *
     * @param {string} message Text for modal
     * @param {string} title Title of modal
     * @param {string} id ID for modal
     * @param {string} options Options for modal
     */
    Lotgd.modal = function (message, title, id, options)
    {
        options = options || {}
        options = jQuery.extend({ size: '', denyButton: true, approveButton: false, closeIcon: true, contentClass: '' }, options)

        let modalId = ''
        if (id) { modalId = id }
        else { modalId = lotgdModalId++ }
        modalId = 'modal-' + modalId

        //-- Force to redo the modal
        if (options.force !== undefined && options.force === true)
        {
            jQuery('#' + modalId).remove()
        }

        if (!jQuery('#' + modalId).length)
        {
            const template = '<div id="' + modalId + '" class="ui modal ' + options.size + '">' +
            (options.closeIcon ? '<i class="close icon"></i>' : '') +
            (title !== undefined && title !== '' ? '<div class="header">' + title + '</div>' : '') +
            '<div class="scrolling ' + options.contentClass + ' content">' + message + '</div>' +
            '<div class="actions">' +
                (options.denyButton ? '<div class="ui red cancel button">Cancelar</div>' : '') +
                (options.approveButton ? '<div class="ui green ok button">Ok</div>' : '') +
            '</div></div>'

            jQuery(template).appendTo('body')
        }

        delete options.force
        delete options.size
        delete options.closeIcon
        delete options.contentClass

        jQuery('#' + modalId).modal(options).modal('show')
    }

    return Lotgd
})
