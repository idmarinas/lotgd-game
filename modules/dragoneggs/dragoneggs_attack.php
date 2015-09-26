<?php
function dragoneggs_attack(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$monster=get_module_pref("monster");
	if ($monster==1){
		//Fire Hound
		$level=max(1,$session['user']['level']-1);
		$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = $level AND forest=1 ORDER BY rand(".e_rand().") LIMIT 1";
		$result = db_query($sql);
		$badguy = db_fetch_assoc($result);
		$badguy = modulehook("buffbadguy", $badguy);
		$name=translate_inline("`4Fire Hound");
		$weapon=translate_inline("Blistering Bites");
		$attack=$badguy['creatureattack'];
		$defense=$badguy['creaturedefense'];
		$health=$badguy['creaturehealth'];
	}elseif ($monster==2){
		//Wraith
		$name=translate_inline("`&Wraith");
		$weapon=translate_inline("Soul Sucking Power");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack']+1;
		$defense=$session['user']['defense']*1.1;
		$health=round($session['user']['maxhitpoints']*.92);
	}elseif ($monster==3){
		//Rat
		$name=translate_inline("`qRat");
		$weapon=translate_inline("Rabid Fangs");
		$level=max(1,$session['user']['level']-1);
		$attack=$session['user']['attack']*.9;
		$defense=$session['user']['defense']*.9;
		$health=round($session['user']['maxhitpoints']*.9);
	}elseif ($monster==4){
		//Heat Vampire
		$name=translate_inline("`QHeat Vampire");
		$weapon=translate_inline("Blood Rage");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack']*1.2;
		$defense=$session['user']['defense']*1.3;
		$health=round($session['user']['maxhitpoints']*1.04);
	}elseif ($monster==5){
		//Rhthithc
		$name=translate_inline("`\$Rhthithc");
		$weapon=translate_inline("Biting Mandibles");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack']*1.3;
		$defense=$session['user']['defense']*1.2;
		$health=round($session['user']['maxhitpoints']*1.1);
	}elseif ($monster==6){
		//Zombie
		$name=translate_inline("`\$Zombie");
		$weapon=translate_inline("Undead Fingernails");
		$level=max(1,$session['user']['level']-1);
		$attack=$session['user']['attack']*1.5;
		$defense=$session['user']['defense']*.9;
		$health=round($session['user']['maxhitpoints']*e_rand(90,110)/100);
	}elseif ($monster==7){
		//Green Slime
		$name=translate_inline("`@Green Slime");
		$weapon=translate_inline("`@Green Sliminess");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack']*1.3;
		$defense=$session['user']['defense']*1.4;
		$health=round($session['user']['maxhitpoints']*e_rand(90,110)/100);
	}elseif ($monster==8){
		//Lumberjack
		$name=translate_inline("`\$Burly Lumberjack");
		$weapon=translate_inline("Razor Sharp Axe");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack']*1.2;
		$defense=$session['user']['defense']*1.2;
		$health=round($session['user']['maxhitpoints']*e_rand(115,120)/100);
	}elseif ($monster==9){
		//Rat Thing
		$name=translate_inline("`\$Rat-Thing");
		$weapon=translate_inline("Dripping Fangs");
		$level=$session['user']['level']+2;
		$attack=$session['user']['attack']+2;
		$defense=$session['user']['defense']+2;
		$health=round($session['user']['maxhitpoints']*.5);
	}elseif ($monster==10){
		//Gastropian
		$name=translate_inline("`\$Gastropian");
		$weapon=translate_inline("Stomach Acid");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack']*1.2;
		$defense=$session['user']['defense'];
		$health=round($session['user']['maxhitpoints']*e_rand(80,120)/100);
	}elseif ($monster==11){
		//Stalagaryth
		$level=$session['user']['level']+1;
		$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = $level AND forest=1 ORDER BY rand(".e_rand().") LIMIT 1";
		$result = db_query($sql);
		$badguy = db_fetch_assoc($result);
		$badguy = modulehook("buffbadguy", $badguy);
		$name=translate_inline("Stalagaryth");
		$weapon=translate_inline("Rock-like fists");
		$attack=$badguy['creatureattack'];
		$defense=$badguy['creaturedefense'];
		$health=$badguy['creaturehealth'];
	}elseif ($monster==12){
		//Swamgrythph
		$level=$session['user']['level']+1;
		$name=translate_inline("`\$Swamgrythph");
		$weapon=translate_inline("Seaweed");
		$attack==max(1,$session['user']['attack']-3);
		$defense=max(1,$session['user']['defense']-3);
		$health=round($session['user']['maxhitpoints']*e_rand(130,180)/100);
	}elseif ($monster==13){
		//Sheldon Boy
		$level=$session['user']['level']+1;
		$name=translate_inline("`QSheldon Boy");
		$weapon=translate_inline("Sheldon Sword");
		$level=$session['user']['level'];
		$attack=$session['user']['attack']+3;
		$defense=$session['user']['defense']+3;
		$health=round($session['user']['maxhitpoints']*e_rand(100,150)/100);
	}elseif ($monster==14){
		//Blupe
		$name=translate_inline("`!Blupe");
		$weapon=translate_inline("Suffocating bluping");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack'];
		$defense=$session['user']['defense'];
		$health=round($session['user']['maxhitpoints']*e_rand(90,110)/100);
	}elseif ($monster==15){
		//Book Gorilla-Man
		$name=translate_inline("Book Gorilla-Man");
		$weapon=translate_inline("Books From the Shelves");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack']+3;
		$defense=max(1,$session['user']['defense']-1);
		$health=round($session['user']['maxhitpoints']*e_rand(90,110)/100);
	}elseif ($monster==16){
		//Dragon Sympathist
		$level=$session['user']['level']+1;
		$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = $level AND forest=1 ORDER BY rand(".e_rand().") LIMIT 1";
		$result = db_query($sql);
		$badguy = db_fetch_assoc($result);
		$badguy = modulehook("buffbadguy", $badguy);
		$name=translate_inline("Dragon Sympathist");
		$weapon=translate_inline("Poisoned Dagger");
		$attack=$badguy['creatureattack'];
		$defense=$badguy['creaturedefense'];
		$health=$badguy['creaturehealth'];
	}elseif ($monster==17){
		//Crazed Inmate
		$name=translate_inline("Crazed Inmate");
		$weapon=translate_inline("Shiv");
		$level=$session['user']['level']+1;
		$attack=$session['user']['attack']+3;
		$defense=$session['user']['defense']+3;
		$health=round($session['user']['maxhitpoints']*e_rand(90,110)/100);
	}elseif ($monster==18){
		//Robber
		$name=translate_inline("Robber");
		$weapon=translate_inline("Very Nice Weapon");
		$level=$session['user']['level'];
		$attack=$session['user']['attack']+3;
		$defense=$session['user']['defense']+3;
		$health=round($session['user']['maxhitpoints']*e_rand(120,130)/100);
	}elseif ($monster==19){
		//Bear
		$name=translate_inline("Great Big Bear");
		$weapon=translate_inline("its Claws");
		$level=$session['user']['level']+1;
		$attack=round($session['user']['attack']*.85);
		$defense=round($session['user']['defense']*1.3);
		$health=round($session['user']['maxhitpoints']*0.98);
	}elseif ($monster==20 || $monster==21){
		//Newsboy
		$name=translate_inline("Newsboy");
		$weapon=translate_inline("Annoying Persistence");
		$level=$session['user']['level'];
		$attack=round($session['user']['attack']*.85);
		$defense=round($session['user']['defense']*.9);
		if ($monster==20) $health=$session['user']['maxhitpoints'];
		else $health=$session['user']['hitpoints'];
	}
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$level,
		"creatureweapon"=>$weapon,
		"creatureattack"=>$attack,
		"creaturedefense"=>$defense,
		"creaturehealth"=>$health,
		"type"=>"dragoneggs",
	);
	$session['user']['badguy']=createstring($badguy);
}
?>