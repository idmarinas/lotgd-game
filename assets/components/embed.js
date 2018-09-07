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
     */
    Lotgd.embed = function (element)
    {
        event.preventDefault()

        element = jQuery(element)
        let elementId = element.attr('id')
        let url = element.attr('href')
        let force = Boolean(element.data('force'))
        let size = String(element.data('size'))
        let options = {}
        options = jQuery.extend({ size: size, denyButton: true, approveButton: false, force: force, closeIcon: false, contentClass: 'embed' }, options)

        let template = '<iframe id="iframe-' + elementId + '" src="' + url + '" width="100%" height="100%" frameborder="0"></iframe>'

        Lotgd.modal(template, undefined, elementId, options)
    }
})
