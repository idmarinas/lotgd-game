define([
    'jquery'
], function (jQuery)
{
    'use strict'

    jQuery(function ()
    {
        jQuery('.ui.dropdown').dropdown()
        jQuery('.ui.popup.lotgd.form .item').tab({
            onVisible: function (tabPath)
            {
                var text = jQuery('.ui.popup.lotgd.form .item[data-tab="' + tabPath + '"]').text()
                jQuery('.ui.menu.lotgd.form .header.item').text(text)
            }
        })
        jQuery('.ui.lotgd.tabular.menu .item').tab()
        jQuery('.ui.checkbox').checkbox()
        jQuery('.ui.menu.form.lotgd .browse').popup({
            inline: true,
            hoverable: true,
            position: 'bottom left'
        })
        jQuery('.ui.tooltip').popup()
        jQuery('.ui.progress').progress({precision: 10})
    })
})
