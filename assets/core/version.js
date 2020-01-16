define([
    '../var/arr'
], function (arr)
{
    'use strict'

    arr.version = {

        version: '0.2.0',

        get ()
        {
            return String(this.version)
        }
    }

    return arr
})
