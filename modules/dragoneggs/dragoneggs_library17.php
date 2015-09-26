<?php
function dragoneggs_library17(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	if (is_module_active("library")) $library="library";
	else{
		$library="dlibrary";
		output("`c`b`^Library`b`c`2`n");
	}
	page_header(array("%s Public Library",get_module_setting("libraryloc",$library)));
	$level=$session['user']['level'];
	$chance=e_rand(1,$op2);
	$array=translate_inline(array("","","","Become More Charming","Money Making Schemes","Gemology","","","","Destroying Dragon Eggs"));
	if (($level>8 && $chance<=2) || ($level<=8 && $chance<=1)){
		output("She leads you to the stacks to find `i%s`i.",$array[$op2]);
		output("`n`n`5'Ah, here we go.  Enjoy the book,'`2 she says.");
		output("`n`nYou read it cover to cover.`n`n");
		if ($op2==3){
			output("You `&gain 2 charm`2.");
			$session['user']['charm']+=2;
			debuglog("gained 2 charm while researching dragon eggs at the Library.");
		}elseif ($op2==4){
			$gold=e_rand(250,400);
			output("You come up with a money making scheme that gains you %s gold!",$gold);
			$session['user']['gold']+=$gold;
			debuglog("gained $gold gold while researching dragon eggs at the Library.");
		}elseif ($op2==5){
			output("You `%gain a gem`2.");
			$session['user']['gems']++;
			debuglog("gained a gem while researching dragon eggs at the Library.");
		}else{
			output("You find a trick to `&Destroying Dragon Eggs`2.  You use your newfound ability to `&destroy a dragon egg`2!");
			addnews("%s `@destroyed a dragon egg in the Library!",$session['user']['name']);
			debuglog("gained a dragon egg point while researching dragon eggs at the Library.");
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
		}
	}else{
		output("`5'We don't have any books on that,'`2 the librarian says as she dismisses you.");
	}
	addnav("Return to the Library","runmodule.php?module=$library&op=enter");
	villagenav();
}
?>