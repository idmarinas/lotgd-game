<?php
//The dohook section was taken from creationaddon.php and slightly modified, so credit to Dannic for the dohook section.
function infocenter_getmoduleinfo(){
	$info = array(
		"name"=>"Information Center",
		"version"=>"1.0",
		"author"=>"`!`bRolland`b`0",
		"category"=>"General",
		"allowanonymous"=>true,
		"override_forced_nav"=>true,
		"download"=>"",
		"settings"=>array(
		  "Contents Page,title",
		    "ctext"=>"Text on contents page,textarea|",
		    "faq"=>"Show link to FAQ on contents page?,bool|0",
		    "petition"=>"Show link to petition on contents page?,bool|0",
      "Page One,title",
        "show1"=>"Show link to this page in contents?,bool|0",
        "name1"=>"Name of this page,text|",
        "text1"=>"Text displayed on this page,textarea|",
      "Page Two,title",
        "show2"=>"Show link to this page in contents?,bool|0",
        "name2"=>"Name of this page,text|",
        "text2"=>"Text displayed on this page,textarea|",
      "Page Three,title",
        "show3"=>"Show link to this page in contents?,bool|0",
        "name3"=>"Name of this page,text|",
        "text3"=>"Text displayed on this page,textarea|",
      "Page Four,title",
        "show4"=>"Show link to this page in contents?,bool|0",
        "name4"=>"Name of this page,text|",
        "text4"=>"Text displayed on this page,textarea|",
      "Page Five,title",
        "show5"=>"Show link to this page in contents?,bool|0",
        "name5"=>"Name of this page,text|",
        "text5"=>"Text displayed on this page,textarea|",
      "Page Six,title",
        "show6"=>"Show link to this page in contents?,bool|0",
        "name6"=>"Name of this page,text|",
        "text6"=>"Text displayed on this page,textarea|",
      "Page Seven,title",
        "show7"=>"Show link to this page in contents?,bool|0",
        "name7"=>"Name of this page,text|",
        "text7"=>"Text displayed on this page,textarea|",
      "Page Eight,title",
        "show8"=>"Show link to this page in contents?,bool|0",
        "name8"=>"Name of this page,text|",
        "text8"=>"Text displayed on this page,textarea|",
      "Page Nine,title",
        "show9"=>"Show link to this page in contents?,bool|0",
        "name9"=>"Name of this page,text|",
        "text9"=>"Text displayed on this page,textarea|",
      "Page Ten,title",
        "show10"=>"Show link to this page in contents?,bool|0",
        "name10"=>"Name of this page,text|",
        "text10"=>"Text displayed on this page,textarea|",
      "Additional Links,title",
		    " - Additional Link 1 -,note",
		    "link1"=>"Display another link on the contents page?,bool|0",
		    "nlink1"=>"Name of this link,text|",
		    "url1"=>"URL:,text|",
		    "open1"=>"Open this link in a new window?,bool|0",
		    " - Additional Link 2 -,note",
		    "link2"=>"Display another link on the contents page?,bool|0",
		    "nlink2"=>"Name of this link,text|",
		    "url2"=>"URL:,text|",
		    "open2"=>"Open this link in a new window?,bool|0",
		    " - Additional Link 3 -,note",
		    "link3"=>"Display another link on the contents page?,bool|0",
		    "nlink3"=>"Name of this link,text|",
		    "url3"=>"URL:,text|",
		    "open3"=>"Open this link in a new window?,bool|0",
		    " - Additional Link 4 -,note",
		    "link4"=>"Display another link on the contents page?,bool|0",
		    "nlink4"=>"Name of this link,text|",
		    "url4"=>"URL:,text|",
		    "open4"=>"Open this link in a new window?,bool|0",
		    " - Additional Link 5 -,note",
		    "link5"=>"Display another link on the contents page?,bool|0",
		    "nlink5"=>"Name of this link,text|",
		    "url5"=>"URL:,text|",
		    "open5"=>"Open this link in a new window?,bool|0",
		),
	);
	return $info;
}
function infocenter_install(){
  module_addhook("everyfooter");
	return true;
}
function infocenter_uninstall(){
	return true;
}
function infocenter_dohook($hookname,$args){
	switch($hookname){	
		case "everyfooter":
$infolink= "<br><a href='runmodule.php?module=infocenter&op=contents' target='_blank' onClick=\"".popup("runmodule.php?module=infocenter&op=contents","500x300")."; return false;\" 'class='motd'>Information Center</a>";
		 addnav("","runmodule.php?module=infocenter&op=contents");
		   if (!isset($args['source'])) {
$args['source'] = array();
			 } elseif (!is_array($args['source'])) {
$args['source'] = array($args['source']);
				}
		   array_push($args['source'], $infolink);
		break;
	}
	return $args;
}
function infocenter_run(){
$show1=get_module_setting("show1");
$show2=get_module_setting("show2");
$show3=get_module_setting("show3");
$show4=get_module_setting("show4");
$show5=get_module_setting("show5");
$show6=get_module_setting("show6");
$show7=get_module_setting("show7");
$show8=get_module_setting("show8");
$show9=get_module_setting("show9");
$show10=get_module_setting("show10");
$faq=get_module_setting("faq");
$petition=get_module_setting("petition");
$link1=get_module_setting("link1");
$open1=get_module_setting("open1");
$url1=get_module_setting("url1");
$nlink1=get_module_setting("nlink1");
$link2=get_module_setting("link2");
$open2=get_module_setting("open2");
$url2=get_module_setting("url2");
$nlink2=get_module_setting("nlink2");
$link3=get_module_setting("link3");
$open3=get_module_setting("open3");
$url3=get_module_setting("url3");
$nlink3=get_module_setting("nlink3");
$link4=get_module_setting("link4");
$open4=get_module_setting("open4");
$url4=get_module_setting("url4");
$nlink4=get_module_setting("nlink4");
$link5=get_module_setting("link5");
$open5=get_module_setting("open5");
$url5=get_module_setting("url5");
$nlink5=get_module_setting("nlink5");
$op = httpget('op');

	switch ($op){
  case"contents":
popup_header("Information Center");
output("%s",get_module_setting("ctext"),true);
output("<br><br><br>`b`!Navigation:`b`0<br>",true);
  		if ($show1 == 1){
rawoutput("&#149; <a href='runmodule.php?module=infocenter&op=page1'>%s</a>",get_module_setting("name1"),true);
}
  		if ($show2 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page2'>%s</a>",get_module_setting("name2"),true);
}
  		if ($show3 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page3'>%s</a>",get_module_setting("name3"),true);
}
  		if ($show4 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page4'>%s</a>",get_module_setting("name4"),true);
}
  		if ($show5 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page5'>%s</a>",get_module_setting("name5"),true);
}
  		if ($show6 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page6'>%s</a>",get_module_setting("name6"),true);
}
  		if ($show7 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page7'>%s</a>",get_module_setting("name7"),true);
}
  		if ($show8 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page8'>%s</a>",get_module_setting("name8"),true);
}
  		if ($show9 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page9'>%s</a>",get_module_setting("name9"),true);
}
  		if ($show10 == 1){
rawoutput("<br>&#149; <a href='runmodule.php?module=infocenter&op=page10'>%s</a>",get_module_setting("name10"),true);
}
  		if ($faq == 1){
rawoutput("<br>&#149; <a href='petition.php?op=faq'>FAQ</a>",true);
}
  		if ($petition == 1){
rawoutput("<br>&#149; <a href='petition.php'>Petition a Problem</a>",true);
}
  		if ($link1 == 1){
	  	if ($open1 == 1){
rawoutput("<br>&#149; <a href='$url1' target='_blank'>$nlink1</a>",true);
}else{
rawoutput("<br>&#149; <a href='$url1'>$nlink1</a>",true);
}
}
  		if ($link2 == 1){
	  	if ($open2 == 1){
rawoutput("<br>&#149; <a href='$url2' target='_blank'>$nlink2</a>",true);
}else{
rawoutput("<br>&#149; <a href='$url2'>$nlink2</a>",true);
}
}
  		if ($link3 == 1){
	  	if ($open3 == 1){
rawoutput("<br>&#149; <a href='$url1' target='_blank'>$nlink3</a>",true);
}else{
rawoutput("<br>&#149; <a href='$url3'>$nlink3</a>",true);
}
}
  		if ($link4 == 1){
	  	if ($open4 == 1){
rawoutput("<br>&#149; <a href='$url1' target='_blank'>$nlink4</a>",true);
}else{
rawoutput("<br>&#149; <a href='$url4'>$nlink4</a>",true);
}
}
  		if ($link5 == 1){
	  	if ($open5 == 1){
rawoutput("<br>&#149; <a href='$url5' target='_blank'>$nlink5</a>",true);
}else{
rawoutput("<br>&#149; <a href='$url5'>$nlink5</a>",true);
}
}
popup_footer();
 break;
  case"page1":
popup_header("Information Center - %s",get_module_setting("name1"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name1"),true);
output_notl("%s`n`n`n",get_module_setting("text1"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page2":
popup_header("Information Center - %s",get_module_setting("name2"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name2"),true);
output_notl("%s`n`n`n",get_module_setting("text2"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page3":
popup_header("Information Center - %s",get_module_setting("name3"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name3"),true);
output_notl("%s`n`n`n",get_module_setting("text3"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page4":
popup_header("Information Center - %s",get_module_setting("name4"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name4"),true);
output_notl("%s`n`n`n",get_module_setting("text4"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page5":
popup_header("Information Center - %s",get_module_setting("name5"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name5"),true);
output_notl("%s`n`n`n",get_module_setting("text5"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page6":
popup_header("Information Center - %s",get_module_setting("name6"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name6"),true);
output_notl("%s`n`n`n",get_module_setting("text6"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page7":
popup_header("Information Center - %s",get_module_setting("name7"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name7"),true);
output_notl("%s`n`n`n",get_module_setting("text7"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page8":
popup_header("Information Center - %s",get_module_setting("name8"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name8"),true);
output_notl("%s`n`n`n",get_module_setting("text8"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page9":
popup_header("Information Center - %s",get_module_setting("name9"));
output("`b`c%s`b`c`n`n",get_module_setting("name9"),true);
output("%s`n`n`n",get_module_setting("text9"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
  case"page10":
popup_header("Information Center - %s",get_module_setting("name10"));
output_notl("`b`c%s`b`c`n`n",get_module_setting("name10"),true);
output_notl("%s`n`n`n",get_module_setting("text10"),true);
rawoutput("<center><a href='runmodule.php?module=infocenter&op=contents'><strong>Contents</strong></a></center>");
popup_footer();
 break;
}
}
?>