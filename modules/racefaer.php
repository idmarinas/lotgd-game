<?php
// City-Less Functionality adopted from racefelyne of core.

function racefaer_getmoduleinfo(){
    $info = array(
        "name"=>"Race - Faerie",
        "version"=>"1.11",
        "author"=>"Chris Vorndran",
        "category"=>"Races",
        "download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=40",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"Race. Buff based on Charm. Female Specific",
        "settings"=>array(
            "Faerie Race Settings,title",
            "minedeathchance"=>"Chance for Faerie to die in the mine,range,0,100,1|25",
			"divide"=>"Charm is divided by this value to give buff,int|5",
			"max"=>"Cap for the charm buff calculation provided to Faeries?,int|5",
			"Calculation: (1+((1+floor(\$faer))/<defense>)).,note",
			"mindk"=>"How many DKs do you need before the race is available?,int|5",
        ),
        );
    return $info;
}

function racefaer_install(){
	if (!is_module_installed("raceelf")) {
		output("The Faerie only choose to live with elves.   You must install that race module.");
		return false;
	}
    module_addhook("chooserace");
    module_addhook("setrace");
	module_addhook("newday");
	module_addhook("racenames");
    module_addhook("raceminedeath");
	module_addhook("charstats");
    return true;
}

function racefaer_uninstall(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$gname = get_module_setting("villagename");
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Faerie'";
	db_query($sql);
	if ($session['user']['race'] == 'Faerie')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racefaer_dohook($hookname,$args){
    //yeah, the $resline thing is a hack.  Sorry, not sure of a better way
    //to handle this.
    // It could be passed as a hook arg?
    global $session,$resline;
	if (is_module_active("raceelf")) {
		$city = get_module_setting("villagename", "raceelf");
	} else {
		$city = getsetting("villagename", LOCATION_FIELDS);
	}
    $race = "Faerie";
	$divide = get_module_setting("divide");
    $faer = (round($session['user']['charm']/$divide));
	if ($faer > get_module_setting("max")) $faer = get_module_setting("max");
    switch($hookname){
    case "raceminedeath":
        if ($session['user']['race'] == $race) {
            $args['chance'] = get_module_setting("minedeathchance");
            $args['racesave'] = "Fortunately your Faerie skill let you escape unscathed.`n";
        }
        break;
	case "racenames":
		$args[$race] = $race;
		break;
    case "charstats":
        if ($session['user']['race']==$race){
            addcharstat("Vital Info");
            addcharstat("Race", $race);
        }
        break;
     case "chooserace":
		if ($session['user']['sex']==SEX_MALE)
		    break;
        if ($session['user']['dragonkills'] < get_module_setting("mindk"))
			break;
        output("<a href='newday.php?setrace=Faerie$resline'>The land of Pixies and Faeries, %s</a>, `5hidden away from the world. `^Faerie`0`5-built houses, capped with mushrooms. Hidden in the deepest of hollows, protected from the world of the normal folk. You are a very small being, only able to fly. You feel the need to help others.`n`n`0", $city,true);
        addnav("`^F`5aerie`0","newday.php?setrace=Faerie$resline");
        addnav("","newday.php?setrace=Faerie$resline");
        break;
    case "setrace":
        if ($session['user']['race']==$race){
            output("`^As a faerie, you feel your cuteness protect you.`nYou gain extra defense!");
            if (is_module_active("cities")) {
                if ($session['user']['dragonkills']==0 &&
                        $session['user']['age']==0){
                    //new farmthing, set them to wandering around this city.
                    set_module_setting("newest-$city",
                            $session['user']['acctid'],"cities");
                }
                set_module_pref("homecity",$city,"cities");
                $session['user']['location']=$city;
            }
        }
        break;
    case "newday":
        if ($session['user']['race']==$race){
            racefaer_checkcity();
            apply_buff("racialbenefit",array(
                "name"=>"`@Faerie Talisman`0",
                "defmod"=>"(<defense>?(1+((1+floor($faer))/<defense>)):0)",
                "allowintrain"=>1,
                "allowinpvp"=>1,
                "rounds"=>-1,
				"schema"=>"module-racefaer",
                )
            );
        }
        break;
    }
    return $args;
}

function racefaer_checkcity(){
    global $session;
    $race="Faerie";
    if (is_module_active("raceelf")) {
		$city = get_module_setting("villagename", "raceelf");
	} else {
		$city = getsetting("villagename", LOCATION_FIELDS);
	}
	
	if ($session['user']['race']==$race && is_module_active("cities")){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
    return true;
}

function racefaer_run(){
}
?>