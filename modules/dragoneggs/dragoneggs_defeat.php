<?php
function dragoneggs_defeat(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$monster=get_module_pref("monster");
	$session['user']['hitpoints']=0;
	$session['user']['alive']=false;
	if ($monster==1){
		//Fire Hound
		$exploss = round($session['user']['experience']*.08);
		$session['user']['hitpoints']=1;
		$session['user']['alive']=true;
		rawoutput("<span style='color: #9900FF'>");
		if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
			$innname=getsetting("innname", LOCATION_INN);
			$barkeep = getsetting('barkeep','`tCedrik');
		}else{
			$innname=translate_inline("The Boar's Head Inn");
			$barkeep =translate_inline("`%Cedrik");
		}
		output("`n`0You find yourself in the teeth of the `4Fire Hound`0 when %s`0 comes to your rescue.`n",$barkeep);
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnav(array("Return to %s",$innname),"inn.php");
		villagenav();
		blocknav("shades.php");
		addnews("%s `%was defeated by a `4Fire Hound`% in %s.",$session['user']['name'],$innname);
		debuglog("was killed by a Fire Hound to lose $exploss experience while researching the Inn.");
	}elseif ($monster==2){
		//Wraith
		$exploss = round($session['user']['experience']*.08);
		output("`n`%You feel the wraith suck your energy away.`n");
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `&Wraith`% in the Order of the Inner Sanctum.",$session['user']['name']);
		debuglog("was killed by a wraith to lose $exploss experience that attacked while researching the Order of the Inner Sanctum.");
	}elseif ($monster==3){
		//Rat
		$exploss = round($session['user']['experience']*.05);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `qRat`%.`n");
		output("`b`^All your gold on hand has been stolen by the rat!`b`n");
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `qRat`% in the Old House.",$session['user']['name']);
		debuglog("was killed by a Rat to lose $expgain experience and all gold that attacked while researching the Old House.");
	}elseif ($monster==4){
		//Heat Vampire
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `QHeat Vampire`%.`n");
		output("`b`^All your gold on hand has burned!`b`n");
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `QHeat Vampire`% in the Old House.",$session['user']['name']);
		debuglog("was killed by a Heat Vampire to lose $expgain experience and all gold that attacked while researching the Old House.");
	}elseif ($monster==5){
		//Rhthithc
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Rhthithc`%.`n");
		output("`b`^All your gold on hand is consumed!`b`n");
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Rhthithc`% in %s Square`@.",$session['user']['name'],getsetting("villagename", LOCATION_FIELDS));
		debuglog("was killed by a Rhthithc to lose $expgain experience and all gold that attacked while researching the Capital Town Square.");
	}elseif ($monster==6){
		//Zombie
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Zombie`%.`n");
		output("`b`^All your gold on hand is buried with `\$the Zombie`^!`b`n");
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Zombie`% in the Gypsy's Graveyard.",$session['user']['name']);
		debuglog("was killed by a Zombie to lose $expgain experience and all gold that attacked while researching the Gypsy Seer's Tent.");
	}elseif ($monster==7){
		//Green Slime
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `@Green Slime`%.`n");
		output("`b`^All your gold on hand is `@slimed`^ beyond use!`b`n");
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by some `@Green Slime`% in the Jeweler's Basement.",$session['user']['name']);
		debuglog("was killed by Green Slime to lose $expgain experience and all gold that attacked while researching the Jeweler's Basement.");
	}elseif ($monster==8){
		//Lumberjack
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Lumberjack`%.`n");
		output("`b`^All your gold on hand is taken as 'compensation'!`b`n");
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by some `\$Lumberjack`% at the Tattoo Parlor.",$session['user']['name']);
		debuglog("was killed by lumberjack to lose $expgain experience and all gold while researching dragon eggs at the Tattoo Parlor.");
	}elseif ($monster==9){
		//Rat-Thing
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Rat-Thing`%.`n");
		output("`b`^All your gold is hoarded away by the `\$Rat-Thing`^!`b`n");
		output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Rat-Thing`% at Heidi's Place.",$session['user']['name']);
		debuglog("was killed by rat-thing to lose $expgain experience and all gold while researching dragon eggs at Heidi's Place.");
	}elseif ($monster==10){
		//Gastropian
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Gastropian`%.`n");
		output("`b`^All your gold is lost and your stomach is inhabited by this horrible creature`^!`b`n");
		output("`b`4You lose `#%s experience`4 and `&3 charm`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Gastropian`% at the Merick's Stables.",$session['user']['name']);
		debuglog("was killed by Gastropian to lose $expgain experience and all gold and 3 charm while researching dragon eggs at the Merick's Stables.");
		$session['user']['charm']-=3;
	}elseif ($monster==11){
		//Stalagaryth
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Stalagaryth`%.`n");
		output("`b`^All your gold is lost and your bones eventually mineralize here.`n");
		output("`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Stalagaryth`% at the Merick's Stables.",$session['user']['name']);
		debuglog("was killed by Stalagaryth to lose $expgain experience and all gold while researching dragon eggs at the Merick's Stables.");
	}elseif ($monster==12){
		//Swamgrythph
		$exploss = round($session['user']['experience']*.07);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Stalagaryth`%.`n");
		output("`b`^All your gold is lost and your body is dragged to the ground.`n");
		output("`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Swamgrythph`% at the Gardens.",$session['user']['name']);
		debuglog("was killed by Swamgrythph to lose $expgain experience and all gold while researching dragon eggs at the Merick's Stables.");
	}elseif ($monster==13){
		//Sheldon Boy
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Sheldon Boy`%.`n");
		output("`b`^He takes all your gold and snickers.`n");
		output("`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Sheldon Boy`% at the Curious Looking Rock.",$session['user']['name']);
		debuglog("was killed by a Sheldon Boy to lose $expgain experience and all gold while researching dragon eggs at the Curious Looking Rock.");
	}elseif ($monster==14){
		//Blupe
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Blupe`%.`n");
		output("`4You lose `#%s experience and all your gold`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Sheldon Boy`% at the Curious Looking Rock.",$session['user']['name']);
		debuglog("was killed by a Blupe to lose $expgain experience and all gold while researching dragon eggs at the Curious Looking Rock.");
	}elseif ($monster==15){
		//Book Gorilla-Man
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Book Gorilla-Man`%.`n");
		output("`b`^He takes all your gold and throws a book at you titled `iLicking the Floor`i.`n");
		output("`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by `\$Book Gorilla-Man`% at the Hall of Fame.",$session['user']['name']);
		debuglog("was killed by Book Gorilla-Man to lose $expgain experience and all gold while researching dragon eggs at the Hall of Fame.");
	}elseif ($monster==16){
		//Dragon Sympathist
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Dragon Sympathist`%.`n");
		output("`b`^He takes all your gold and yells at you `&'You have no right to harm dragons!'`n");
		output("`4You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Dragon Sympathist`% at the Library.",$session['user']['name']);
		debuglog("was killed by a Dragon Sympathist to lose $expgain experience and all gold while researching dragon eggs at the Library.");
	}elseif ($monster==17){
		//Crazed Inmate
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Crazed Inmate`%.`n");
		output("`^He takes all your gold and escapes from the jail`n");
		output("`4You `blose `#%s experience`4`b.`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Crazed Inmate`% at the Jail.",$session['user']['name']);
		debuglog("was killed by a Crazed Inmate to lose $expgain experience and all gold while researching dragon eggs at the Jail.");
	}elseif ($monster==18){
		//Robber
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`%You are killed by the `\$Robber`%.`n");
		output("`^He takes all your gold and runs out of the store.`n");
		output("`4You `blose `#%s experience`4`b.`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Robber`% at MightyE's Weapons.",$session['user']['name']);
		debuglog("was killed by a Robber to lose $expgain experience and all gold while researching dragon eggs at MightyE's Weapons.");
	}elseif ($monster==19){
		//Bear
		$exploss = round($session['user']['experience']*.1);
		$session['user']['gold']=0;
		output("`n`n`b`%You can't `Q'bear'`% to think how you got killed...`b`n");
		output("`b`%You feel like he `%'stuffed'`% you!`b`n");
		output("`b`^All gold on hand has been lost!`b`n");
		output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
		addnews("%s `%was defeated by a `\$Bear`% that was pretending to be stuff.  Ha! Now %s`% is stuffed!",$session['user']['name'],$session['user']['name']);
		debuglog("was killed by a Bear to lose $expgain experience and all gold while researching dragon eggs at the Gift Shop.");
	}elseif ($monster==20 || $monster==21){
		//Newsboy
		$exploss = 0;
		output("`n`n`b`%You can't believe the pesky little brat killed you!`b`n`n`@");
		if ($session['user']['gold']>1){
			$session['user']['gold']-=2;
			output("'I'm taking my `^2 gold`@,'`2 he says as he takes the money form you.");
		}else{
			output("'Sheesh you weren't kidding.  You don't even have `^2 gold pieces`@,'`2 he says to you.");
		}
		output("`n`n`%Luckily, you don't lose any experience. Unluckily, you're dead.");
		addnews("%s `%was killed by a `\$Pesky Newsboy`% over `^2 gold pieces`%.  How sad.",$session['user']['name']);
		debuglog("was killed by a Newsboy for 2 gold while researching dragon eggs at the News.");
	}
	addnav("Continue","shades.php");
	$session['user']['experience']-=$exploss;
	$badguy=array();
	$session['user']['badguy']="";  
	set_module_pref("monster",0);
}
?>