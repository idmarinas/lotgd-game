<?php
function dragoneggs_rock5(){
	global $session;
	page_header("The Veteran's Club");
	output("`c`b`2The Veteran's Club`b`c`4`n`n");
	$chance=0;
	if (e_rand(1,2)==1) $chance++;
	$rand=e_rand(1,9);
	$level=$session['user']['level'];
	if (($level>10 && $rand<=2) || ($level<=10 && $rand<=1)) $chance+=2;
	elseif (($level>10 && $rand<=5) || ($level<=10 && $rand<=4)) $chance++;
	$previous= strpos($session['user']['weapon'],"Racing Striped ")!==false ? 1 : 0;
	//output("`n`nChance=%s`n`n",$chance);
	if ($chance==3){
		output("You find an amazing box full of jewelry!! It's worth `^1000 gold`4!!!");
		$session['user']['gold']+=1000;
		debuglog("gained 1000 gold while researching dragon eggs at the Curious Looking Rock.");
	}elseif ($chance==2){
		output("You find a magic crystal.  As soon as you touch it, you feel your strength grow.  You `&gain an attack`4 and a `\$permanent hitpoint`4!");
		$session['user']['attack']++;
		$session['user']['maxhitpoints']++;
		$session['user']['hitpoints']++;
		debuglog("gained 1 attack and 1 permanent hitpoint while researching dragon eggs at the Curious Looking Rock.");
	}elseif ($chance==1 && $previous==0){
		output("It's `^Racing Stripes`4 for your %s`4! This will make your weapon faster!",$session['user']['weapon']);
		$session['user']['weapon']="Racing Striped ".$session['user']['weapon'];
		$session['user']['weapondmg']++;
		addnews("`^Go check out %s's`^ new weapon... its got `i`&Racing Stripes`i`^!!");
		debuglog("gained racing stripes on their weapon to add 1 dmg while researching dragon eggs at the Curious Looking Rock.");
	}elseif ($chance==1){
		output("It's empty.");
	}else{
		output("You carefully pry open the box and find...");
		output("`n`nOh the horror! It's a rotting human foot!");
		output("`n`nYou start wretching; the odor overwhelms you.  You stumble back and fall.");
		if ($session['user']['maxhitpoints']>$session['user']['level']*12){
			output("`n`nIt's a nasty fall and you `\$lose all your hitpoints except one`4 but even worse you `\$lose a permanent hitpoint`4.");
			$session['user']['maxhitpoints']--;
			$session['user']['hitpoints']=1;
			debuglog("lost all hitpoints except one and a permanent hitpoint while researching dragon eggs at the Curious Looking Rock.");
		}else{
			output("As the shattering sound of your skull cracking reverberates in your ears, you lose consciousness.");
			output("`n`nAs you lay unconscious, thieves come by and steal all your gold. You're lucky to recover with `\$1 hitpoint`4.");
			if ($session['user']['turns']>0){
				output("Also, it still takes a turn for you to recover.");
				$debug="lost a turn, ";
				$session['user']['turns']--;
			}
			$session['user']['gold']=0;
			$session['user']['hitpoints']=1;
			$debug.="lost all gold and lost all hitpoints except 1 while researching dragon eggs at the Curious Looking Rock.";
			debuglog($debug);
		}
	}
	addnav("Return to the Curious Looking Rock","rock.php");
	villagenav();
}
?>