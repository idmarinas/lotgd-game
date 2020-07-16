define([
    '../core',
    '../external/jquery',
    '../external/swal'
], function (Lotgd, jQuery, Swal)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.datacache
     * @kind function
     *
     * @description Function of storage cache
     *
     * @param {string} cache Name of cache
     * @param {string} type optimize|clearexpire|clearall|clearbyprefix
     */
    Lotgd.datacache = function (cache, type)
    {
        let options = {}
        if (type === 'optimize')
        {
            options = optimize(cache)
        }
        else if (type === 'clearexpire')
        {
            options = clearexpire(cache)
        }
        else if (type === 'clearall')
        {
            options = clearall(cache)
        }
        else if (type === 'clearbyprefix')
        {
            options = clearbyprefix(cache)
        }
        else
        {
            return
        }

        options = jQuery.extend({ icon: 'question', showLoaderOnConfirm: true, showCancelButton: true }, options)
        Swal.configChange(options)
        Swal.get().fire(options)
    }

    function optimize (cache)
    {
        return {
            title: 'Optimize data cache',
            confirmButtonText: 'Yes, optimize',
            text: `Want optimize data cache for "${cache}"?`,
            preConfirm: () =>
            {
                return jqueryGet(`ajaxdatacache.php?op=optimize&cache=${cache}`)
            }
        }
    }

    function clearexpire (cache)
    {
        return {
            title: 'Clear expire data cache',
            confirmButtonText: 'Yes, clear expire',
            text: `Want clear expire data cache for "${cache}"?`,
            preConfirm: () =>
            {
                return jqueryGet(`ajaxdatacache.php?op=clearexpire&cache=${cache}`)
            }
        }
    }

    function clearall (cache)
    {
        return {
            title: 'Clear all data cache',
            confirmButtonText: 'Yes, clear all',
            text: `This action empty the cache directory of "${cache}", Want clear all data cache?`,
            preConfirm: () =>
            {
                return jqueryGet(`ajaxdatacache.php?op=clearall&cache=${cache}`)
            }
        }
    }

    function clearbyprefix (cache)
    {
        return {
            title: 'Clear data cache by prefix',
            confirmButtonText: 'Yes, clear this',
            text: `Type the prefix of the cache data to be deleted for "${cache}"`,
            input: 'text',
            preConfirm: (prefix) =>
            {
                return jqueryGet(`ajaxdatacache.php?op=clearbyprefix&cache=${cache}&prefix=${prefix}`)
            }
        }
    }

    /**
     * Pattern function to execute a url
     *
     * @param {string} url Url to execute
     */
    function jqueryGet (url)
    {
        return jQuery.get(url).then(response =>
        {
            if (response.trim() !== 'ok')
            {
                throw new Error(response.statusText)
            }

            return response
        })
            .catch(error =>
            {
                Swal.get().showValidationMessage(error)
            })
    }

    return Lotgd
})
