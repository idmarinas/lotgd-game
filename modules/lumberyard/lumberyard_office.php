<?php
function lumberyard_office(){
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
	
	addnav("`@To the Forest","forest.php");
	if ($session['user']['turns']>1) addnav("Plant Some Trees","runmodule.php?module=lumberyard&op=clearyes");
	output("`n`b`c`@F`7oreman's `@O`7ffice`b`c`n`n");
	output("`#'You've spent`@ %s out of %s turns`# working  in the forest.`n`n",$usedlts,$lumberturns);
	output("`#You are on `Q`bPhase %s`b`#.`n`n", $phase);
	if ($squares==0) $squares=translate_inline("zero");
	output("`#You now have`b`& %s `bSquares of Wood`#.`n`n", $squares,translate_inline($squares<>1?"s":""));
	output("I think the yard should be ready once we've got `6 %s more trees`# planted.`n`n",$remaining);
	output("`#You could consider giving `@2 turns `#to plant more trees.`n`n");
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
					addnav(array("Sell %s %s %s",translate_inline($left>1?"All":""),$left,translate_inline($left>1?"Squares":"Square")),"runmodule.php?module=lumberyard&op=clearcutsell&op2=$left");
				}else addnav(array("Sell %s %s %s",translate_inline($squares>1?"All":""),$squares,translate_inline($squares>1?"Squares":"Square")),"runmodule.php?module=lumberyard&op=clearcutsell&op2=$squares");
				output("`n`n How many would you like to sell?'");
				output("<form action='runmodule.php?module=lumberyard&op=clearcutsell' method='POST'><input name='sell' id='sell'><input type='submit' class='button' value='sell'></form>",true);
				addnav("","runmodule.php?module=lumberyard&op=clearcutsell");
			}else{
				output("`#Unfortunately, you've sold your maximum `^%s `&Squares of Wood`# today already.  Please come back tomorrow.'",$maximumsell);
			}
		}else{
			output("`#Unfortunately, you need to be at least level `^%s`# to sell any `&wood`#.  Feel free to come back when you've advanced.'",$levelreq);
		}
	}else{
		output("`#You don't have any squares to sell.`n`n");
		output("`^The foreman closes the ledger and leads you to the door.`n`n");
	}
}
?>