define([
    '../core',
    '../external/toastr'
], function (Lotgd, toastr)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.notify
     * @kind function
     *
     * @description Show notification to user.
     *
     * @param {Object} options Options for notification
     */
    Lotgd.notify = function (options)
    {
        const type = options.type
        const message = options.message
        const title = options.title

        delete options.type
        delete options.message
        delete options.title

        toastr.configChange(options)

        if (type === 'success')
        {
            toastr.get().success(message, title)
        }
        else if (type === 'error' || type === 'danger')
        {
            toastr.get().error(message, title)
        }
        else if (type === 'warning')
        {
            toastr.get().warning(message, title)
        }
        else
        {
            toastr.get().info(message, title)
        }

        toastr.configRestart()
    }

    return Lotgd
})
