define([
    '../core',
    '../external/jquery'
], function (Lotgd)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.escapeRegex
     * @kind function
     *
     * @description Function for RegExp patterns
     *
     * @param {Object} type
     */
    Lotgd.escapeRegex = function (value)
    {
        return value.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&')
    }

    return Lotgd
})
