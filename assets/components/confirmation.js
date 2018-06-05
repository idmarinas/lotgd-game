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
     * @param {Boolean} form Indicate if is a form
     */
    Lotgd.confirm = function (element, event, form = false)
    {
        event.preventDefault()

        element = jQuery(element)
        var options = element.data('options')
        options = jQuery.extend({type: 'question', showCancelButton: true}, options)

        var success = function ()
        {
            if (form === false)
            {
                window.location = element.href

                return window.location
            }
            else
            {
                element.parent('form').submit()
            }
        }

        Lotgd.swal(options).then(success, function () {})
    }
})
