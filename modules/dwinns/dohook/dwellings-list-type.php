<?
	addnav("Show Only Types");
	addnav(array("%s",translate_inline(ucfirst(get_module_setting("dwnameplural","dwinns")))),"runmodule.php?module=dwellings&op=list&showonly=dwinns&ref={$args['ref']}&sortby={$args['sortby']}&order={$args['order']}");
?>