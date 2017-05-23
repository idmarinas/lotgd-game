define([
    '../core',
    '../external/swal'
], function (Lotgd, swal)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.swal
     * @kind function
     *
     * @description Show a modal to user
     *
     * @param {Object} options Options of modal
     */
    Lotgd.swal = function (options)
    {
        if (options !== undefined)
        {
            swal.configChange(options)
        }
        else options = {}

        var modal = swal.get()(options)

        swal.configRestart()

        return modal
    }

    return Lotgd
})
