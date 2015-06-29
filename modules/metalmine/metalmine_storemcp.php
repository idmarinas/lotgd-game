<?php
function metalmine_storemcp(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	addnav("General Store");
	output("`n`c`b`&General `)Store`0`c`b`n");
	if ($allprefs['toothy']==2){
		output("You show Grober the map that you found in the mine.  Although he tries not to show any excitement, he asks to 'see' it again.");
		output("`n`nYou see a malicious glint in his eye and decide that you better search for `qToothy`0 on your own.");
		output("`n`n`Q'Now, if you find anything substantial of `qToothy's`Q, you let me know.  I  may have something worth trading with you if you find something interesting.'");
		output("`n`n`0You feel inspired to search for `qToothy`0 more vigorously.");
		$allprefs['toothy']=$allprefs['toothy']+1;
	}elseif ($allprefs['toothy']==4){
		output("You bring `qToothy's Tooth`0 to Grober and tell the tale of your encounter.");
		output("`n`nGrober listens intently to your story with an envious eye on the tooth.");
		output("`Q'Yeah... that's definitely `qToothy's Tooth`Q! Hey, I'll give you a magic potion for that tooth!'`0");
		output("`n`nYou look at the cavity ridden tooth and realize you're probably not going to get a better offer than that.");
		output("You make the exchange and drink down the potion.  You feel strength flow through you! You `&Gain 1 defense`0!");
		output("`n`nHappy with the trade, Grober asks about `qToothy's Pickaxe`0. You mention that `qToothy`0 didn't have one, but that you'll look for it. It sounds like there's more to this adventure still!");
		$session['user']['defense']++;
		$allprefs['toothy']=$allprefs['toothy']+1;
	}elseif ($allprefs['toothy']==6){
		output("You show `qToothy McPicker's Pickaxe`0 to Grober and his eyes widen. You tell your story and Grober listens intently.");
		output("`n`nHe takes you to a back room and shows you his 'Shrine' to `qToothy`0. You see a large collection of junk, with the tooth you gave him sitting on a pedestal.");
		output("Grober tells you he'll give you an even better magic potion for the pickaxe and that his collection will be complete.");
		output("`n`nWhat else are you going to do with a smelly old pickaxe? You make the deal and quaff the potion.");
		output("Oooohh! Now you feel stronger! You've `&gained an attack`0!");
		$session['user']['attack']++;
		$allprefs['toothy']=$allprefs['toothy']+1;		
	}else{
		output("`Q'Yeah, Good ole' `qToothy McPicker`Q.... I loved him like a father.  Well; maybe a grandfather.  Possibly a great grandfather. Now that I think about it, he was probably a little too old to be running around in a mine.'");
	}
	metalmine_storenavs();
	blocknav("runmodule.php?module=metalmine&op=storemcp");
	set_module_pref('allprefs',serialize($allprefs));
}
?>