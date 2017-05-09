define([
    '../core'
], function (Logtd)
{
    'use strict'

    if (typeof define === 'function' && define.amd)
    {
        define('logtd', [], function ()
        {
            return Logtd
        })
    }
})
