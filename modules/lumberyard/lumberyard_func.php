<?php
function lumberyard_planttrees(){
	global $session;
	output("`c`n`&`b Planting `@Trees`b`n`n`c");
	output("`^You go to the nursery and grab a tree to plant.");
	output("This is some of the most difficult work you've ever done in your life.`n`n");
	addnav("`@Back to the Forest","forest.php");
	addnav("Show me my Ledger","runmodule.php?module=lumberyard&op=office");
	if ($session['user']['turns']>1) addnav("Plant Some More Trees","runmodule.php?module=lumberyard&op=planttree");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['usedlts']=$allprefs['usedlts']+2;
	$allprefs['planthof']++;
	$session['user']['turns']-=2;
	$plantneed=get_module_setting("plantneed");
	increment_module_setting("remainsize",1);
	$remainsize=get_module_setting("remainsize");
	switch(e_rand(1,5)){
		case 1:
			switch(e_rand(1,10)){
				case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9:
					output("`^You do so well at planting trees that the foreman digs deep into his pocket and hands you `%a gem`^!`n`n");
					output("`#'Thanks to your work, the yard now has `6 %s trees`# planted.'`n`n",$remainsize);
					$session['user']['gems']++;
					debuglog("gained a gem planting 2 trees in the lumberyard.");
				break;
				case 10:
					increment_module_setting("remainsize",-1);
					$exploss = round($session['user']['experience']*.05);
					output("`^Well, this is just really bad luck.");
					output("The madman`b`$ %s `b`^sees you trying to plant trees.",get_module_setting("clearcutter"));
					output("This sends`b`$ %s `b`^ into a berserker's rage.",get_module_setting("clearcutter"));
					output("You try to defend yourself, but honestly, it's a slaughter.");
					output("You're`$ `bMOSTLY dead`b`^.`n`n");
					output("`b`^ You lose `#%s experience`^.`b`n`n",$exploss);
					output("`b`^ You are done working in the `QL`qumber `QY`qard for today`^`b.`n");
					output("`c`n`@You are `\$MOSTLY dead`@... Which means you're still really alive.`b`c");
					$allprefs['usedlts']=get_module_setting("lumberturns");
					addnews("%s `@was rendered `\$MOSTLY dead `@by `\$%s`@ in the `QL`qumber `QY`qard`@.  `\$NO TREES!",$session['user']['name'],get_module_setting("clearcutter"));
					blocknav("runmodule.php?module=lumberyard&op=office");
					blocknav("runmodule.php?module=lumberyard&op=planttree");
					blocknav("runmodule.php?module=lumberyard&op=work");
					$session['user']['experience']-=$exploss;
					$session['user']['hitpoints']=1;
					debuglog("became mostly dead trying to plant trees in the lumberyard and lost $exploss and left with 1 hitpoint.");
				break;
			}
		break;
		case 2:
			output("`^Just what the doctor ordered!");
			output("You plant some trees and watch the lumber yard start to take back it's original glory.");
			output("Nothing will stop the trees!`n`n");
			output("You feel stronger!");
			output("You've gained a`7 temporary 25 hitpoint`^ boost!`n`n");
			output("`#'Thanks to your work, the yard now has `6 %s trees`# planted.'`n`n",$remainsize);
			$session['user']['hitpoints']+= 25;
			debuglog("planted a tree and gained 25 hitpoints in the lumberyard.");
		break;
		case 3:
			output("`^Being outdoors planting trees is invigorating.");
			output("This was a great idea!");
			output("You feel supercharged!`n`n");
			output("`^You `@gain 3 forest fights`^!`n`n");
			output("`#'Thanks to your work, the yard now has `6 %s trees`# planted.'`n`n",$remainsize);
			$session['user']['turns']+=5;
			debuglog("planted a tree and gained 5 turns planting a tree in the lumberyard.");
		break;
		case 4:
			output("`^With great gusto you plant the trees.");
			output("You find that you have a knack for planting.");
			output("It makes you a better person.");
			output("You are`& more charming`^.");
			output("You also find `b200 gold`b.");
			output("The foreman smiles as you finish planting.`n`n");
			output("`#'Thanks to your work, the yard now has `6 %s trees`# planted.'`n`n",$remainsize);
			$session['user']['gold']+=200;
			$session['user']['charm']++;
			debuglog("planted a tree and gained 200 gold and 1 charm in the lumberyard.");
		break;
		case 5:
			output("`^The foreman watches you work and notices how efficient you are.");
			output("He brings you a cup of `blemonade`b and you feel good about your contribution.`n`n");
			apply_buff('lemonrush',array(
				"name"=>"Lemonade Rush",
				"rounds"=>20,
				"wearoff"=>"You run out of lemon power.",
				"defmod"=>1.15,
			));
			output("`#'Thanks to your work, the yard now has `6 %s trees`# planted.'`n`n",$remainsize);
			debuglog("planted a tree and gained the Lemonade Rush Buff in the lumberyard.");
		break;
	}
	set_module_pref('allprefs',serialize($allprefs));
}
function lumberyard_sellsquare(){
	global $session;
	$op2 = httpget('op2');
	output("`n`b`c`@F`7oreman's `@O`7ffice`b`c`n`n");
	$squarepay=get_module_setting("squarepay");
	if (get_module_setting("leveladj")==1) $squarepay= round($squarepay*$session['user']['level'] / 15);
	$allprefs=unserialize(get_module_pref('allprefs'));
	$sell = httppost('sell');
	if ($op2>0) $sell=$op2;
	if (get_module_setting("maximumsell")>0){
		$max=get_module_setting("maximumsell")-$allprefs['woodsold'];
		if ($max>$allprefs['squares']) $max=$allprefs['squares'];
	}else $max = $allprefs['squares'];
	if ($sell < 0) $sell = 0;
	if ($sell >= $max) $sell = $max;
	if ($max < $sell) {
		output("`^Foreman N. Hanson looks at you bewildered. `#'You know you don't have that many squares!'`n`n"); 
	}else{
		$cost=$sell * $squarepay;
		$session['user']['gold']+=$cost;
		$allprefs['squares']=$allprefs['squares']-$sell;
		$allprefs['squaresold']=$allprefs['squaresold']+$sell;
		debuglog("Sold $sell squares for $squarepay to collect a total of $cost gold.");
		set_module_pref('allprefs',serialize($allprefs));
		increment_module_setting("woodsold",$sell);
		output("`^Foreman N. Hanson gives you `b%s gold`b in return for`& %s %s`^.",$cost,$sell,translate_inline($sell>1?"squares":"square"));
	}
}
?>