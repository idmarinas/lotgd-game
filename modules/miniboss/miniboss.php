<?php
global $session;
$op = httpget('op');
$dks=$session['user']['dragonkills'];
$type=ceil($dks/10);
if ($dks==0) $type=1;
elseif ($type>10) $type=10;
$num=$dks-((floor($dks/10))*10);
$prefix=array("`\$Fire ","`QOrange ","`^Yellow ","`6Gold ","`2Green ","`@Forest ","`#Ocean ","`3Sky ","`&Snow ","`)Death ");
$name=get_module_setting("monster".$type);
$color=$prefix[$num];
$header=color_sanitize($prefix[$num]);
page_header("%s%s",$header,$name);
if ($op=="enter"){
	output("`b`c%s %s Encounter`b`c",$color,$name);
	output("`n`0There's a %s %s`0 that has been threatening the safety of the kingdom.  You realize that this will be a good test of your skills and a source of glory for your name!",$color,$name);
	output("`n`nIt will not be an easy battle, so you should be at your best.");
	output("`n`nAre you  ready?");
	addnav("Yes","runmodule.php?module=miniboss&op=attack");
	addnav("No","forest.php");
}
if ($op=="traintoboss"){
	page_header("Bluspring's Warrior Training");
	output("`b`cBluspring's Warrior Training`b`c");
	output("`^'I see that you're here  for training.  Unfortunately, I cannot train you right now.  There are bigger problems in the kingdom.'");
	output("`n`n`^'Currently, there is a %s %s `^attacking innocent villagers and we need your help.  Go to the forest and seek out this bane.",$color,$name);
	output("When you return, we'll be ready to resume your training. You won't be able to find the `@Green Dragon`^ until this menace is defeated.'");
	villagenav();
}
if ($op=="forcedfight"){
	output("`b`c%s %s Encounter`b`c",$color,$name);
	output("`n`%Before you get a chance to think about what's going on in the world, you're attacked by a %s %s`%!",$color,$name);
	addnav(array("Fight the %s %s",$color,$name),"runmodule.php?module=miniboss&op=attack");
}
if ($op=="finish"){
	output("`b`c%s %s Defeated!`b`c",$color,$name);
	$expmultiply = e_rand(8,21);
	$expbonus=$session['user']['dragonkills']*4;
	$expgain =($session['user']['level']*$expmultiply+$expbonus);
	$session['user']['experience']+=$expgain;
	output("`n`%Congratulations! You have helped the kingdom by destroying the %s %s`%.",$color,$name);
	output("`n`nYou gain `^%s`% experience.",$expgain);
	if (is_module_active("cities")){
		$city = getsetting("villagename", LOCATION_FIELDS);
		$capital = $session['user']['location']==$city;
		if ($capital){
			villagenav();
		}else{
			addnav("To the Forest","forest.php");
		}
	}else addnav("To the Forest","forest.php");
}
if ($op=="attack"){
	$monster=$color.$name;
	$weapon=get_module_setting("weapon".$type);
	$badguy = array(
		"creaturename"=>$monster,
		"creatureweapon"=>$weapon,
		"creaturelevel"=>$session['user']['level']+1,
		"creatureattack"=>$session['user']['attack']*get_module_setting("att".$type),
		"creaturedefense"=>$session['user']['defense']*get_module_setting("def".$type),
		"creaturehealth"=>round($session['user']['hitpoints']*get_module_setting("hp".$type)),
		"diddamage"=>0);
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="fight"){ $battle=true; }
if ($battle){       
	include("battle.php");  
	if ($victory){
		set_module_pref("miniboss",1);
		addnews("%s `@ has proven great strength and valor by ridding the kingdom of a %s`@. Hazzah!",$session['user']['name'],$badguy['creaturename']);
		output("`n`#You've killed the %s`#!!",$badguy['creaturename']);
		addnav("Continue","runmodule.php?module=miniboss&op=finish");
	}elseif($defeat){
		set_module_pref("miniboss",2);
		$exploss = round($session['user']['experience']*get_module_setting("exp".$type)/100);
		output("`n`n`4You have been defeated by a %s`4.  You have died.",$badguy['creaturename']);
		output("`n`n`^All gold on hand has been lost!`n");
		output("`#You lose `^%s `#experience.",$exploss);
		output("`n`n`c`@`bYou may begin fighting again tomorrow.`c`b");
		addnav("Daily news","news.php");
		$session['user']['experience']-=$exploss;
		$session['user']['alive'] = false;
		$session['user']['hitpoints'] = 0;
		$session['user']['gold']=0;
		addnews("%s `@was killed trying to defend the kingdom from a %s`@.",$session['user']['name'],$badguy['creaturename']);
	}else{
		require_once("lib/fightnav.php");
		fightnav(true,false,"runmodule.php?module=miniboss");
	}
}
page_footer();
?>
