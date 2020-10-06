/**
 * All auto-initialize method of Semantic Ui have a class tab "lotgd"
 */
define([
    '../external/jquery'
], function (jQuery)
{
    'use strict'

    jQuery(function ()
    {
        jQuery('.ui.lotgd.dropdown').dropdown()
        jQuery('.ui.popup.lotgd.form .item').tab({
            onVisible: function (tabPath)
            {
                var text = jQuery('.ui.popup.lotgd.form .item[data-tab="' + tabPath + '"]').text()
                jQuery('.ui.menu.lotgd.form .header.item').text(text)
            }
        })
        jQuery('.ui.lotgd.tabular.menu .item').tab()
        jQuery('.ui.translatable.tabular.menu .item').tab({ context: 'parent' })
        jQuery('.ui.lotgd.checkbox').checkbox()
        jQuery('.ui.menu.form.lotgd .browse').popup({
            inline: true,
            on: 'click',
            hoverable: true,
            position: 'bottom left'
        })
        jQuery('.ui.lotgd.tooltip').popup()
        jQuery('.ui.lotgd.progress').progress({ precision: 10 })
        jQuery('.ui.lotgd.message i.close').on('click', event =>
        {
            jQuery(event.target).closest(jQuery(event.target).parent('.message')).transition('fade')
        })
    })
})
