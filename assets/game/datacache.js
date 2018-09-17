define([
    '../core',
    '../external/jquery'
], function (Lotgd, jQuery)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.datacache
     * @kind function
     *
     * @description Function of storage cache
     *
     * @param {Object} type optimize|clearexpire|clearall|clearbyprefix
     */
    Lotgd.datacache = function (type)
    {
        if (type === 'optimize')
        {
            Lotgd.swal({
                type: 'question',
                title: 'Optimize data cache',
                confirmButtonText: 'Yes, optimize',
                text: 'Want optimize data cache of game?',
                showLoaderOnConfirm: true,
                showCancelButton: true,
                preConfirm: () =>
                {
                    return jQuery.get('ajaxdatacache.php?op=optimize')
                }
            })
        }
        else if (type === 'clearexpire')
        {
            Lotgd.swal({
                type: 'question',
                title: 'Clear expire data cache',
                confirmButtonText: 'Yes, clear expire',
                text: 'Want clear expire data cache of game?',
                showLoaderOnConfirm: true,
                showCancelButton: true,
                preConfirm: () =>
                {
                    return jQuery.get('ajaxdatacache.php?op=clearexpire')
                }
            })
        }
        else if (type === 'clearall')
        {
            Lotgd.swal({
                type: 'question',
                title: 'Clear all data cache',
                confirmButtonText: 'Yes, clear all',
                text: 'This action empty the cache directory including the template directory, Want clear all data cache of game?',
                showLoaderOnConfirm: true,
                showCancelButton: true,
                preConfirm: function ()
                {
                    return jQuery.get('ajaxdatacache.php?op=clearall')
                }
            })
        }
        else if (type === 'clearbyprefix')
        {
            Lotgd.swal({
                type: 'question',
                title: 'Clear data cache by prefix',
                confirmButtonText: 'Yes, clear this',
                text: 'Type the prefix of the cache data to be deleted',
                input: 'text',
                showLoaderOnConfirm: true,
                showCancelButton: true,
                preConfirm: function (prefix)
                {
                    return jQuery.get('ajaxdatacache.php?op=clearbyprefix&prefix=' + prefix)
                }
            })
        }
    }

    return Lotgd
})
