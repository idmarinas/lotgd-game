define([
    '../core',
    '../external/jquery',
    '../var/window'
], function (Lotgd, jQuery, window)
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
     */
    Lotgd.confirm = function (element, event)
    {
        event.preventDefault()

        var options = jQuery(element).data('options')
        options = jQuery.extend({type: 'question', showCancelButton: true}, options)

        var success = function ()
        {
            window.location = element.href

            return window.location
        }

        Lotgd.swal(options).then(success, function () {})
    }
})
