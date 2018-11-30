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
        let options = {}
        if (type === 'optimize')
        {
            options = optimize()
        }
        else if (type === 'clearexpire')
        {
            options = clearexpire()
        }
        else if (type === 'clearall')
        {
            options = clearall()
        }
        else if (type === 'clearbyprefix')
        {
            options = clearbyprefix()
        }
        else
        {
            return
        }

        Lotgd.swal(jQuery.extend({ type: 'question', showLoaderOnConfirm: true, showCancelButton: true }, options))
    }

    function optimize ()
    {
        return {
            title: 'Optimize data cache',
            confirmButtonText: 'Yes, optimize',
            text: 'Want optimize data cache of game?',
            preConfirm: () =>
            {
                return jQuery.get('ajaxdatacache.php?op=optimize')
            }
        }
    }

    function clearexpire ()
    {
        return {
            title: 'Clear expire data cache',
            confirmButtonText: 'Yes, clear expire',
            text: 'Want clear expire data cache of game?',
            preConfirm: () =>
            {
                return jQuery.get('ajaxdatacache.php?op=clearexpire')
            }
        }
    }

    function clearall ()
    {
        return {
            title: 'Clear all data cache',
            confirmButtonText: 'Yes, clear all',
            text: 'This action empty the cache directory including the template directory, Want clear all data cache of game?',
            preConfirm: () =>
            {
                return jQuery.get('ajaxdatacache.php?op=clearall')
            }
        }
    }

    function clearbyprefix ()
    {
        return {
            title: 'Clear data cache by prefix',
            confirmButtonText: 'Yes, clear this',
            text: 'Type the prefix of the cache data to be deleted',
            input: 'text',
            preConfirm: (prefix) =>
            {
                return jQuery.get('ajaxdatacache.php?op=clearbyprefix&prefix=' + prefix)
            }
        }
    }

    return Lotgd
})
