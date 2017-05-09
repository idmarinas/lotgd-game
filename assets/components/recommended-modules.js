define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    /**
     * Select all recommended modules
     */
    Lotgd.recommendedModules = function ()
    {
        jQuery(':radio[data-recommended]').each(function ()
        {
            jQuery(this).prop('checked', true)
        })
    }

    return Lotgd
})
