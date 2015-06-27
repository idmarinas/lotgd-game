<?php
// addnews ready
// mail ready
function advertising_google_getmoduleinfo(){
	$info = array(
		"name"=>"Advertising -- Google",
		"author"=>"Eric Stevens",
		"version"=>"1.0",
		"download"=>"core_module",
		"category"=>"General",
//		"prefs"=>array(
//			"Google Advertising Preferences,title",
//			"user_color"=>"Color Theme,enum,"
//					."333300-003300-00ff00-00cc00-00cc66,LotGD,"
//					."336699-ffffff-0000ff-008000-000000,Mother Earth,"
//					."000000-f0f0f0-0000ff-008000-000000,Black and Blue,"
//					."b4d0dc-ecf8ff-0000cc-008000-6f6f6f,Fresh Mint,"
//					."a8dda0-ebffed-0000cc-008000-6f6f6f,Cut Grass,"
//					."ddb7ba-fff5f6-0000cc-008000-6f6f6f,Raspberry Smoothie,"
//					."fdefd2-fdefd2-0000cc-008000-000000,Vanilla Cream,"
//					."e0ffe3-e0ffe3-0000cc-008000-000000,Green Taffy,"
//					."f9dff9-f9dff9-0000cc-008000-000000,It's a Girl!,"
//					."dff2fd-dff2fd-0000cc-008000-000000,Aquadoodle,"
//					."fdffca-fdffca-0000cc-008000-000000,Popcorn,"
//					."6699cc-003366-ffffff-aecceb-aecceb,Blue Whale,"
//					."b0e0e6-ffffff-000000-336699-333333,Cumulous Cloud,"
//					."003366-003366-ff6600-99ccff-ffffff,Blue Bird,"
//					."ff4500-ffebcd-de7008-e0ad12-8b4513,Peach Melba,"
//					."003366-000000-ffffff-ff6600-ff6600,Wicked Witch,"
//					."669966-99cc99-000000-00008b-336633,Swamp Green,"
//					."cc99cc-e7c6e8-000000-00008b-663366,Grape Skin,"
//					."2d5893-99aacc-000000-000099-003366,Melancholy Blue,"
//					."cccccc-ffffff-000000-666666-333333,Steely Gaze,"
//					."333333-000000-ffffff-999999-cccccc,Black Knight,"
//					."ddaaaa-ecf8ff-0033ff-0033ff-000000,Robin's Egg,"
//					."578a24-ccff99-00008b-00008b-000000,Green Tea,"
//					."191933-333366-99cc33-ffcc00-ffffff,Blue Suey,"
//					."660000-7d2626-ffffff-daa520-bdb76b,Pot Roast"
//				."|333300-003300-00ff00-00cc00-00cc66",
//			),
		"settings"=>array(
			"Publisher identification,title",
			"publisher" => "Publisher ID|pub-1704430222579783",
			"Conversion Tracking,title",
			"tracking_do"=>"Do tracking?,bool|1",
			"tracking_id"=>"Tracking (Conversion) ID|",
			"tracking_format"=>"Tracking Format,enum,1,Vertical,2,Horizontal|2",
			"tracking_color"=>"Tracking Color (hex number, no # please)|000000",
		)
	);
	return $info;
}

function advertising_google_install(){
	module_addhook("mod_ad_banner");
	module_addhook("process-create");
	return true;
}
function advertising_google_uninstall(){
	return true;
}
function advertising_google_dohook($hookname,$args){
	switch($hookname){
	case "process-create":
		if (get_module_setting("tracking_do")){
			rawoutput('<!-- Google Code for Signup Conversion Page -->
<script language="JavaScript" type="text/javascript">
<!--
var google_conversion_id = '.get_module_setting("tracking_id").';
var google_conversion_language = "en_US";
var google_conversion_format = "'.get_module_setting("tracking_format").'";
var google_conversion_color = "'.get_module_setting("tracking_color").'";
if (1) {
  var google_conversion_value = 1;
}
var google_conversion_label = "Signup";
//-->
</script>
<script language="JavaScript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<img height=1 width=1 border=0 src="http://www.googleadservices.com/pagead/conversion/'.get_module_setting("tracking_id").'/?value=1&label=Signup&script=0">
</noscript>');
		}
		break;
	default:
		array_push($args['banners'],advertising_google_get($args['w'],$args['h']));
	}
	return $args;
}
function advertising_google_get($w,$h){
	$publisher = get_module_setting("publisher");
	$size = $w . 'x' . $h;
	if ('180x150' == $size)
	{
		$slot = 3692576949;
	}
	else if ('728x90' == $size)
	{
		$slot = 7564491912;
	}
	else
	{
		$w = 468;
		$h = 60;
		$slot = 6080840945;
	}

	return "
<script async src='//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
<!-- 180x150 DragonVerde -->
<ins class='adsbygoogle'
     style=display:inline-block;width:{$w}px;height:{$h}px
     data-ad-client='$publisher'
     data-ad-slot='{$slot}'></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>

";
}

?>
