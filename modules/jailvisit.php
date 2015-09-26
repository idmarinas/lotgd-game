<?php
require_once("common.php");
require_once("lib/fightnav.php");
require_once("lib/pvpwarning.php");
require_once("lib/pvplist.php");
require_once("lib/pvpsupport.php");
require_once("lib/http.php");
require_once("lib/taunt.php");
require_once("lib/villagenav.php");
require_once("lib/battle-skills.php");
require_once("lib/systemmail.php");
function jailvisit_getmoduleinfo(){
		$info = array(
			"name"=>"Visit a foe",
			"author"=>"Sixf00t4 - from Sichae's Estate Break-in",
			"version"=>"20050426",
			"category"=>"Jail",
			"download"=>"http://dragonprime.net/users/sixf00t4/jailvisit.zip",
			"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
			"requires"=>array(
				"jail"=>"20050310|by sixf00t4, http://dragonprime.net/users/sixf00t4/jail.zip",
			),
			"settings"=>array(
				"Jail Visit General Settings,title",
					"aliaff"=>"Is alignment affected by visiting,bool|1",
					"alatk"=>"Alignment is decrease by this much - after Attacking,int|5",
				"Sheriff Settings,title",
					"sheriff"=>"Does the sheriff protect the jailed?,bool|1",
                    "whensheriff"=>"Does the sheriff attack after or before PvP?,enum,1,After,2,Before",
                    "sheriffchance"=>"Chance player will be attacked by sheriff,int|100",
					"atk"=>"Multiplier of attacker's attack to get sheriff's Attack,floatrange,0,2,.02|1.7",
					"def"=>"Multiplier of attacker's defense to get sheriff's Defense,floatrange,0,2,.02|1.7",
					"hp"=>"Multiplier of attacker's maxHP to get sheriff's Hitpoints,floatrange,0,2,.02|2",
                    "jailloss"=>"Player gets sent to jail if losing to sheriff rather than death?,bool|1",
                    ),
            "prefs"=>array(
                "Jail Visiting preferences,title",
                "sfight"=>"Is user fighting the sheriff,bool|0",
                ),
		);
	return $info;
}
function jailvisit_install(){
	module_addhook("sheriff-jail");
	module_addhook("pvpcount");
	return true;
}
function jailvisit_uninstall(){
	return true;
}
function jailvisit_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "sheriff-jail":
            addnav("Visit a foe","runmodule.php?module=jailvisit&op=foe");
            break;
	 case "pvpcount":
			if ($args['loc'] != translate_inline("`7The Jail")) break;
		    $args['handled'] = 1;
			if ($args['count'] == 1) {
	            output("`&There is `^1`& person sleeping in the jail whom you might find interesting.`0`n");
		    } else {
			    output("`&There are `^%s`& people sleeping in the jail whom you might find interesting.`0`n", $args['count']);
	        }
	    break;
		}
	return $args;
}
function jailvisit_run(){
	global $session,$pvptime,$pvptimeout;
    
	$pvptime = getsetting("pvptimeout",600);
	$pvptimeout = date("Y-m-d H:i:s",strtotime("-$pvptime seconds"));
	page_header("Jail visit");
 

require_once("lib/pvpwarning.php");
require_once("lib/pvplist.php");
require_once("lib/taunt.php");
require_once("lib/battle-skills.php");
require_once("lib/systemmail.php");
 
	$op2 = httpget('op2');
	$op = httpget('op');
	$id = httpget('id');
	$jailloc = translate_inline("`7The jail");


   
	global $session,$pvptime,$pvptimeout;
	$pvptime = getsetting("pvptimeout",600);
	$pvptimeout = date("Y-m-d H:i:s",strtotime("-$pvptime seconds"));


if ($op=="foe"){
page_header("Jail Visit - Attack");
   
    if ($session['user']['playerfights']>0){
        output("The sheriff looks at his watch to make sure that visiting hours are not over.  Since you still have some time, he pulls out the mugshots from his desk, and asks who you would like to see.");
		if(get_module_setting("sheriff")==1 && get_module_setting("whensheriff")==2){
                $sheriffchance=get_module_setting("sheriffchance");
                if(e_rand(1,100)<$sheriffchance){
					output("`#If you want to get to anyone, you are going to have to go through The Sheriff first...");                
                    addnav("Take out the sheriff","runmodule.php?module=jailvisit&op=sheriff");
                   }        
                }else{ addnav("Take a look at the list","runmodule.php?module=jailvisit&op=atklist");}
    }else{
        output("The sheriff says that visiting hours are over.  Perhaps you shouldn't have spent your time slaying others before hand.");
    }  
addnav("Back to the jail","runmodule.php?module=jail"); 
}elseif ($op=="sheriff"){
    $guardname= get_module_setting("sheriffname","jail");
    $hp = get_module_setting("hp");
    $atk = get_module_setting("atk");
    $def = get_module_setting("def");
set_module_pref('sfight',1);
    $badguy = array(
    "creaturename"=>"$guardname",
    "creaturelevel"=>$session['user']['level']+5,
    "creatureweapon"=>"Club",
    "creatureattack"=>$session['user']['attack']*$atk,
    "creaturedefense"=>$session['user']['defense']*$def,
    "creaturehealth"=>round($session['user']['maxhitpoints']*$hp),
    "diddamage"=>0,);
    $session['user']['badguy'] = createstring($badguy);
    $op = "setup";
    httpset('op', $op);
            
}elseif($op=="atklist"){
				page_header("Jail Vist - Attack");

				$days = getsetting("pvpday",1);
				$exp = getsetting("pvpminexp",1000);
				$clanrankcolors=array("`!","`#","`^","`&");
				$lev1 = $session['user']['level']-1;
				$lev2 = $session['user']['level']+2;                
				$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
				$id = $session['user']['acctid'];
				$loc = $session['user']['location'];
				$sql = "SELECT name, alive, ".db_prefix("module_userprefs").".value AS location, sex, level, laston, loggedin, login, pvpflag, clanshort, clanrank
				FROM ".db_prefix("accounts")."
				LEFT JOIN ".db_prefix("clans")." ON ".db_prefix("clans").".clanid=".db_prefix("accounts").".clanid
				INNER JOIN ".db_prefix("module_userprefs")." ON ".db_prefix("accounts").".acctid=".db_prefix("module_userprefs").".userid
				WHERE (locked=0)
				AND ".db_prefix("module_userprefs").".setting = 'playerloc'
				AND ".db_prefix("module_userprefs").".modulename = 'jail'
				AND (age>$days OR dragonkills>0 OR pk>0 OR experience>$exp)
				AND (level>=$lev1 AND level<=$lev2) AND (alive=1)
				AND (laston<'$last' OR loggedin=0) AND (acctid<>$id)
				ORDER BY location='$loc' DESC, location, level DESC,
				experience DESC, dragonkills DESC";
				pvplist($loc,"runmodule.php?module=jailvisit","&op=combat&pvp=1", $sql);
				addnav("Actions");
				addnav("Refresh List","runmodule.php?module=jailvisit&op=atklist");
				addnav("Return");
				addnav("Return to the Jail","runmodule.php?module=jail");

                } elseif ($op=="combat") {
			// Okay, we've picked a person to fight.
	        require_once("lib/pvpsupport.php");
		    $name = httpget("name");
			$badguy = setup_target($name);
	        $failedattack = false;
		    if ($badguy===false) {
			    output("`n`nYou should head back to the jail.`n");
				addnav("Return");
				addnav("Return to the Jail","runmodule.php?module=jail");
	        } else {
		        $battle = true;
			    $session['user']['badguy']=createstring($badguy);
				$session['user']['playerfights']--;
	        }
		}
        if ($op == "setup"){
		suspend_buffs("allowinpvp","`\$Your mount has to wait in the waiting area.`0");
		$op = "fight";

	}
	if ($op=="fight"){
		$battle = true;
	}
    if ($battle){
        include_once("battle.php");
        if ($victory){
            if(get_module_pref('sfight')==0){
                require_once("lib/pvpsupport.php");
				$killedin = $badguy['location'];
				pvpvictory($badguy, $killedin);
                addnews("`4%s`3 defeated `4%s`3 while they were in the jail.", $session['user']['name'], $badguy['creaturename']);
                $badguy=array();
				addnav("Return");
        		if(get_module_setting("sheriff")==1 && get_module_setting("whensheriff")==1){
                    $sheriffchance=get_module_setting("sheriffchance");
                    if(e_rand(1,100)<$sheriffchance){
					output("`n`#The sheriff comes back in to check on you and sees what you have done.  Time to fight the sheriff.");                
                    addnav("Take out the sheriff","runmodule.php?module=jailvisit&op=sheriff");
                   }        
                }else {addnav("Return to the Jail","runmodule.php?module=jail");}
			unsuspend_buffs("allowinpvp","`n`nYou come back into the main waiting area and your mount puts down the magazine it was reading and joins you again.`0");
		$alatk = get_module_pref("alatk");
		if (is_module_active("alignment") && get_module_setting("aliaff") == 1){
			align("-$alatk");
		}
        }else{
        set_module_pref('sfight',0);
        addnews("".$session['user']['name']." defeated the sheriff while visiting a foe!");
		if(get_module_setting("whensheriff")==2){
        output("`n`nYou quickly pull out the list of the jailed to see where your victim is being held.");
		addnav("Look at the list","runmodule.php?module=jailvisit&op=atklist");
		}else{
         output("`n`nYou have defeated the sheriff!  You better get out of here!.");
		addnav("Back to the village","village.php");       
        }
        unsuspend_buffs("allowinpvp","`n`nfirst You come back into the main waiting area and your mount puts down the magazine it was reading and joins you again.`0");
		$alatk = get_module_pref("alatk");
		if (is_module_active("alignment") && get_module_setting("aliaff") == 1){
			align("-$alatk");
		}
        }
        }elseif ($defeat){
        if(get_module_pref('sfight')==0){
				require_once("lib/pvpsupport.php");
				$killedin = $badguy['location'];
				$taunt = select_taunt_array();
				pvpdefeat($badguy, $killedin, $taunt);
                addnews("`4%s`3 was defeated while attacking `4%s`3 in the Jail.`n%s", $session['user']['name'], $badguy['creaturename'], $taunt);
				output("`n`n`&You are sure that someone, sooner or later, will stumble over your corpse and return it to %s for you." , $session['user']['location']);
			}else{
        set_module_pref('sfight',0);
				output("`nThe Sheriff strikes down one with a final blow and knocks you out cold.");
				addnews("%s fell at the feet of the Sheriff while trying to visit a foe.",$session['user']['name']);
				addnav("Return");
                if(get_module_setting("jailloss")==1){
                    set_module_pref("injail",1,"jail");
				addnav("To Your Cell","runmodule.php?module=jail");
                $session['user']['hitpoints']=1;
                    }else{ 
                $exploss = $session['user']['experience']*.1;
				output("`n You lose %s experience.",$exploss);
				$session['user']['experience']-=$exploss;
				debuglog("lost $exploss experience and all gold to the sheriff.");                    
				addnav("Return to the Shades","shades.php");
			}
        }    
		unsuspend_buffs("allowinpvp","`n`nYou come back into the main waiting area and your mount puts down the magazine it was reading and joins you again.`0");
        }else{
            require_once("lib/fightnav.php");
            $allow = true;
            $extra = "";
                        	$script = "runmodule.php?module=jailvisit&op=fight";
            if (get_module_pref('sfight')) {
                $allow = false;
	            fightnav($allow,$allow,$script.$extra);
            }else{
				$allow = true;
	            fightnav($allow,false,$script.$extra);
			}
        }
    }
page_footer();
}
?>