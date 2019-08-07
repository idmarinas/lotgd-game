<?php

// translator ready
// addnews ready
// mail ready

/**
 * Construct TABS Semantic UI style.
 *
 * @param array    $tabs     Format:
 *                           [
 *                           'title for tab 1' => 'Content of tab 1'
 *                           'title for tab 2' => 'Content of tab 2'
 *                           ]
 * @param bool     $print    Print tabs or return
 * @param callable $callback If you need proccess de content of tab can pass a callback. Default no process content and only show.
 *                           callback recibe paraments $callback($content, $title)
 * @param bool     $browse   Indicate type of menu: tabular or browse menu
 */
function lotgd_showtabs($tabs, $print = true, callable $callback = null, $browse = false)
{
    static $showtab_id = 0;

    $showtab_id++;
    $tab_id = 0;

    $ulMenu = [];
    $ulContent = '';
    $tabActive = '';

    foreach ($tabs as $title => $content)
    {
        $tab_id++;

        if (1 < $tab_id)
        {
            $class = '';
        }
        else
        {
            $class = 'active';
            $tabActive = $title;
        }

        if (! $browse)
        {
            $class = 'bottom attached '.$class;
        }
        //-- Title of tab
        $ulMenu[] = sprintf('<a class="%s item" data-tab="%s-%s">%s</a>', $class, $showtab_id, $tab_id, translate($title));

        //-- Content of tab
        if (! $callback)
        {
            $ulContent .= sprintf('<div class="ui %s tab segment" data-tab="%s-%s">%s</div>', $class, $showtab_id, $tab_id, $content);
        }
        else
        {
            $ulContent .= sprintf('<div class="ui %s tab segment" data-tab="%s-%s">%s</div>', $class, $showtab_id, $tab_id, $callback($content, $title));
        }
    }

    $content = '';

    if (! $browse)
    {
        $content .= sprintf('<div class="ui top attached lotgd tabular menu">%s</div>', implode('', $ulMenu));
    }
    else
    {
        $tabMenu = array_chunk($ulMenu, ceil(count($ulMenu) / 4));

        $popupMenu = '<div class="ui flowing popup transition hidden lotgd form">';
        $popupMenu .= '<div class="ui four column relaxed divided grid">';

        foreach ($tabMenu as $menu)
        {
            $popupMenu .= '<div class="column"><div class="ui list">';
            $popupMenu .= implode('', $menu);
            $popupMenu .= '</div></div>';
        }
        $popupMenu .= '</div></div>';

        $content .= sprintf('<div class="ui menu lotgd form "><a class="browse item active">%s <i class="dropdown icon"></i></a>%s<div class="header item">%s</div></div>',
            translate_inline('Browse'),
            $popupMenu,
            $tabActive
        );
    }

    $content .= $ulContent;

    if (! $print)
    {
        return $content;
    }

    rawoutput($content);
}
