<?php
function specialtyforbiddenarts_getmoduleinfo(){
	$info = array(
		"name" => "Specialty - Forbidden Arts",
		"author" => "`4Thanatos`2, Based on Eric Steven's specialtydarkarts",
		"version" => "1.1",
		"category" => "Specialties",
		"requires"=>array("mana"=>"`71.0|By `4Thanatos",),
		"settings"=>array(
      "Forbidden Arts Settings,title",
      "expreq"=>"Must be used X times to level up?,int|100",
      "dkreq" =>"DK Requirement,int|0"
    ),
    "prefs"   =>array(
			"Specialty - Forbidden Arts User Prefs,title",
			"level"=>"Skill Level,int|0",
			"exp"  =>"Skill Exp,int|0",
		),
	);
	return $info;
}

function specialtyforbiddenarts_install(){
	$sql = "DESCRIBE " . db_prefix("accounts");
	$result = db_query($sql);
	$specialty="FA";
	while($row = db_fetch_assoc($result)) {
		// Convert the user over
		if ($row['Field'] == "forbiddenarts") {
			debug("Migrating forbiddenarts field");
			$sql = "INSERT INTO ".db_prefix("module_userprefs")." (modulename,setting,userid,value) SELECT 'specialtyforbiddenarts', 'skill', acctid, forbiddenarts FROM " . db_prefix("accounts");
			db_query($sql);
			debug("Dropping forbiddenarts field from accounts table");
			$sql = "ALTER TABLE ".db_prefix("accounts")." DROP forbiddenarts";
			db_query($sql);
		}
	}
	debug("Migrating Forbiddenarts Specialty");
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='$specialty' WHERE specialty='1'";
	db_query($sql);
	module_addhook("choose-specialty");
	module_addhook("charstats");
	module_addhook("set-specialty");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("incrementspecialty");
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
  module_addhook("battle-victory");
	return true;
}
function specialtyforbiddenarts_uninstall(){
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='FA'";
	db_query($sql);
	return true;
}

function specialtyforbiddenarts_dohook($hookname,$args){
	global $session,$resline;
	$spec = "FA";
	$name = "Forbidden Arts";
	$ccode = "`$";
	$maxmana=get_module_pref("maxmana","mana");
	$curmana=get_module_pref("curmana","mana");
	$lvl=get_module_pref("level");
	switch ($hookname) {
	/*
	case "charstats":
		if($session['user']['specialty'] == $spec) {
			require_once('./lib/javascript.php');
      addcharstat("Vital Info");
      addcharstat(color_fadeout('Skill','008080','00FFFF'), translate_inline($name)."`n(Level $lvl)");
		}
	break;
	*/
	case "choose-specialty":
	  if(get_module_setting("dkreq")<=$session['user']['dragonkills']){
		if ($session['user']['specialty'] == "" ||
				$session['user']['specialty'] == '0') {
			addnav("$ccode$name`0","newday.php?setspecialty=$spec$resline");
			$t1 = translate_inline("Killing a lot of woodland creatures");
			$t2 = appoencode(translate_inline("$ccode$name`0"));
			rawoutput("<a href='newday.php?setspecialty=$spec$resline'>$t1 ($t2)</a><br>");
			addnav("","newday.php?setspecialty=$spec$resline");
		}}
	break;
	case "set-specialty":
		if($session['user']['specialty'] == $spec) {
			page_header($name);
			output("`5Growing up, you recall killing many small woodland creatures, insisting that they were plotting against you.");
			output("Your parents, concerned that you had taken to killing the creatures barehanded, bought you your very first pointy twig.");
			output("It wasn't until your teenage years that you began performing Forbidden rituals with the creatures, disappearing into the forest for days on end, no one quite knowing where those sounds came from.");
		}
	break;
	case "specialtycolor":
		$args[$spec] = $ccode;
	break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
	break;
	case "specialtymodules":
		$args[$spec] = "specialtyforbiddenarts";
	break;
	case "fightnav-specialties":
	   if($session['user']['specialty'] == $spec) {
     //Search through skills Database looking for skills that player meets the requirments for
	   $sql=db_query("SELECT * FROM ".db_prefix("skills")." WHERE type='".$name."' AND manacost<='".$curmana."' AND levelreq<='".$lvl."' ");
	   $script = $args['script'];
     while ($row=db_fetch_assoc($sql)) {
			addnav("$row[ccode] &#149; $row[name]`7 ($row[manacost])`0", 
			             $script."op=fight&skill=$spec&s=".$row['id'], true);	   
     }
     }
  break;
  case "apply-specialties":
		if($session['user']['specialty'] == $spec) {
      $skill=httpget('s');
  		$s=db_fetch_assoc(db_query("SELECT * FROM ".db_prefix("skills")." WHERE id='".$skill."'"));
      if($s>0){
  	  apply_buff("fa$skill",array(
        "startmsg"        =>$s['startmsg'],
        "name"            =>$s['name'],
        "rounds"          =>$s['rounds'],
        "effectmsg"       =>$s['effectmsg'],
  			"effectnodmgmsg"  =>$s['effectnodmgmsg'],
  			"effectfailmsg"   =>$s['effectfailmsg'],
        "roundmsg"        =>$s['roundmsg'],
        "wearoff"         =>$s['wearoff'],
  			"atkmod"          =>$s['atkmod'],
  			"defmod"          =>$s['defmod'],	
  			"badguydmgmod"    =>$s['badguydmgmod'],
  			"badguyatkmod"    =>$s['badguyatkmod'],
  			"badguydefmod"    =>$s['badguydefmod'],
  			"minioncount"     =>$s['minioncount'],
  			"mingoodguydamage"=>$s['mingoodguydamage'],
  			"maxgoodguydamage"=>$s['maxgoodguydamage'],
  			"minbadguydamage" =>$s['minbadguydamage'],
  			"maxbadguydamage" =>$s['maxbadguydamage'],
  			"lifetap"         =>$s['lifetap'],
  			"damageshield"    =>$s['damageshield'],
  			"regen"           =>$s['regen'],	
  			"schema"          =>"module-specialtyforbiddenarts"
      ));
      $mana=get_module_pref("curmana","mana")-$s['manacost'];
      if($mana<0){$mana=0;}
      set_module_pref("curmana",$mana,"mana");
      $exp=get_module_pref("exp")+1;
	    set_module_pref("exp",$exp);
	    if($exp>get_module_setting("expreq")){
	      $level=get_mdoule_pref("level")+1;
        output("You have leveled up your $name");
        set_module_pref("exp",0);
        set_module_pref("level",$level);
      }
      }
    }
  break;
	}
	return $args;
}

function specialtyforbiddenarts_run(){
}
?>
