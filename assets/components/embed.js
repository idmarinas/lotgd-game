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

        let template = '<div class="ui embed ' + elementId + '" data-url="' + url + '"></div>'

        Lotgd.modal(template, undefined, elementId, options)
        jQuery('.ui.embed.' + elementId).embed()
    }
})
