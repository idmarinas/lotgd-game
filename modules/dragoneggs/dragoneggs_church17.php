<?php
function dragoneggs_church17(){
	global $session;
	page_header("Old Church");
	switch(e_rand(1,10)){
		case 1: case 2: case 3: case 4:
			output("You don't get anything and you frustratedly leave; `%one gem less`3 and a bit the wiser not to trust those funny monks.");
			$session['user']['gems']--;
			debuglog("lost a gem while researching dragon eggs at the Church.");
		break;
		case 5: case 6: case 7:
			output("You give them a `%gem`3 and then they give you a `%gem`3. So no gain, no loss.");
		break;
		case 8: case 9:
			output("Oooh! The things you learn! You lose a `%gem`3, but gain `%2 gems`3 in return.  So it's like you `%gain 1 net gem`3!");
			$session['user']['gems']++;
			debuglog("gained a gem while researching dragon eggs at the Church.");
		break;
		case 10:
			output("They take you to a deep vault and they show you their `%gem`3 collection and they let you take some of them.  You lose a gem, but gain `%3 gems`3 in return.  It's `%2 net gems gained`3!");
			$session['user']['gems']+=2;
			debuglog("gained 2 gems while researching dragon eggs at the Church.");
		break;
	}
	addnav("Return to the Church","runmodule.php?module=oldchurch");
	villagenav();
}
?>