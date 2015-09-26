<?php
function dragoneggs_police13(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header(array("The Jail of %s", $session['user']['location']));
	output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
	if (is_module_active("jail")) $jail="jail";
	else $jail="djail";
	if ($op2=="pay"){
		if ($session['user']['gold']>=500){
			output("You hand over the `^500 gold`2 and the sheriff tells you that your papers are now all in order.");
			$session['user']['gold']-=500;
			debuglog("lost 500 gold while researching dragon eggs at the Jail.");
		}else{
			$goldonhand=$session['user']['gold'];
			$gold=500-$session['user']['gold'];
			output("You give the sheriff `^%s gold`2from your wallet and give him a bank withdrawal slip for `^%s gold`2. The sheriff tells you that your papers are now all in order.",$session['user']['gold'],$gold);
			$session['user']['gold']=0;
			$session['user']['goldinbank']-=$gold;
			debuglog("lost $gold gold from their bank and the $goldonhand gold while researching dragon eggs at the Jail.");
		}
	}elseif ($op2=="talk"){
		$dmg=$session['user']['weapondmg'];
		debuglog("Lost their old weapon ($dmg attack) and received an Old Dagger with 9 attack while researching dragon eggs at the Jail.");
		output("You start to explain that you really do have a permit for your weapon but you left it at home.");
		output("`n`n`@'Nope, I'm not buying what you're selling.'`2 He confiscates your weapon and you start to look VERY sad.  He tosses you an old dagger and smiles at his new %s`2.",$session['user']['weapon']);
		$session['user']['weapon']="`#Old Dagger`0";
		$session['user']['attack']-=$session['user']['weapondmg'];
		$session['user']['attack']+=9;
		$session['user']['weapondmg']=9;
		$session['user']['weaponvalue']=4230;
	}
	addnav("Return to the Jail","runmodule.php?module=$jail");
	villagenav();
}
?>