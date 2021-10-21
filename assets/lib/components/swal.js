/* eslint-disable no-new-func */
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

        //-- Old functions
        const funcsOld = ['onBeforeOpen', 'onOpen,', 'onRender', 'onClose', 'onAfterClose', 'onDestroy']
        //-- New functions
        const funcsNew = ['willOpen', 'didOpen', 'didRender', 'willClose', 'didClose', 'didDestroy']
        const funcs = ['preConfirm', ...funcsOld, ...funcsNew]

        for (const funcId in funcs)
        {
            let func = funcs[funcId]
            // Replace old for new function
            if (funcsOld.includes(func))
            {
                func = funcsNew[funcsOld.indexOf(func)]
            }

            if (options[func])
            {
                options[func] = new Function('element', options[func])
            }
        }

        const modal = swal.get().fire(options)

        swal.configRestart()

        return modal
    }

    return Lotgd
})
