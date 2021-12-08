module.exports = function (theme, e)
{
    return {
        ...require('./base')(theme, e),
        ...require('./links')(theme, e),
        ...require('./table')(theme, e),
    }
}
