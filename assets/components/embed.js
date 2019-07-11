define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.embed
     * @kind function
     *
     * @description Show a embed dialog
     *
     * @param {Object} element
     */
    Lotgd.embed = function (element)
    {
        event.preventDefault()

        element = jQuery(element)
        const elementId = element.attr('id')
        const url = element.attr('href')
        const force = Boolean(element.data('force'))
        const size = String(element.data('size'))
        let options = {}
        options = jQuery.extend({ size: size, denyButton: true, approveButton: false, force: force, closeIcon: false, contentClass: 'embed' }, options)

        const template = '<iframe id="iframe-' + elementId + '" src="' + url + '" width="100%" height="100%" frameborder="0" style="height: calc(70vh);"></iframe>'

        Lotgd.modal(template, undefined, elementId, options)
    }
})
