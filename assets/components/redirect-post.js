define([
    '../core',
    '../external/jquery',
    '../var/document'
], function (Lotgd, jQuery, document)
{
    'use strict'

    /*
    * Function to redirect using post method
    * @var url URL to redirect
    * @var parameters Parameters to send format { param : value }
    */
    Lotgd.redirectPost = function (url, parameters)
    {
        parameters = (typeof parameters === 'undefined') ? {} : parameters

        const form = document.createElement('form')

        jQuery(form).attr('id', 'reg-form')
            .attr('name', 'reg-form')
            .attr('action', url)
            .attr('method', 'post')
            .attr('enctype', 'multipart/form-data')

        jQuery.each(parameters, function (key)
        {
            jQuery(form).append('<input type="text" name="' + key + '" value="' + this + '" />')
        })
        document.body.appendChild(form)
        form.submit()
        document.body.removeChild(form)

        return false
    }

    return Lotgd
})
