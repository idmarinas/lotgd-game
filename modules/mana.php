<?php
function mana_getmoduleinfo(){
	$info = array(
		"name" => "Mana Core",
		"author" => "`4Thanatos",
		"version" => "1.1",
		"category" => "Specialties",
		"settings"=>array(
      "reqlevels"=>"User must Gain X levels to get an extra mana point?,int|10",
      "mananame" =>"Mana Name?,text|Mana"
    ),
    "prefs"=>array(
		  "curmana"=>"Current Mana,int|0",
		  "maxmana"=>"Maximum Mana,int|10",
		  "lvlgain"=>"Gained levels?,int|0"
		  ),
		);
		return $info;
}
function mana_install(){
  module_addhook("newday");
  module_addhook("charstats");  
  module_addhook("incrementspecialty");
  if (!db_table_exists(db_prefix("skills"))){
		$sql="CREATE TABLE `".db_prefix("skills")."` (
		  	 `id` int(11) unsigned NOT NULL auto_increment,
		  	 `name` varchar(50) NOT NULL default '',
		  	 `levelreq` int(11) NOT NULL default '0',
		  	 `type` varchar(50) NOT NULL default '',
		  	 `startmsg` varchar(255) NOT NULL default '',
			   `effectmsg`varchar(255) NOT NULL default '',
			   `effectnodmgmsg` varchar(255) NOT NULL default '',
			   `effectfailmsg` varchar(255) NOT NULL default '',
			   `roundmsg` varchar(255) NOT NULL default '',
			   `wearoff` varchar(255) NOT NULL default '', 
			   `manacost` int(11) NOT NULL default '0',
			   `rounds` int(11) NOT NULL default '0',
			   `minioncount` int(11) NOT NULL default '0',
			   `mingoodguydamage` int(11) NOT NULL default '0',
			   `maxgoodguydamage` int(11) NOT NULL default '0',
			   `minbadguydamage` int(11) NOT NULL default '0',
			   `maxbadguydamage` int(11) NOT NULL default '0',
			   `badguydmgmod` float NOT NULL default '1',
			   `badguyatkmod` float NOT NULL default '1',
			   `badguydefmod` float NOT NULL default '1',
			   `atkmod` float NOT NULL default '1',
			   `defmod` float NOT NULL default '1',
			   `damageshield` float NOT NULL default '0',
			   `lifetap` float NOT NULL default '0',
			   `regen` float NOT NULL default '0',
		PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1;";
		db_query($sql);
		output("`c`b`QCreating Skills Table.`b`n`c");
    }
    return true;  	
}		 
function mana_uninstall(){
	if (db_table_exists(db_prefix("skills"))){db_query ("DROP TABLE ".db_prefix("skills").";");}
  return true;
  }
function mana_dohook($hookname,$args){
  global $session;
  $curmana=get_module_pref("curmana");
  $maxmana=get_module_pref("maxmana");
  switch ($hookname) {
  case "newday":
    set_module_pref("curmana",$maxmana);
  break;
  case "charstats":
    	addcharstat("Character Info");
    	addcharstat(get_module_setting("mananame"), $curmana."`7/".$maxmana);
  break;
  case "incrementspecialty":
      $lvlgain=get_module_pref("lvlgain");
      set_module_pref("lvlgain",$lvlgain+1);
      $remlvl=get_module_setting("reqlevels")-$lvlgain;
      if($remlvl>0){
       output("`^Only $remlvl more levels to go before you recieve an extra mana point!`0`n");
      }else{
       output("`^You gain an extra mana point!`0`n");
			 set_module_pref("maxmana",$maxmana+1);
			 set_module_pref("curmana",$maxmana+1);
			 set_module_pref("lvlgain",0);
      }
	break;
  }
	return $args;
}
?>
