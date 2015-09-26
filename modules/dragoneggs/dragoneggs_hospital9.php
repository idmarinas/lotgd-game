<?php
function dragoneggs_hospital9(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Healer's Hut");
	output("`c`b`#Healer's Hut`b`c`n");
	if ($op2=="no"){
		output("`3You decide it's not worth the gold and head back to the forest.");
	}else{
		output("`3You hand over the money and sit patiently to receive the injection...`n`n");
		$chance=e_rand(1,7);
		$session['user']['gold']-=$op2;
		if ($chance==1){
			output("It works! You gain one permanent hitpoint and feel refreshed.");
			$session['user']['maxhitpoints']+=1;
			$session['user']['hitpoints']=$session['user']['maxhitpoints'];
			debuglog("gained a permanent hitpoint and full hitpoints by researching at Healer's Hut.");
		}elseif ($chance<4){
			output("It works... a little... You feel refreshed with some extra hitpoints.");
			$session['user']['hitpoints']=$session['user']['maxhitpoints']+10;
			debuglog("gained a full hitpoints + 10 by researching at Healer's Hut.");
		}elseif ($chance==4){
			output("It works in a painful way.  You gain a permanent hitpoint but you feel very weak.");
			$session['user']['maxhitpoints']+=1;
			$session['user']['hitpoints']=1;
			debuglog("gained a permanent hitpoint and lost all hitpoints except 1 by researching at Healer's Hut.");
		}else{
			output("Nothing. Nothing happens.  What a waste of money!");
		}
	}
	require_once("lib/forest.php");
	forest(true);
}
?>