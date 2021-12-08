module.exports = function (theme, e)
{
    return {
        ...require('./form')(theme, e),
        ...require('./lotgd')(theme, e),
        ...require('./pagination')(theme, e),
        ...require('./tooltip')(theme, e),
    }
}
