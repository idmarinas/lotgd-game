<?php
function sanctum_getmoduleinfo(){
	$info = array(
		"name"=>"Order of the Inner Sanctum",
		"author"=>"DaveS",
		"version"=>"1.0",
		"category"=>"Dragon Expansion",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"settings"=>array(
			"Order of the Inner Sanctum,title",
			"sanctumloc"=>"Where is the sanctum Order located?,location|".getsetting("villagename", LOCATION_FIELDS),
			"mindk"=>"Minimum number of dks above Base (from Dragon Eggs Module) required to become a member:,int|0",
			"healnumber"=>"Number of days required to heal a tattoo:,range,1,10,1|2",
			"sanctumnum"=>"Number of Members that have ever joined the order:,int|0",
			"newestmember"=>"Name of the last player to join the order:,text|",
			"chance"=>"Chance to encounter recruiter in the forest:,range,0,100,5|30",
		),
		"prefs"=>array(
			"Order of the Inner Sanctum,title",
			"member"=>"What number member was this player to join?,int|0",
			"tatpain"=>"How long left for the tattoo to heal?,int|0",
			"encounter"=>"Has the player encountered the forest event today?,bool|0",
		),
		"requires"=>array(
			"dragoneggs"=>"1.0|Dragon Eggs Expansion by DaveS",
		),
	);
	return $info;
}
function sanctum_chance() {
	global $session;
	if (get_module_pref("encounter",'sanctum')==1 || $session['user']['dragonkills']<get_module_setting("mindk","sanctum")+get_module_setting("mindk","dragoneggs")) $ret=0;
	else $ret= get_module_setting('chance','sanctum');
	return $ret;
}
function sanctum_install(){
	module_addhook("changesetting");
	module_addhook("newday");
	module_addhook("village");
	module_addhook("bioinfo");
	module_addhook("dragonkill");
	module_addeventhook("forest","require_once(\"modules/sanctum.php\"); 
	return sanctum_chance();");
	return true;
}
function sanctum_uninstall(){
	return true;
}
function sanctum_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "changesetting":
			if ($args['setting'] == "villagename") {
				if ($args['old'] == get_module_setting("sanctumloc")) set_module_setting("sanctumloc", $args['new']);
			}
		break;
		case "newday":
			set_module_pref("encounter",0);
			if (get_module_pref("member")==-1) set_module_pref("member",0);
			if (get_module_pref("member")>0 && get_module_pref("tatpain")>0){
				if (get_module_pref("tatpain")>1) {
					output("`n`&Your Order of the Inner Sanctum tattoo is gradually healing.  You wake to the excruciating pain with only `\$one stamina point`&.`n");
					$session['user']['hitpoints']=1;
				}elseif (get_module_pref("tatpain")==1) {
					output("`n`&Your Order of the Inner Sanctum tattoo tattoo has finally healed.`n");
				}
				increment_module_pref("tatpain",-1);
			}
		break;
		case "village":
			if ($session['user']['location'] == get_module_setting("sanctumloc") && get_module_pref("member")<>0){
				tlschema($args['schemas']['tavernnav']);
				addnav($args['tavernnav']);
				tlschema();
				addnav("Order of the Inner Sanctum","runmodule.php?module=sanctum");
			}
		break;
		case "bioinfo":
			if (get_module_pref('member','sanctum',$args['acctid'])>0){
				output("`n`&%s `# has a beautiful tattoo on %s hand that says `&O`)I`&S`#.`n", $args['name'],translate_inline(($args['sex']?"her":"his")));
			}
		break;
		case "dragonkill":
			set_module_pref("member",0);
			set_module_pref("tatpain",0);
		break;
	}
	return $args;
}
function sanctum_runevent($type){
	global $session;
	$op = httpget('op');
	if ($op==""){
		$session['user']['specialinc']="module:sanctum";
		set_module_pref("encounter",1);
		if (get_module_pref("member")==-1){
			output("You see the finely dressed man walk by again. `7'You should head to the Order of the Inner Sanctum as soon as you get a chance.'");
			$session['user']['specialinc']="";
			require_once("lib/forest.php");
			forest(true);
		}elseif(get_module_pref("member")>0  && e_rand(1,5)==1){
			output("You slash and stab at everything.  When the dust settles you realize that you cut the tie off the finely dressed man in charge of recruitment into the Order of the Inner Sanctum.");
			output("`n`nHe doesn't take kindly to this and he revokes your membership!!");
			debuglog("was dismissed from the Inner Sanctum by the Forest Special.");
			set_module_pref("member",0);
			$session['user']['specialinc']="";
			require_once("lib/forest.php");
			forest(true);
		}elseif(get_module_pref("member")>0 ){
			output("You encounter the Finely Dressed Man who tells you that you should take care so that you don't get your membership revoked.");
			output("`n`nBefore you have a chance to respond he disappears.");
			$session['user']['specialinc']="";
			require_once("lib/forest.php");
			forest(true);
		}else{
			if (e_rand(1,4)==1){
				output("You are approached by a finely dressed man.  He introduces himself as a representative of a very elite group of individuals.");
				output("Do you chat a little longer with him?");
				addnav("Yes","forest.php?op=yes");
				addnav("No","forest.php?op=no");
			}else{
				output("You see a strange man in a very expensive suit walk past you but he doesn't seem to take notice of you.");
				$session['user']['specialinc']="";
				require_once("lib/forest.php");
				forest(true);
			}
		}
	}
	if ($op=="yes"){
		output("After some small talk, the man invites you to become a member of the `&Order of the Inner Sanctum.`0 You gladly accept a chance to become a member.");
		output("`n`nHe hands you an invitation and points you to the headquarters in %s.",get_module_setting("sanctumloc"));
		output("`7'Try to stop by as soon as you can.  `&N`)armyan`7 will be waiting for you. The offer expires at the end of the day.'");
		set_module_pref("member",-1);
		$session['user']['specialinc']="";
		require_once("lib/forest.php");
		forest(true);
	}
	if ($op=="no"){
		output("You're not interested in joining anything today and dismiss the finely dressed man.");
		$session['user']['specialinc']="";
		require_once("lib/forest.php");
		forest(true);	
	}
}
function sanctum_run(){
	include("modules/sanctum/sanctum.php");
}
?>