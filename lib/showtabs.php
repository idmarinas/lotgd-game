<?php
// translator ready
// addnews ready
// mail ready

/**
 * Construct TABS Uikit style
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

	$ulMenu = '';
	$ulContent = '';

	foreach($tabs as $title => $content)
	{
		//-- Title of tab
		$ulMenu .= sprintf('<li><a href="#">%s</a></li>', translate($title));

		//-- Content of tab
		if (! $callback) $ulContent .= '<li>'.$content.'</li>';
		else $ulContent .= '<li>'.$callback($content, $title).'</li>';
	}

	rawoutput(sprintf('<ul class="uk-tab" data-uk-tab="{connect:\'#tabs-%s\'}">%s</ul>', $showtab_id, $ulMenu )	);

	rawoutput(sprintf('<ul class="uk-switcher" id="tabs-%s">%s</ul>', $showtab_id, $ulContent ) );
	unset($ulContent, $ulMenu);
}