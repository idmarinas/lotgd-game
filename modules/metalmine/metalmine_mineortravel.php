<?php
function metalmine_mineortravel(){
	global $session;
	$op = httpget('op');
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	$helmet=$allprefs['helmet'];
	$usedmts=$allprefs['usedmts'];
	$mineturnset=get_module_setting("mineturnset");
	if  ($usedmts>=$mineturnset){
		output("You prepare to enter the mine but a Supervisor stops you.");
		output("`n`n`@'I'm sorry, but you've been in there long enough today.  We have a strict policy to not let anyone back into the mine once they've spent `^%s Mine Turns`@ in the mine. You could end up getting people killed.'",$mineturnset);
		output("`n`n`0You apologize for being so thoughtless.");
		addnav("Leave","runmodule.php?module=metalmine&op=enter");
	}else{
		$chance=e_rand(1,6);
		if ($helmet==0 || $allprefs['oil']>=1000){
			if ($usedmts<$mineturnset){
				if ($helmet==0) output("Not having much of a helmet,");
				else output("Not having any oil in your helmet,");
				output("you use up all your remaining turns stumbling around and not getting anywhere. You look up and find");
				if ($op=="mine") output("that you're back at the entrance.");
				else output("that you've stumbled back to the entrance.");
				addnav("Leave","runmodule.php?module=metalmine&op=enter");
			}else{
				output("You stumble around using all of your Mine Turns getting to a");
				if ($op=="travel") output("new");
				output("location to dig.  If only you had a helmet, you could have made it there without so much trouble.");
				addnav("Continue","runmodule.php?module=metalmine&op=mining");
			}
			$allprefs['usedmts']=$mineturnset;
		}else{
			if($helmet==1){
				if ($chance==1) $move=1;
				elseif ($chance==2 ||$chance==3) $move=2;
				else $move=3;
			}elseif ($helmet==2){
				if ($chance==1 ||$chance==2) $move=1;
				elseif ($chance==3 ||$chance==4) $move=2;
				else $move=3;
			}else{
				if ($chance==1) $move=3;
				elseif ($chance==2 ||$chance==3) $move=2;
				else $move=1;
			}
			if ($usedmts>($mineturnset+$move)){
				if ($op=="mine") output("You try to head down into the mine but you can't find your way. You spend the rest of your mine turns wandering around but have to leave.");
				else output("You try to travel to a new section of the mine but lose your way.  You end up spending the rest of your mine turns getting to the exit of the mine.");
				$allprefs['usedmts']=$allprefs['usedmts']+$mineturnsset;
				addnav("Leave","runmodule.php?module=metalmine&op=enter");
			}else{
				output("You spend `^%s mine %s `0traveling",$move,translate_inline($move>1?"turns":"turn"));
				if ($op=="mine") output("down to the mine.");
				else output("to a new part of the mine.");
				$allprefs['usedmts']=$allprefs['usedmts']+$move;
				addnav("Continue","runmodule.php?module=metalmine&op=mining");
			}
		}
		set_module_pref('allprefs',serialize($allprefs));
	}
}
?>