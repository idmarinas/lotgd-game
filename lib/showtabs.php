<?php
// translator ready
// addnews ready
// mail ready

/**
 * Construct TABS Semantic UI style
 *
 * @param array $tabs Format:
 *						[
 *							'title for tab 1' => 'Content of tab 1'
 *							'title for tab 2' => 'Content of tab 2'
 * 						]
 * @param callable $callback If you need proccess de content of tab can pass a callback. Default no process content and only show.
 * 						   callback recibe paraments $callback($content, $title)
 * @param boolean $browse Indicate type of menu: tabular or browse menu
 */
function lotgd_showtabs($tabs, callable $callback = null, $browse = false )
{
	static $showtab_id = 0;

	$showtab_id++;
    $tab_id = 0;

	$ulMenu = [];
	$ulContent = '';
	$tabActive = '';

	foreach($tabs as $title => $content)
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
            $class = 'bottom attached ' . $class;
        }
		//-- Title of tab
		$ulMenu[] = sprintf('<a class="%s item" data-tab="%s-%s">%s</a>', $class, $showtab_id, $tab_id, translate($title));

		//-- Content of tab
		if (! $callback) $ulContent .= sprintf('<div class="ui %s tab segment" data-tab="%s-%s">%s</div>', $class, $showtab_id, $tab_id, $content);
		else $ulContent .= sprintf('<div class="ui %s tab segment" data-tab="%s-%s">%s</div>', $class, $showtab_id, $tab_id, $callback($content, $title));
	}

    if (! $browse)
    {
        rawoutput(sprintf('<div class="ui top attached lotgd tabular menu">%s</div>',implode('', $ulMenu) ));
    }
    else
    {
        $tabMenu = array_chunk($ulMenu , ceil(count($ulMenu)/4));

        $popupMenu = '<div class="ui flowing popup transition hidden lotgd form">';
        $popupMenu .= '<div class="ui four column relaxed divided grid">';
        foreach($tabMenu as $menu)
        {
            $popupMenu .= '<div class="column"><div class="ui list">';
            $popupMenu .= implode('', $menu);
            $popupMenu .= '</div></div>';
        }
        $popupMenu .= '</div></div>';

        rawoutput(sprintf('<div class="ui menu lotgd form "><a class="browse item active">%s <i class="dropdown icon"></i></a>%s<div class="header item">%s</div></div>',
                translate_inline('Browse'),
                $popupMenu,
                $tabActive
            )
        );
    }

	rawoutput($ulContent);
	unset($ulContent, $ulMenu);
}

// {
//  	static $showform_id = 0;
//  	static $title_id = 0;

//  	$showform_id++;
// 	$extensions = modulehook("showformextensions", []);

// 	$i = false;
// 	$tabMenu = [];
// 	$tabContent = [];
// 	$tabActive = '';
// 	foreach ($layout as $key => $val)
// 	{
// 		$pretrans = 0;

// 		if ($keypref !== false) $keyout = sprintf($keypref, $key);
// 		else $keyout = $key;

// 		if (is_array($val))
// 		{
// 			$info = explode(',', $val[0]);
// 			$val[0] = $info[0];
// 			$info[0] = $val;
// 		}
// 		else $info = explode(',', $val);

// 		if (is_array($info[0])) $info[0] = call_user_func_array("sprintf_translate", $info[0]);
// 		else $info[0] = translate($info[0]);

// 		if (isset($info[1])) $info[1] = trim($info[1]);
// 		else $info[1] = "";

// 		if ($info[1] == "title")
// 		{
// 		 	$title_id++;
// 			if (1 == $title_id)
// 			{
// 				$tabActive = $info[0];
// 				$tabMenu[] = sprintf('<a class="item active" data-tab="tab-%s">%s</a>', $title_id, $info[0]);
// 			}
// 			else
// 			{
// 				$tabMenu[] = sprintf('<a class="item" data-tab="tab-%s">%s</a>', $title_id, $info[0]);
// 			}

//  		}
// 		elseif ($info[1]=="note")
// 		{
// 			$tabContent[$title_id][] = sprintf('<div class="ui small info message">%s</div>', appoencode($info[0]));
// 		}
// 		else
// 		{
// 			if (! $callback) $result = lotgd_show_form_field($info, $row, $key, $keyout, $val, $extensions);
// 			else $result = $callback($info, $row, $key, $keyout, $val, $extensions);

// 			$tabContent[$title_id][] = sprintf('<div class="inline field"><label>%s</label>%s</div>',
// 				appoencode($info[0]),
// 				$result
// 			);

// 			$i = !$i;
// 		}
// 	}

// 	$content = '';
// 	foreach($tabContent as $key => $value)
// 	{
// 		$text = sprintf('<div class="ui form">%s</div>',
// 			implode('', $value)
// 		);

// 		if (0 < $key) $text = sprintf('<div class="ui tab segment %s" data-tab="tab-%s">%s</div>',
// 			(1 == $key?'active':null),
// 			$key,
// 			$text
// 		);

//  		$content .= $text;
// 	}

// 	unset($text);

// 	if (! empty($tabMenu))
// 	{
// 		$tabMenu = array_chunk($tabMenu , ceil(count($tabMenu)/4));

// 		$popupMenu = '<div class="ui flowing popup transition hidden lotgd form">';
// 		$popupMenu .= '<div class="ui stackable equal width divided grid">';
// 		foreach($tabMenu as $menu)
// 		{
// 			$popupMenu .= '<div class="column"><div class="ui list">';
// 			$popupMenu .= implode('', $menu);
// 			$popupMenu .= '</div></div>';
// 		}
// 		$popupMenu .= '</div></div>';

// 		rawoutput(sprintf('<div class="ui menu lotgd form "><a class="browse item active">%s <i class="dropdown icon"></i></a>%s<div class="header item">%s</div></div>',
// 				translate_inline('Browse'),
// 				$popupMenu,
// 				$tabActive
// 			)
// 		);

// 		unset($popupMenu);
// 	}

// 	rawoutput($content);

// 	unset($tabContent, $content, $tabMenu);

// 	tlschema("showform");
// 	$save = translate_inline("Save");
// 	tlschema();

// 	if (! $nosave) rawoutput("<input class='ui button' type='submit' value='$save'>");
// }

