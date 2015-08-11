<?php
/**************
Name: Equipment Buffs, for the Equipment Shop
Author: Eth - ethstavern(at)gmail(dot)com 
Version: 1.3
Re-Release Date: 01-25-2006
About: An addon for the Equipment Shop that lets you
	   add buffs to existing items. Could be *very*
	   unbalancing. Use at your own risk.
Notes: Inspired by XChrisX's Inventory mod.
	   pieced together from items.php and a few snippets 
	   from XChrisX's Inventory System.	   
Translation compatible. Mostly.
*****************/
function mysticalshop_buffs_getmoduleinfo(){
		require_once("modules/mysticalshop_buffs/moduleinfo.php");
	return $info;
}

function mysticalshop_buffs_install(){
		require_once("modules/mysticalshop_buffs/install.php");
	return true;
}
function mysticalshop_buffs_uninstall(){
	$sql = "DROP TABLE " . db_prefix("magicitembuffs");
	db_query($sql);
	return true;
}
function mysticalshop_buffs_dohook($hookname,$args){
	global $session;
	$from = "runmodule.php?module=mysticalshop_buffs&";
	switch($hookname){
	case "newday":
	case "mysticalshop-buy":
		require_once("modules/mysticalshop_buffs/addbuff.php");
		break;
	case "mysticalshop-sell-after":
		require_once("modules/mysticalshop_buffs/stripbuff.php");
		break;
	case "mysticalshop-preview":
		require_once("modules/mysticalshop_buffs/preview.php");
		break;
	case "mysticalshop-editor":
		addnav("Admin Tools");
		addnav("`^Go to Buff Manager",$from."op=editor&what=view");
		break;
	}	 
	return $args;
}

//code by Thanatos
function mysticalshop_buffs_calc($value){
	global $session;
	$value=preg_replace("/<([A-Za-z0-9]+)\\|([A-Za-z0-9]+)>/","get_module_pref('\\2','\\1')",$value);
	$value=preg_replace("/<([A-Za-z0-9]+)>/","\$session['user']['\\1']",$value);
	eval('$value='.$value.";");
	return $value;
}

function mysticalshop_buffs_run(){
	global $session;
	$title = translate_inline("Equipment Buffs Manager");
	page_header($title);
	$op = httpget('op');
	$id=httpget("id");
	$from = "runmodule.php?module=mysticalshop_buffs&";	   
	if ($op == "editor"){
		require_once("modules/mysticalshop_buffs/editor.php");
	}
	page_footer();
}
?>