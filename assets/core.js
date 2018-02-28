define([
    './var/arr',

    './core/version'
], function (arr)
{
    'use strict'

    var Lotgd = {}

    /**
     * @LotgdDoc function
     * @name Lotgd.toCamelCase
     * @kind function
     *
     * @description Converts a string to the camelCase format
     *
     * @param {string} string String to format
     *
     * @return {string}
     */
    Lotgd.toCamelCase = function (string)
    {
        return string.replace(/^([A-Z])|[\s-_](\w)/g, function (match, p1, p2, offset)
        {
            if (p2) return p2.toUpperCase()
            return p1.toLowerCase()
        })
    }

    /**
     * @LotgdDoc function
     * @name Lotgd.set
     * @kind function
     *
     * @description Set value for a param
     *
     * @param {string} param Name of param
     * @param {mix} value Value of param
     *
     */
    Lotgd.set = function (param, value)
    {
        var name = Lotgd.toCamelCase(param)

        if (name in arr && typeof arr[name].set === 'function')
        {
            arr[name].set(value)
        }
        else
        {
            arr[name] = {
                value: '',
                set (value)
                {
                    this.value = String(value)
                },

                get (param)
                {
                    if (param === undefined) param = ''

                    return String(this.value + param)
                }
            }

            arr[name].set(value)
        }
    }

    /**
     * @LotgdDoc function
     * @name Lotgd.get
     * @kind function
     *
     * @description Get value of param
     *
     * @param {string} param Name of param
     * @param {string} string Default value
     *
     * @return {mix}
     */
    Lotgd.get = function (param, string)
    {
        var name = Lotgd.toCamelCase(param)

        if (name in arr && typeof arr[name].get === 'function')
        {
            return arr[name].get(string)
        }
        else return undefined
    }

    return Lotgd
})
