<?php

function alignment_getmoduleinfo(){
	$info = array(
		"name"=>"Alignment Core",
		"author"=>"Chris Vorndran<br/>`6Original Script by: `QWebPixie",
		"version"=>"1.82",
		"category"=>"Stat Display",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=64",
		"vertxtloc">"http://dragonprime.net/users/Sichae/",
		"description"=>"This module will display the alignment of a character (Evil, Neutral, Good). Certain events in the LotGD universe will affect this alignment.",
		"settings"=>array(
			"Alignment Settings,title",
				"evilalign"=>"What number is evil alignment,int|-33",
				"Any number under the evil number will make the user show up evil. You can use negative numbers.,note",
				"goodalign"=>"What number is good alignment,int|33",
				"Any number above the good number will make the user show up good,note",
				"Any number between evil and good number the user shows up neutral,note",
				"display-num"=>"Display a number alongside of the Alignment statement?,bool|1",
			"Maximum/Minimum Settings,title",
				"reset"=>"Reset user's alignment if it goes over/under the maximum/minimum (see below),bool|0",
				"max-num"=>"What is the maximum alignment?,int|100",
				"min-num"=>"What is the minimum alignment?,int|-100",
			"Other Settings,title",
				"shead"=>"What Stat heading does this go under,text|Vital Info",
				"pvp"=>"Does PVP affect Alignment,bool|1",
				"Whether to remove or add is based on a comparison of the warrior's alignment.,note",
				"How much to remove or add is based from the character's level divided by two.,note",
				"In the neutral case it is a 50/50 chance either way. Level is divided by 3 for amount to change.,note",
		),
		"prefs-mounts"=>array(
			"Mount Alignment Settings,title",
			"Please note that this change happens at newday.,note",
			"al"=>"How much does having this mount affect a person's alignment?,int|0",
			"0 This value to disable. You may also set negative numbers.,note",
		),
		"prefs-creatures"=>array(
			"Creature Alignment Settings,title",
			"al"=>"How much does slaying this creature affect a person's alignment?,int|0",
			"0 This value to disable. You may also set negative numbers.,note",
		),
		"prefs"=>array(
	    "Alignment user preferences,title",
			"alignment"=>"Current alignment number,text|none",
		),
	);
	return $info;
}

function alignment_install(){
	module_addhook("biostat");
	module_addhook("charstats");
	module_addhook("newday");
	module_addhook("battle-victory");
    return true;
}

function alignment_uninstall(){
    return true;
}

function alignment_dohook($hookname,$args){
	global $session,$badguy,$options;
	$title = translate_inline("Alignment");
	$good = translate_inline("`@Good`0");
	$evil = translate_inline("`\$Evil`0");
	$neutral = translate_inline("`6Neutral`0");
	$evilalign = get_module_setting('evilalign','alignment');
	$goodalign = get_module_setting('goodalign','alignment');
    switch($hookname){
		case "newday":
			require_once("modules/alignment/func.php");

			$max_num = get_module_setting("max-num");
			$min_num = get_module_setting("min-num");
			if (get_align() == "none"){
				set_align(round(($max_num + $min_num)/2));
			}

			$id = $session['user']['hashorse'];
			if ($id){
				$al = get_module_objpref("mounts",$id,"al");
				if ($al != "")
					align($al);
			}
			if (get_module_setting("reset")){
				$align = get_align();
				if ($align > $max_num){
					set_align($max_num);
				}elseif ($align < $min_num){
					set_align($min_num);
				}
			}
			break;
		case "charstats":
			$val = get_module_pref("alignment");
			$extra = "";
			if (get_module_setting("display-num")) $extra = "(`b$val`b)";
			if ($val >= $goodalign){
				$color = sprintf("`b%s`b %s",$good,$extra);
			}
			if ($val <= $evilalign){
				$color = sprintf("`b%s`b %s",$evil,$extra);
			}
			if ($val > $evilalign && $val < $goodalign){
				$color = sprintf("`b%s`b %s",$neutral,$extra);
			}
			$area = get_module_setting("shead");
			setcharstat($area,$title,$color);
			break;		
		case "biostat":
			require_once("modules/alignment/func.php");
			$useralign = get_align($args['acctid']);
			if ($useralign >= $goodalign) output("`^Alignment: %s`n",$good);
			if ($useralign <= $evilalign) output("`^Alignment: %s`n",$evil);
			if ($useralign > $evilalign && $useralign < $goodalign) output("`^Alignment: %s`n", $neutral);
			break;
		case "battle-victory":
			if ($options['type'] == "pvp" && get_module_setting("pvp")){
				$ual = get_module_pref("alignment");
				$al = get_module_pref("alignment","alignment",$badguy['acctid']);
				if ($al > $goodalign && $ual < $evilalign){
					$new = round($session['user']['level']/2);
					output("`n`bYou have smote a good person, and as your are evil... it makes you more evil.`b`0`n");
					require_once("modules/alignment/func.php");
					align("-$new");
				}elseif($al < $evilalign && $ual > $goodalign){
					$new = round($session['user']['level']/2);
					output("`n`bYou have destroyed an evil person, and as you are good... it makes you more good.`b`0`n");
					require_once("modules/alignment/func.php");
					align("+$new");
				}else{
					switch (e_rand(1,2)){
						case 1:
							$new = round($session['user']['level']/3);
							output("`n`bYou have destroyed a person... strangely, it makes you more good.`b`0`n");
							require_once("modules/alignment/func.php");
							align("+$new");
							break;
						case 2:
							$new = round($session['user']['level']/3);
							output("`n`bYou have destroyed a person... strangely, it makes you more evil.`b`0`n");
							require_once("modules/alignment/func.php");
							align("-$new");
							break;
						}
					}
			}
			if ($options['type'] == 'forest' || $options['type'] == 'travel'){
				debug($options);
				debug($badguy);
				$id = $badguy['creatureid'];
				$al = get_module_objpref("creatures",$id,"al");
				if ($al != ""){
					require_once("modules/alignment/func.php");
					align($al);
				}
			}
			break;
	}
	return $args;
}
?>
