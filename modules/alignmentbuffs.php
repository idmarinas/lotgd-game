<?php
 
function alignmentbuffs_getmoduleinfo(){
	$info = array(
		"name"=>"Alignment Buffs",
		"version"=>"1.0",
		"author"=>"SexyCook",
		"category"=>"General",
		"download"=>"",
		"settings"=>array(
			"First Buff,title",
				"activate1"=>"Buff activates on alignment under,int|-2500",
				"buffname1"=>"Name of the buff,text|Bad to the bones",
				"buffturns1"=>"Number of turns for the buff,int|500",
				"buffatt1"=>"Attack modifier for the buff (1 equals 100% meaning no change),text|1.3",
				"buffdef1"=>"Defense modifier for the buff (1 equals 100% meaning no change),text|0.85",
			"Second Buff,title",
				"activate2"=>"Buff activates on alignment under x and over that of first buff,int|-1250",
				"buffname2"=>"Name of the buff,text|Son of the devil",
				"buffturns2"=>"Number of turns for the buff,int|250",
				"buffatt2"=>"Attack modifier for the buff (1 equals 100% meaning no change),text|1.2",
				"buffdef2"=>"Defense modifier for the buff (1 equals 100% meaning no change),text|0.9",
			"Third Buff,title",
				"activate3"=>"Buff activates on alignment under x and over that of second buff,int|-625",
				"buffname3"=>"Name of the buff,text|A wee bit bad",
				"buffturns3"=>"Number of turns for the buff,int|100",
				"buffatt3"=>"Attack modifier for the buff (1 equals 100% meaning no change),text|1.1",
				"buffdef3"=>"Defense modifier for the buff (1 equals 100% meaning no change),text|0.95",
			"Forth Buff,title",
				"activate4"=>"Buff activates on alignment over x and under that of forth buff,int|625",
				"buffname4"=>"Name of the buff,text|In accord with nature",
				"buffturns4"=>"Number of turns for the buff,int|100",
				"buffatt4"=>"Attack modifier for the buff (1 equals 100% meaning no change),text|0.9",
				"buffdef4"=>"Defense modifier for the buff (1 equals 100% meaning no change),text|1.1",
			"Fifth Buff,title",
				"activate5"=>"Buff activates on alignment over x and under that of fifth buff,int|1250",
				"buffname5"=>"Name of the buff,text|Shield of faith",
				"buffturns5"=>"Number of turns for the buff,int|250",
				"buffatt5"=>"Attack modifier for the buff (1 equals 100% meaning no change),text|0.85",
				"buffdef5"=>"Defense modifier for the buff (1 equals 100% meaning no change),text|1.2",
			"Sixth Buff,title",
				"activate6"=>"Buff activates on alignment over,int|2500",
				"buffname6"=>"Name of the buff,text|Protector of the world",
				"buffturns6"=>"Number of turns for the buff,int|500",
				"buffatt6"=>"Attack modifier for the buff (1 equals 100% meaning no change),text|0.8",
				"buffdef6"=>"Defense modifier for the buff (1 equals 100% meaning no change),text|1.3",
		),
		"requires"=>array(
			"alignment" => "Alignment Core, Chris Vorndran",
			),
		);
		return $info;
}

function alignmentbuffs_install(){
	module_addhook("newday");
	return true;
}

function alignmentbuffs_uninstall(){
    return true;
}

function alignmentbuffs_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "newday":
			
			$alignment=get_module_pref("alignment","alignment");
			debug("align = $alignment");
			
			if($alignment<=get_module_setting("activate1")){
				$buff = array();
				$buff['name'] = get_module_setting("buffname1");
				$buff['rounds'] = get_module_setting("buffturns1");
				$buff['atkmod'] = get_module_setting("buffatt1");
				$buff['defmod'] = get_module_setting("buffdef1");
				$buff['schema'] = "module-alignmentbuffs";
				apply_buff('alignment',$buff);
				output("`$ `nThere's no one more evil than you. You attack without thought, giving you a nasty edge on every fight.`n");
			}
			elseif($alignment<=get_module_setting("activate2")){
				$buff = array();
				$buff['name'] = get_module_setting("buffname2");
				$buff['rounds'] = get_module_setting("buffturns2");
				$buff['atkmod'] = get_module_setting("buffatt2");
				$buff['defmod'] = get_module_setting("buffdef2");
				$buff['schema'] = "module-alignmentbuffs";
				apply_buff('alignment',$buff);
				output("`$ `nYou truly are the spawn of the devil. Creatures cower at your passing, making it easyer to attack them.`n");
			}
			elseif($alignment<=get_module_setting("activate3")){
				$buff = array();
				$buff['name'] = get_module_setting("buffname3");
				$buff['rounds'] = get_module_setting("buffturns3");
				$buff['atkmod'] = get_module_setting("buffatt3");
				$buff['defmod'] = get_module_setting("buffdef3");
				$buff['schema'] = "module-alignmentbuffs";
				apply_buff('alignment',$buff);
				output("`$ `nYou feel a bit like a rebel today. While you attack without regret, you don't waste a thought about your own skin.`n");
			}
			elseif($alignment>=get_module_setting("activate6")){
				$buff = array();
				$buff['name'] = get_module_setting("buffname6");
				$buff['rounds'] = get_module_setting("buffturns6");
				$buff['atkmod'] = get_module_setting("buffatt6");
				$buff['defmod'] = get_module_setting("buffdef6");
				$buff['schema'] = "module-alignmentbuffs";
				apply_buff('alignment',$buff);
				output("`2 `nThe world is a safe place because of people like you. In return, the world keeps you safe of but the worst attacks against you.`n");
			}
			elseif($alignment>=get_module_setting("activate5")){
				$buff = array();
				$buff['name'] = get_module_setting("buffname5");
				$buff['rounds'] = get_module_setting("buffturns5");
				$buff['atkmod'] = get_module_setting("buffatt5");
				$buff['defmod'] = get_module_setting("buffdef5");
				$buff['schema'] = "module-alignmentbuffs";
				apply_buff('alignment',$buff);
				output("`2 `nYour faith in goodness in yourself and everyone else shields you from harm.`n");
			}
			elseif($alignment>=get_module_setting("activate4")){
				$buff = array();
				$buff['name'] = get_module_setting("buffname4");
				$buff['rounds'] = get_module_setting("buffturns4");
				$buff['atkmod'] = get_module_setting("buffatt4");
				$buff['defmod'] = get_module_setting("buffdef4");
				$buff['schema'] = "module-alignmentbuffs";
				apply_buff('alignment',$buff);
				output("`2 `nYou can feel the goodness all around you. A blanket of warm air protects you, but also hinders your attacks.`n");
			}
			break;
	}
	return $args;
}

function alignmentbuffs_run(){
	global $session;
}

?>
