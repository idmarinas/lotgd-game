/**
 * Javascript for LOTGD
 * By IDMarinas
 *
 * Part of the scripts in game are in this file, reducing server load
 */

$(function(){
	$('.ui.dropdown').dropdown();
	$('.ui.popup.lotgd.form .item').tab({
        onVisible : function (tabPath)
        {
            var text = $('.ui.popup.lotgd.form .item[data-tab="' + tabPath + '"]').text();
            $('.ui.menu.lotgd.form .header.item').text(text);
        },
    });
    $('.ui.checkbox').checkbox();
    $('.ui.menu.form.lotgd .browse').popup({
        inline     : true,
        hoverable  : true,
        position   : 'bottom left',
    });
    $('.ui.tooltip').popup();
    $('.ui.progress').progress({precision: 10});
});
