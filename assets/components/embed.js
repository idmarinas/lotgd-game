define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.confirm
     * @kind function
     *
     * @description Show a confirmation dialog
     *
     * @param {Object} element
     * @param {Object} event
     * @param {Boolean} form Indicate if is a form
     */
    Lotgd.embed = function (element)
    {
        event.preventDefault()

        element = jQuery(element)
        let elementId = element.attr('id')
        let url = element.attr('href')
        let options = {}
        options = jQuery.extend({size: '', denyButton: true, approveButton: false}, options)

        let template = '<iframe id="iframe-' + elementId + '" src="' + url + '" width="100%" height="100%" frameborder="0"></iframe>'

        Lotgd.modal(template, undefined, elementId, options)
    }
})
