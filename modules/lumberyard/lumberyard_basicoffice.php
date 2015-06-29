<?php
function lumberyard_basicoffice(){
	global $session;
	$fullsize=get_module_setting("fullsize");
	$remainsize=get_module_setting("remainsize");
	$lumberturns=get_module_setting("lumberturns");
	$plantneed=get_module_setting("plantneed");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$usedlts=$allprefs['usedlts'];
	$squares=$allprefs['squares'];
	$phase=$allprefs['phase'];
	$remaining=$plantneed-$remainsize;
	
	$squarepay=get_module_setting("squarepay");
	if (get_module_setting("leveladj")==1) $squarepay=round($squarepay*$session['user']['level'] / 15);
	
	require_once("modules/lumberyard/lumberyard_navs.php");
	lumberyard_navs();
	blocknav("runmodule.php?module=lumberyard&op=office");
	output("`n`b`c`@F`7oreman's `@O`7ffice`b`c`n");
	output("`^You climb the stairs up to the second floor of the mill to enter a plain office that seems to be covered by a thin layer of saw dust.");
	output("`n`n On the wall is a calender featuring the newest mounts available in the kingdom highlighted by a fair maiden showing a little bit of ankle.");
	output("The foreman sits down behind the desk and motions for you to sit down.");
	output("`n`n He pulls out the ledger and turns to your page.`n`n");
	output("`#'Let's see, under`^ %s`#... I have you listed for the following:'`n`n",$session['user']['name']);
	output("`#'You've spent`@ %s out of %s turns`# working in the forest.`n`n",$usedlts,$lumberturns);
	output("`#You are on `Q`bPhase %s`b`#.`n`n",$phase);
	if ($squares==0) $squares=translate_inline("zero");
	output("`#You now have`b`& %s `bSquare%s of Wood`#.`n`n",$squares,translate_inline($squares<>1?"s":""));
	output("`#There are`6 %s trees `#remaining, and the forest is full when it has`6 %s trees`#.`n`n",$remainsize,$fullsize);
	if ($squares>=1){
		$levelreq=get_module_setting("levelreq");
		if (($levelreq>1 && $session['user']['level']>=$levelreq) || $levelreq==1){
			$maximumsell=get_module_setting("maximumsell");
			if (($maximumsell>0 && $allprefs['squaresold']<$maximumsell) || $maximumsell==0){
				output("`#Currently, the best I can offer you for a square of wood is`^ %s gold`#.",$squarepay);
				if ($maximumsell>0){
					$left=$maximumsell-$allprefs['squaresold'];
					output("Remember, you can sell up to `^%s `&%s`# per day, and",$maximumsell,translate_inline($maximumsell>1?"squares":"square"));
					if ($allprefs['squaresold']==0) output("you haven't sold any today yet.");
					else output("you've already sold `^%s `&%s`# today; meaning you can only sell `^%s`# more today.",$allprefs['squaresold'],translate_inline($allprefs['squaresold']>1?"squares":"square"),$left);
					if ($left>$squares) $left=$squares;
					addnav(array("Sell %s %s %s",translate_inline($left>1?"All":""),$left,translate_inline($left>1?"Squares":"Square")),"runmodule.php?module=lumberyard&op=squaresell&op2=$left");
				}else addnav(array("Sell %s %s %s",translate_inline($squares>1?"All":""),$squares,translate_inline($squares>1?"Squares":"Square")),"runmodule.php?module=lumberyard&op=squaresell&op2=$squares");
				output("`n`nHow many would you like to sell?'");
				output("<form action='runmodule.php?module=lumberyard&op=squaresell' method='POST'><input name='sell' id='sell'><input type='submit' class='button' value='sell'></form>",true);
				addnav("","runmodule.php?module=lumberyard&op=squaresell");
			}else{
				output("`#Unfortunately, you've sold your maximum `^%s `&Squares of Wood`# today already.  Please come back tomorrow.'",$maximumsell);
			}
		}else{
			output("`#Unfortunately, you need to be at least level `^%s`# to sell any `&wood`#.  Feel free to come back when you've advanced.'",$levelreq);
		}
	}else{
		output("`#Since you don't have any squares to sell, you'll probably want to get to work in the yard.'");
		output("`n`n `^ The foreman	closes the ledger and leads you to the door.`n`n");
	}
}
?>