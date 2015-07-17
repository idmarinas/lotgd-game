<?php
function specialtymysticalpowers_getmoduleinfo(){
	$info = array(
		"name" => "Specialty - Mystical Powers",
		"author" => "`4Thanatos`2, Based on Eric Steven's specialtymysticpowers",
		"version" => "1.1",
		"category" => "Specialties",
		"requires"=>array("mana"=>"`71.0|By `4Thanatos",),
		"settings"=>array(
      "Mystical Powers Settings,title",
      "expreq"=>"Skills must be used X times to level up?,int|100",
      "dkreq" =>"DK Requirement,int|0"
    ),
    "prefs"   =>array(
			"Specialty - Mystical Powers User Prefs,title",
			"level"=>"Skill Level,int|0",
			"exp"  =>"Skill Exp,int|0",
		),
	);
	return $info;
}

function specialtymysticalpowers_install(){
	$sql = "DESCRIBE " . db_prefix("accounts");
	$result = db_query($sql);
	$specialty="MP";
	while($row = db_fetch_assoc($result)) {
		// Convert the user over
		if ($row['Field'] == "mysticalpowers") {
			debug("Migrating mysticalpowers field");
			$sql = "INSERT INTO ".db_prefix("module_userprefs")." (modulename,setting,userid,value) SELECT 'specialtymysticalpowers', 'skill', acctid, mysticalpowers FROM " . db_prefix("accounts");
			db_query($sql);
			debug("Dropping mysticalpowers field from accounts table");
			$sql = "ALTER TABLE ".db_prefix("accounts")." DROP mysticalpowers";
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
function specialtymysticalpowers_uninstall(){
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='MP'";
	db_query($sql);
	return true;
}

function specialtymysticalpowers_dohook($hookname,$args){
	global $session,$resline;
	$spec = "MP";
	$name = "Mystical Powers";
	$ccode = "`%";
	$maxmana=get_module_pref("maxmana","mana");
	$curmana=get_module_pref("curmana","mana");
	$lvl=get_module_pref("level");
	switch ($hookname) {
	/*
	case "charstats":
		if($session['user']['specialty'] == $spec) {
			require_once('./lib/javascript.php');
      addcharstat("Vital Info");
      addcharstat(color_mpdeout('Skill','008080','00FFFF'), translate_inline($name)."`n(Level $lvl)");
		}
	break;
	*/
	case "choose-specialty":
	  if(get_module_setting("dkreq")<=$session['user']['dragonkills']){
		if ($session['user']['specialty'] == "" ||
				$session['user']['specialty'] == '0') {
			addnav("$ccode$name`0","newday.php?setspecialty=".$spec."$resline");
			$t1 = translate_inline("Dabbling in mystical forces");
			$t2 = appoencode(translate_inline("$ccode$name`0"));
			rawoutput("<a href='newday.php?setspecialty=$spec$resline'>$t1 ($t2)</a><br>");
			addnav("","newday.php?setspecialty=$spec$resline");
		}}
	break;
	case "set-specialty":
		if($session['user']['specialty'] == $spec) {
			page_header($name);
			output("`3Growing up, you remember knowing there was more to the world than the physical, and what you could place your hands on.");
			output("You realized that your mind itself, with training, could be turned into a weapon.");
			output("Over time, you began to control the thoughts of small creatures, commanding them to do your bidding, and also to begin to tap into the mystical force known as mana, which could be shaped into the elemental forms of fire, water, ice, earth, and wind.");
			output("To your delight, it could also be used as a weapon against your foes.");
		}
	break;
	case "specialtycolor":
		$args[$spec] = $ccode;
	break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
	break;
	case "specialtymodules":
		$args[$spec] = "specialtymysticalpowers";
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
  	  apply_buff("mp$skill",array(
        "startmsg"        =>$s['startmsg'],
        "name"            =>$s['name'],
        "rounds"          =>$s['rounds'],
        "effectmsg"       =>$s['effectmsg'],
  			"effectnodmgmsg"  =>$s['effectnodmgmsg'],
  			"effectmpilmsg"   =>$s['effectmpilmsg'],
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
  			"schema"          =>"module-specialtymysticalpowers"
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

function specialtymysticalpowers_run(){
}
?>
