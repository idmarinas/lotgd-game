define([
    '../core',
    '@yaireo/tagify'
], function (Lotgd, Tagify)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.tagify
     * @kind function
     *
     * @description Use Tagify libriry
     *
     * @param {object} input
     * @param {object} settings
     */
    Lotgd.tagify = function (input, settings)
    {
        return new Tagify(input, settings)
    }
})
