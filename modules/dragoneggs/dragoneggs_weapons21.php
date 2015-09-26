<?php
function dragoneggs_weapons21(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("MightyE's Weapons");
	output("`c`b`&MightyE's Weapons`b`c`7");
	$guess = httppost('guess');
	if ($guess==""){
		if (get_module_setting("marbles")==0) set_module_setting("marbles",e_rand(100,200));
		if (get_module_pref("marblecheck")==0){
			output("You notice that this is a new jar of marbles that you've never seen before.`n`n");
			set_module_pref("marblecheck",1);
		}else output("You're becoming familiar with this jar of marbles... maybe this time you'll guess the right number!`n`n");
		if ($op2==""){
			increment_module_setting("jar",1);
			$session['user']['gold']--;
			output("You hand over your gold and stare at the jar.  You know there's somewhere around 100 to 200 marbles in there... What's your guess?");
		}elseif($op2=="refund") output("You ask for a refund and `!MightyE`7 tells you that there are no refunds. `#'Just make a guess already!'`7 he says.");
		else output("You try picking up the jar to see if you can figure out the number of marbles based on weight but you're no closer.  You just know there's somewhere around 100 to 200 marbles.");
		rawoutput("<form action='runmodule.php?module=dragoneggs&op=weapons21&op2=try' method='POST'>");
		addnav("","runmodule.php?module=dragoneggs&op=weapons21&op2=try");
		rawoutput("<input name='guess' id='guess'><input type='submit' class='button' value='Guess'></form>");
		addnav("Refund","runmodule.php?module=dragoneggs&op=weapons21&op2=refund");
	}else{
		if ($guess>0){
			if ($guess==get_module_setting("marbles")){
				$reward=get_module_setting("jar")+249;
				output("`#'Correct!'`7 exclaims `!MightyE`7, `#'You win `^%s gold`#.'",$reward);
				output("`n`n`!MightyE`7 holds an over-the-top ceremony presenting you with the money.  You feel a little special as the Daily News shows up to report your success.`n`nYou see `!MightyE`7 pull out a new jar full of marbles and place it on the counter.");
				$session['user']['gold']+=$reward;
				set_module_setting("marbles",0);
				set_module_setting("jar",0);
       			$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='marblecheck' and modulename='dragoneggs'";
       			db_query($sql);
				addnews("%s `7 knows how many marbles are in a jar.  Isn't that impressive???",$session['user']['name']);
				debuglog("spent a gold to successfully win $reward gold from the marble jar while researching dragon eggs at MightyE's Weapons.");
			}else{
				output("`#'Hmm... no, that's not right,'`7 says `!MightyE`7.");
				if ($guess>get_module_setting("marbles")) $amount=translate_inline("high");
				else $amount=translate_inline("low");
				debuglog("spent a gold guessing that there were $guess marbles by researching at the MightyE's Weapons.");
				output("`#'In fact, you're a little `^%s`#.  However, I'm going to keep this jar on the table until someone guesses correctly so you can try again next time.'",$amount);
			}
		}else{
			output("`#'Umm... that's not really a guess.  Perhaps you'd like to try again?'`7 says `!MightyE`7.");
			blocknav("weapons.php");
			blocknav("village.php");
			addnav("Guess Again","runmodule.php?module=dragoneggs&op=weapons21&op2=try");
		}
		addnav("Return to MightyE's Weapons","weapons.php");
		villagenav();
	}
}
?>