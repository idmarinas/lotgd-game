<?php
function dragoneggs_church9(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Old Church");
	if ($op2==""){
		$level=$session['user']['level'];
		$chance=e_rand(1,7);
		if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
			output("It's a strange and cryptic book. You page through it for a while and see strange symbols. OOOohh... it's giving you a headache to read through it.");
			output("`n`nWill you keep reading or just give up now?");
			addnav("Focus on the Book","runmodule.php?module=dragoneggs&op=church9&op2=continue");
		}else{
			output("No... there's nothing here but an old hymnal.  You got all excited for nothing.");
		}
	}else{
		output("You persist!");
		if ($session['user']['turns']>2){
			output("Your eyes blur...");
			if (isset($session['bufflist']['blurredvision'])) {
				$session['bufflist']['blurredvision']['rounds'] += 10;
			}else{
				apply_buff('blurredvision',array(
					"name"=>translate_inline("Blurred Vision"),
					"rounds"=>25,
					"wearoff"=>translate_inline("`2Your eyes clear and your focus returns."),
					"defmod"=>.9,
				));
			}
			debuglog("gained a gem and a blurred vision buff while researching dragon eggs at the Church.");
		}else{
			output("Your eyes blur and you realize they aren't going to clear up within the next day...");
			if (isset($session['bufflist']['blurredvision'])) {
				$session['bufflist']['blurredvision']['rounds'] += 10;
			}else{
				apply_buff('blurredvision',array(
					"name"=>translate_inline("Blurred Vision"),
					"rounds"=>25,
					"wearoff"=>translate_inline("`2Your eyes clear and your focus returns."),
					"defmod"=>.9,
					"survivenewday"=>1,
				));
			}
			debuglog("gained a gem and a blurred vision buff that survives the newday while researching dragon eggs at the Church.");
		}
		output("but it pays off! You gain `%a gem`@!");
		$session['user']['gems']++;
	}
	addnav("Return to the Church","runmodule.php?module=oldchurch");
	villagenav();
}
?>