define([
    '../core',
    '@yaireo/tagify'
], function (Lotgd, Tagify)
{
    'use strict'

    // import Tagify from '@yaireo/tagify/src/tagify'

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
