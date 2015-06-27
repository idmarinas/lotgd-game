<?
	if($args['type']=="dwinns" && $args['status']==1 && get_module_setting("maxkeys")==123456789){
		addnav("","runmodule.php?module=dwellings&op=enter&dwid={$args['dwid']}");
		$tress=translate_inline("Tresspass");
		rawoutput("<a href='runmodule.php?module=dwellings&op=enter&dwid={$args['dwid']}'>$tress</a>");
	}
?>