<?php
function orchard_audresults(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['caplay']=1;
	$allprefs['canumb']=$allprefs['canumb']+1;
	$canumb=$allprefs['canumb'];
	$session['user']['gold']-=1000;
	switch(e_rand($canumb,10)){
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
			output("`5This time you're ready for that Crazy Audrey. \"`#No throwing %s!`5\" you tell her.",get_module_setting("animals","crazyaudrey"));
			output("`n`nAnd you start staring... and staring...`n`n");
			switch(e_rand(1,5)){
				case 1:
					output("Then you notice the big hairy mole on her cheek! You can't take it anymore and you turn away.");
				break;
				case 2:
					output("You feel Crazy Audrey kick you under the table. You look down and lose the staring contest!");
				break;
				case 3:
					output("You suddenly smell a very obnoxious odor.  Suddenly, Crazy Audrey starts smiling very broadly as you realize she just passed gas.");
					output("The smell overwhelms you and you cover your nose and turn away.");
				break;
				case 4:
					output("Crazy Audrey starts drooling. A huge gob is about to hit the ground when she sucks it back up.");
					output("She really is crazy.  You can't stand watching her and lose the staring contest!");
				break;
				case 5:
					output("You notice that Crazy Audrey is picking at a scab on her arm.  She slowly peals it off and eats it.");
					output("If, at this point, you are not honestly sick to your stomach, you should be.  You can't watch anymore and turn away.");
				break;
			}
			output("`n`n\"`%Thanks for the gold.  You can try and beat me again tomorrow.`5\"");
		break;
		case 10:
		case 11:
			output("`5You take your position and begin the contest.  She won't be  able to distract you this time!");
			output("`n`nShe picks her nose, scratches her butt, puts her finger in her ear, and even pulls out her hair trying to distract you.");
			output("`n`nHowever, you keep your stare.  It seems like you're going to win and you start to smile.`n`n");
			output("\"`%You have such hideous teeth! I can't stand it anymore!`5\" cries Crazy Audrey.");
			output("`n`n`bYou've beaten Crazy Audrey!!!`b");
			require_once("modules/orchard/orchard_func.php");
			orchard_findseed();
			$allprefs=unserialize(get_module_pref('allprefs'));
			$allprefs['caspiel']=0;
			$allprefs['caplay']=0;
			$allprefs['canumb']=0;
		break;
	}
	set_module_pref('allprefs',serialize($allprefs));
}
?>