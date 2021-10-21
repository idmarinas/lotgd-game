define([
    '../core',
    '../external/swal'
], function (Lotgd, Swal)
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
    Lotgd.datacache = function (cache, type, JaxonLotgd)
    {
        let options = {}
        if (type === 'optimize')
        {
            options = optimize(cache, JaxonLotgd)
        }
        else if (type === 'clearexpire')
        {
            options = clearexpire(cache, JaxonLotgd)
        }
        else if (type === 'clearall')
        {
            options = clearall(cache, JaxonLotgd)
        }
        else if (type === 'clearbyprefix')
        {
            options = clearbyprefix(cache, JaxonLotgd)
        }
        else
        {
            return
        }

        options = Object.assign({ icon: 'question', showCancelButton: true }, options)
        Swal.configChange(options)
        Swal.get().fire(options)
    }

    function optimize (cache, JaxonLotgd)
    {
        return {
            title: 'Optimize data cache',
            confirmButtonText: 'Yes, optimize',
            text: `Want optimize data cache for "${cache}"?`,
            preConfirm: () =>
            {
                return JaxonLotgd.Ajax.Core.Cache.optimize(cache)
            }
        }
    }

    function clearexpire (cache, JaxonLotgd)
    {
        return {
            title: 'Clear expire data cache',
            confirmButtonText: 'Yes, clear expire',
            text: `Want clear expire data cache for "${cache}"?`,
            preConfirm: () =>
            {
                return JaxonLotgd.Ajax.Core.Cache.clearexpire(cache)
            }
        }
    }

    function clearall (cache, JaxonLotgd)
    {
        return {
            title: 'Clear all data cache',
            confirmButtonText: 'Yes, clear all',
            text: `This action empty the cache directory of "${cache}", Want clear all data cache?`,
            preConfirm: () =>
            {
                return JaxonLotgd.Ajax.Core.Cache.clearall(cache)
            }
        }
    }

    function clearbyprefix (cache, JaxonLotgd)
    {
        return {
            title: 'Clear data cache by prefix',
            confirmButtonText: 'Yes, clear this',
            text: `Type the prefix of the cache data to be deleted for "${cache}"`,
            input: 'text',
            preConfirm: prefix =>
            {
                return JaxonLotgd.Ajax.Core.Cache.clearbyprefix(cache, prefix)
            }
        }
    }

    return Lotgd
})
