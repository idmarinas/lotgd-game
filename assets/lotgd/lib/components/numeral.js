define([
    '../core',
    '../external/numeral'
], function (Lotgd, Numeral)
{
    'use strict'

    /**
     * @lotgdDoc function
     * @name Lotgd.numeral
     * @kind function
     *
     * @description Use a numeral javascript formating
     */
    Lotgd.numeral = function (number)
    {
        return Numeral(number)
    }

    /**
     * @lotgdDoc function
     * @name Lotgd.duration
     * @kind function
     *
     * @description Humanize with format 00:00:00 a number
     */
    Lotgd.duration = function (number)
    {
        return Numeral(number).format('00:00:00')
    }

    return Lotgd
})
