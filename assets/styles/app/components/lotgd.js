module.exports = function (theme, e)
{
    const path = require('path')

    return {
        //-- Box border LoTGD Jade Style
        '.lotgd-border-image': {
            borderWidth: '13px',
            borderImage: `url("${path.resolve(__dirname, '../../../images/box-border.png')}") 13 stretch`
        }
    }
}
