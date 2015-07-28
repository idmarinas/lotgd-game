<?php

function ppad_getmoduleinfo(){
	$info = array(
			"name"=>"Paypal Area Ad",
			"version"=>"1.01",
			"author"=>"Chris Vorndran",
			"category"=>"Administrative",
			"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=59",
			"vertxtloc"=>"http://dragonprime.net/usres/Sichae/",
			"settings"=>array(
				"Paypal Area Ad Settings,title",
				"It is recommended that if you are using AdSense code you go for the smallest possible one.,note",
				"This is to ensure that you don't stretch out the paypal display area.,note",
				"The ad will be centered so there is no need to add any centering tags to the code.,note",
				"adcode"=>"Code for Ad,textarea|",
				"over-width"=>"How many pixels are allowed before overflow is used?,int|100",
			),
		);
	return $info;
}
function ppad_install(){
	module_addhook_priority("everyfooter",80);
	return true;
}
function ppad_uninstall(){
	return true;
}
function ppad_dohook($hookname,$args){
	switch($hookname){
	case "everyfooter":
		if (!isset($args['paypal'])) {
			$args['paypal'] = array();
		} elseif (!is_array($args['paypal'])) {
			$args['paypal'] = array($args['paypal']);
		}
		$width = get_module_setting("over-width");
		$display = "<div style='text-align:center; width:".$width."px; overflow:auto;'>".get_module_setting("adcode")."</div>";
		array_push($args['paypal'], $display);
		
	break;
	}
	return $args;
}
function ppad_run(){
}
?>