<?php
function dragoneggs_rock25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Veteran's Club");
	output("`c`b`2The Veteran's Club`b`c`4`n`n");
	if ($op2==""){
		output("The stranger walks over to you and looks you up and down.");
		output("`Q'I'm recruiting into my gang. Are you interested?'`4 he asks.`n`n");
		output("It looks like you've met `b`QRazor Sheldon`b`4, the leader of the Sheldon Gang.");
		output("`n`n`Q'Unfortunately, joining my gang isn't like getting invited to a ball.  You have to show me that you've got what it takes,'`4 he says.");
		output("`QRazor`4 tells you that you're going to have to cough up `&2 Dragon Egg Tokens`4 or `%6 gems`4 to join.`n`n");
		if ($session['user']['gems']>=6 || get_module_pref("dragoneggs","dragoneggpoints")>=2){
			addnav("Join the Gang");
			if ($session['user']['gems']>=6) addnav("Give 6 gems","runmodule.php?module=dragoneggs&op=rock25&op2=1");
			if (get_module_pref("dragoneggs","dragoneggpoints")>=2) addnav("Give 2 Dragon Egg Tokens","runmodule.php?module=dragoneggs&op=rock25&op2=2");
			addnav("Leave");
		}else{
			output("Explaining that, despite how much you'd love to join their fine establishment, you just don't have what their looking for.");
			output("`n`nAfraid that this will anger them, you decide to make a hasty retreat.");
		}
	}else{
		if ($op2==2){
			increment_module_pref("dragoneggs",-2,"dragoneggpoints");
			$item="`&2 Dragon Egg Points";
			debuglog("gave up 2 dragon egg points to become a member of the Sheldon Gang while researching dragon eggs at the Curious Looking Rock.");
		}else{
			$session['user']['gems']-=6;
			$item="`%6 gems";
			debuglog("gave up 6 gems to become a member of the Sheldon Gang while researching dragon eggs at the Curious Looking Rock.");
		}
		set_module_pref("member",-1,"sheldon");
		output("You hand over the %s`4 and tell `QRazor Sheldon`4 that you're ready to join.",$item);
		output("`n`nHe smiles, takes your stuff, and gives you a ratty piece of paper.  Before you get a chance to look at it, he tells you last thing.");
		output("`n`n`Q'You better show up before the day is over.  This offer does not last more than a day.'`4");
		output("`n`nThe slip of paper explains that you'll have to find the `QSheldon Gang Hideout`4 in %s... which will now be visible to you.",get_module_setting("sheldonloc","sheldon"));
	}
	addnav("Return to the Curious Looking Rock","rock.php");
	villagenav();
}
?>