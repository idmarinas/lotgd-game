<?php
// translator ready
// addnews ready
// mail ready

/**
 * Construct TABS Semantic UI style
 *
 * @var array $tabs Format:
 *						[
 *							'title for tab 1' => 'Content of tab 1'
 *							'title for tab 2' => 'Content of tab 2'
 * 						]
 * @var callable $callback If you need proccess de content of tab can pass a callback. Default no process content and only show.
 * 						   callback recibe paraments $callback($content, $title)
 */
function lotgd_showtabs($tabs, callable $callback = null)
{
	static $showtab_id = 0;

	$showtab_id++;
    $tab_id = 0;

	$ulMenu = '';
	$ulContent = '';

	foreach($tabs as $title => $content)
	{
        $tab_id++;
        $class = (1 < $tab_id ? '' : 'active');
		//-- Title of tab
		$ulMenu .= sprintf('<a class="%s item" data-tab="%s-%s">%s</a>', $class, $showtab_id, $tab_id, translate($title));

		//-- Content of tab
		if (! $callback) $ulContent .= sprintf('<div class="ui bottom attached %s tab segment" data-tab="%s-%s">%s</div>', $class, $showtab_id, $tab_id, $content);
		else $ulContent .= sprintf('<div class="ui bottom attached %s tab segment" data-tab="%s-%s">%s</div>', $class, $showtab_id, $tab_id, $callback($content, $title));
	}

	rawoutput(sprintf('<div class="ui top attached lotgd tabular menu">%s</div>',$ulMenu ));

	rawoutput($ulContent);
	unset($ulContent, $ulMenu);
}
