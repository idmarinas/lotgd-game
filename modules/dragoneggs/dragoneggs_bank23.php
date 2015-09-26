<?php
function dragoneggs_bank23(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Ye Olde Bank");
	output("`c`b`^Ye Olde Bank`b`c`6");
	if ($op2==1){
		$session['user']['gold']-=100;
		output("You hand over the money to the homeless man and hope he can make something more of himself.");
		$chance=e_rand(1,3);
		if ($chance==1 && $session['user']['weapon']!="`#Vorpal Pike`0"){
			output("He smiles and pulls out a `%Vorpal Pike`6 and hands it to you.  `@'I was hoping someone would be kind enough to help. I want you to use this with my blessing.'");
			output("`n`n`6You notice that this pike is better than your current weapon and you decide to keep it.");
			$session['user']['weapon']="`#Vorpal Pike`0";
			$session['user']['weapondmg']++;
			$session['user']['attack']++;
			debuglog("received a weapon upgrade to a Vorpal Pike with an attack 1 higher by researching at the Bank.");
		}else{
			if (is_module_active("bakery")) $bakery=translate_inline("Hara's Bakery");
			else $bakery=translate_inline("the Bakery");
			output("He thanks you and smiles on his way over to %s for a nice pastry. You `&gain 1 charm`6 for your act of kindness.",$bakery);
			$session['user']['charm']++;
			debuglog("received a charm point researching dragon eggs at the Bank.");
		}
	}else{
		output("Giving is not your way.");
		$chance=e_rand(1,4);
		if ($chance==1){
			output("The patrons of the bank notice your tight-wad ways and call you a scrooge.");
			require_once("lib/names.php");
			$newtitle = translate_inline("Scrooge");
			$newname = change_player_title($newtitle);
			$session['user']['title'] = $newtitle;
			$session['user']['name'] = $newname;
			debuglog("was named Scrooge by researching at the Bank.");
		}
	}
	addnav("Continue Banking","bank.php");
	villagenav();
}
?>