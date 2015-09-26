<?php
function orchard_findseed(){
	$allprefs=unserialize(get_module_pref('allprefs'));
	$seed= $allprefs['seed'];
	$names=translate_inline(array("","an `\$apple","an `Qorange","a `6pear","an `Qapricot","a `^banana","a `4peach","a `5plum","a `qfig","a `6mango","a `\$cherry","a `Qtangerine","a `^grapefruit","a `^lemon","an `2avocado","a `@lime","a `\$pomegranate","a `qkiwi","a `4cranberry","a `^star fruit","a `@dragon`\$fruit"));
	output("`n`n`#You have found %s `#seed!`n",$names[$seed]);
	output("You should probably take it to the orchard and get it planted.`n`n");
	$allprefs['found']=$seed;
	$allprefs['seed']=0;
	set_module_pref('allprefs',serialize($allprefs));
}

function orchard_monster($lvl){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$monsterid=$allprefs['monsterid'];
	$monsterlevel=$allprefs['monsterlevel'];
	$monstername=$allprefs['monstername'];
	$names=translate_inline(array("","Apple","Orange","Pear","Apricot","Banana","Peach","Plum","Fig","Mango","Cherry","Tangerine","Grapefruit","Lemon","Avocado","Lime","Pomegranate","Kiwi","Cranberry","Star Fruit","Dragonfruit"));
	if ($monsterid>0){
		if ($monsterlevel!=$session['user']['level']){
			orchard_monsterid($session['user']['level']);
			output("`!Elendir`7 looks a little embarrassed, \"`#Urm, yes, well I know what I said before.  But after thinking about it, I'm quite sure it was actually a `4%s`# that stole my `@%s`# seed.`7\"",$monstername,$names[$lvl]);
		}else{
			output("`7\"`#As I said before, I think a `4%s`# stole my `@%s`# seed in the forest.`7\"",$monstername,$names[$lvl]);
		}
	}else{
		orchard_monsterid($session['user']['level']);
		$allprefs=unserialize(get_module_pref('allprefs'));
		$monstername=$allprefs['monstername'];
		output("`7\"`#I'm afraid I lost one of my `@%s`# seeds the other day when I was wondering in the forest, if I recall correctly, a `4%s`# stole it.`7\"",$names[$lvl],$monstername);
	}
}

function orchard_monsterid($lvl){
	// thankyou GenmaC for this little code snippet
	$allprefs=unserialize(get_module_pref('allprefs'));
	$sql = "SELECT * FROM ".db_prefix("creatures")." WHERE creaturelevel='".$lvl."'";
	$result = db_query($sql);
	while($row = db_fetch_assoc($result)) $critters[] = $row; 
	$select = e_rand(0,count($critters)-1);
	$allprefs['monsterid']=$critters[$select]['creatureid'];
	$allprefs['monsterlevel']=$lvl;
	$allprefs['monstername']=$critters[$select]['creaturename'];
	set_module_pref('allprefs',serialize($allprefs));
}
?>