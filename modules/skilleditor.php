<?php
function skilleditor_getmoduleinfo(){
	$info = array(
		"name" => "Skill Editor",
		"author" => "`4Thanatos`2, Based on Eth's mysticalshop",
		"version" => "1.1",
		"category" => "Specialties",
		"requires"=>array("mana"=>"`71.0|By `4Thanatos",),
		);
		return $info;
}
function skilleditor_install(){
  module_addhook("superuser");
  return true;
}
function skilleditor_uninstall(){return true;}
function skilleditor_dohook($hookname,$args){
	global $session;
	switch ($hookname){  
	case "superuser":
		if ($session['user']['superuser'] & SU_EDIT_USERS) {
			addnav("Editors");
			addnav("Skill Editor","runmodule.php?module=skilleditor&op=editor&what=view");
		}
	break;
	}
	return $args;
}
function skilleditor_run(){
	global $session;
	page_header("Skill Editor");
  $op=httpget('op');
	switch ($op){
	case "editor":
	$id = httpget('id');
	$what = httpget('what');
	$from = "runmodule.php?module=skilleditor&";
	$skillarray = array(
		"Skill Properties,title",
			"id"             =>"Skill ID,hidden",
			"name"           =>"Skill Name,Name|",
			"levelreq"       =>"Level Requirement,int|0",
			"type"           =>"Specialty Type ,text|",
			"startmsg"       =>"startmsg,text|",
			"effectmsg"      =>"effectmsg,text|",
			"roundmsg"       =>"roundmsg,text|",
			"effectnodmgmsg" =>"effectnodmgmsg,text|",
			"effectfailmsg"  =>"effectfailmsg,text|",
      "wearoff"        =>"wearoff,text|",
			"minioncount"    =>"minioncount,int|0",
			"mingoodguydamage"=>"mingoodguydamage,int|0",
      "maxgoodguydamage"=>"maxgoodguydamage,int|0",
      "minbadguydamage"=>"minbadguydamage,int|0",
      "maxbadguydamage"=>"maxbadguydamage,int|0",
			"manacost"       =>"manacost,int|0",
			"rounds"         =>"rounds,int|0",
			"badguydmgmod"   =>"badguydmgmod,float|1",
			"badguyatkmod"   =>"badguyatkmod,float|1",
			"badguydefmod"   =>"badguydefmod,float|1",
			"atkmod"         =>"atkmod,float|1",
			"defmod"         =>"defmod,float|1",
			"lifetap"        =>"lifetap,float|0",
			"damageshield"   =>"damageshield,float|0",
      "regen"          =>"regen,float|1",		
	);
	require_once("modules/skilleditor/$what.php");
	addnav("Admin Tools");
	addnav("Examine Skills`0",$from."op=editor&what=view");
	addnav("Add a Skill", $from."op=editor&what=add");
	
	addnav("Other");
	addnav("Return to the Grotto", "superuser.php");
  break;
  }
  page_footer();
}

?>
