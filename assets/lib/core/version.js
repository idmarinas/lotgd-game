define([
    '../var/arr'
], function (arr)
{
    'use strict'

    arr.version = {

        version: '0.3.0',

        get ()
        {
            return String(this.version)
        }
    }

    return arr
})
