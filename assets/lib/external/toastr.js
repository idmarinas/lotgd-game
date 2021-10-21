/**
 * Basic configuration for toastr
 */
define([
    'toastr',
    './jquery'
], function (toastr, jQuery)
{
    'use strict'

    const notify = {
        optionsCustom:
        {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
            escapeHtml: true
        },

        initiated: false,

        //-- Change configuration for
        configChange (options)
        {
            options = options || {}

            toastr.options = jQuery.extend({}, this.optionsCustom, options)
        },

        //-- Restart custom configuration
        configRestart ()
        {
            this.configChange()
        },

        //-- Get instance of toastr
        get ()
        {
            return toastr
        },

        //-- Init toastr with custom configuration
        init ()
        {
            if (this.initiated === false)
            {
                this.configChange()
                this.initiated = true
            }
        }
    }

    notify.init()

    return notify
})
