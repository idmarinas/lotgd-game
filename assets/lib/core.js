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
     * @param {string} str String to format
     *
     * @return {string}
     */
    Lotgd.toCamelCase = function (str)
    {
        const string = str.match(/[a-z]+|\d+/gi)

        return string.map((m, i) =>
        {
            let low = m.toLowerCase();
            if (i != 0)
            {
                low = low.split('').map((s,k) => k==0 ? s.toUpperCase() : s).join``
            }

            return low
        }).join``
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
        const name = Lotgd.toCamelCase(param)

        if (name in arr && typeof arr[name].set === 'function')
        {
            arr[name].set(value)
        }
        else
        {
            arr[name] = {
                value: '',
                set (val)
                {
                    this.value = val
                },

                get (val)
                {
                    if (this.value)
                    {
                        return this.value
                    }

                    return val
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
        const name = Lotgd.toCamelCase(param)

        if (name in arr && typeof arr[name].get === 'function')
        {
            return arr[name].get(string)
        }
        else return undefined
    }

    return Lotgd
})
