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
        options = jQuery.extend({ type: 'question', showCancelButton: true }, options)

        Lotgd.swal(options).then(result =>
        {
            if (result.value)
            {
                if (form === false)
                {
                    window.location = element.attr('href')

                    return window.location
                }
                else
                {
                    return element.parents('form').submit()
                }
            }
        })
    }
})
