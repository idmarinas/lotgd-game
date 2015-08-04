<?php
//For versioninfos just take a look at /modules/translationwizard/versions.txt

// Okay, someone wants to use this outside of normal game flow.. no real harm
if(!defined('OVERRIDE_FORCED_NAV')) define("OVERRIDE_FORCED_NAV",true);

function translationwizard_getmoduleinfo(){
	//Slightly modified by JT Traub in the original untranslated.php
	$info = array(
	    "name"=>"Translation Wizard",
		"version"=>"1.47",
		"author"=>"`2Written by Oliver Brendel, `3based on the untranslated.php by Christian Rutsch`nFilescan by `qEdorian`n",
		"category"=>"Administrative",
		"download"=>"http://lotgd-downloads.com",
		"settings"=>array(
			"General Preferences,title",
				"blocktrans"=>"Block the Untranslated Text in the grotto,bool|0",
				"query"=>"Use nested query (doesn't works with lower mysql servers),bool|0",
				"page"=>"How many results per page for fixing/checking,int|20",
			"Access Restrictions,title",
				"Restrictions are: search+edit the translations table + truncate untranslated,note",
				"restricted"=>"Has the wizard restrictions for some users?,bool|0",
			"Auto Scan + Cleanup,title",
				"This is only for skilled users! Its not finding everything yet,note",
				"and your untranslated gets filled quickly if you begin to use this at start,note",
				"but if you want to scan new modules automatically on install - here it is,note",
				"autoscan"=>"Scan automatically modules upon install and insert into untranslated,bool|0",
				"translationdelete"=>"Ask if translations should be deleted at uninstallation of a module,bool|0",
			"Central Translations,title",
				"blockcentral"=>"Block the Central Translations Section in the wizard,bool|0",
				"lookuppath"=>"URL to the central translations section,text|http://translations.nb-core.org",
				"Note: This is usually translation.nb-core.org,note",
			
		),
		"prefs"=>array(
		    "Translation Wizard - User prefs,title",
				"language"=>"Languages for the Wizard,enum,".getsetting("serverlanguages","en,English,fr,Français,dk,Danish,de,Deutsch,es,Español,it,Italian"),
				"Note: don't change this if you don't need to... it is set up in the Translation Wizard!,note",
				"allowed"=>"Does this user have unrestricted access to the wizard?,bool|0",
				"Note: This is only active if the restriction settings is 'true' in the module settings,note",
				"view"=>"Use advanced view (shows more),bool|0",
		),
		);
    return $info;
}

function translationwizard_install(){
	module_addhook("superuser");
	module_addhook("header-modules");
	if (is_module_active("translationwizard")) debug("Module Translationwizard updated");
	$wizard=array(
		'tid'=>array('name'=>'tid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'language'=>array('name'=>'language', 'type'=>'varchar(10)'),
		'uri'=>array('name'=>'uri', 'type'=>'varchar(255)'),
		'intext'=>array('name'=>'intext', 'type'=>'text'),
		'outtext'=>array('name'=>'outtext', 'type'=>'text'),
		'author'=>array('name'=>'author', 'type'=>'varchar(50)'),
		'version'=>array('name'=>'version', 'type'=>'varchar(50)'),
		'key-PRIMARY' => array('name'=>'PRIMARY', 'type'=>'primary key', 'unique'=>'1', 'columns'=>'tid'),
		'key-one'=> array('name'=>'language', 'type'=>'key', 'unique'=>'0', 'columns'=>'language,uri'),
		'key-two'=> array('name'=>'uri', 'type'=>'key', 'unique'=>'0', 'columns'=>'uri'),
		);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix("temp_translations"), $wizard, true);
	return true;
}

function translationwizard_uninstall() {
	debug ("Performing Uninstall on Translation Wizard. Thank you for using!`n`n");
	if(db_table_exists(db_prefix("temp_translations"))){
		$result=db_query("DROP TABLE ".db_prefix("temp_translations"));
	}
	return $result;
}


function translationwizard_dohook($hookname, $args){
	global $session;
	require("./modules/translationwizard/translationwizard_dohook.php");
	return $args;
}

function translationwizard_run(){
	global $session,$logd_version,$coding;
	check_su_access(SU_IS_TRANSLATOR); //check again Superuser Access
	$op = httpget('op');
	page_header("Translation Wizard");
	//get some standards
	$languageschema=get_module_pref("language","translationwizard");
	//these lines grabbed the local scheme, in 1.1.0 there is a setting for it
	$coding=getsetting("charset", "ISO-8859-1");
	$viewsimple=get_module_pref("view","translationwizard");
	$mode = httpget('mode');
	$namespace = httppost('ns');
	$from = httpget('from');
	$page = get_module_setting('page');
	if (httpget('ns')<>"" && $namespace=="") $namespace=httpget('ns'); //if there is no post then there is maybe something to get
	$trans = httppost("transtext");
	if (is_array($trans)) { //setting for any intexts you might receive
		$transintext = $trans;
	}else {
		if ($trans) $transintext = array($trans);
		else $transintext = array();
	}
	$trans = httppost("transtextout");
	if (is_array($trans)) { //setting for any outtexts you might receive
		$transouttext = $trans;
	}else {
		if ($trans) $transouttext = array($trans);
		else $transouttext = array();
	}
	//end of the header
	if ($op=="")  $op="default";
	if($op!='scanmodules') require("./modules/translationwizard/errorhandler.php");	
	require("./modules/translationwizard/$op.php");
	require_once("lib/superusernav.php");
	superusernav();
	require("./modules/translationwizard/build_nav.php");
	page_footer();
}

?>
