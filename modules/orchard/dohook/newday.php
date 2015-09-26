<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	$tree=$allprefs['tree'];
	$names=translate_inline(array("","`\$Apple","`QOrange","`6Pear","`QApricot","`^Banana","`QPeach","`5Plum","`qFig","`^Mango","`\$Cherry","`QTangerine","`^Grapefruit","`^Lemon","`2Avocado","`@Lime","`\$Pomegranate","`qKiwi","`4Cranberry","`^Star Fruit","`@Dragon`\$fruit"));
	if ($allprefs['dieingtree']>0){
		output("`n`n`%You receive a very sad notice from `!Elendil`% informing you that your `b%s tree`b has died from Fruit Tree Disease.`n",$names[$tree]);
		$allprefs['bankkey']=0;
		$allprefs['mespiel']=0;
		$allprefs['menumb']=0;
		$allprefs['caspiel']=0;
		$allprefs['canumb']=0;
		$allprefs['bellyrub']=0;
		$allprefs['pegplay']=0;
		$allprefs['dragonseedage']=0;
		$allprefs['monsterid']="";
		$allprefs['monsterlevel']="";
		$allprefs['monstername']="";
		$allprefs['dietreehit']=0;
		$allprefs['dieingtree']=0;
		if ($allprefs['seed']>0) $allprefs['seed']--;
		if ($allprefs['found']>0) $allprefs['found']--;
		if ($allprefs['tree']>0) $allprefs['tree']--;
	}
	if (get_module_setting("dryenable")==2) $allprefs['dietreehit']=0;
	$allprefs['hadfruittoday']=0;
	$allprefs['meplay']=0;
	$allprefs['caplay']=0;
	$allprefs['pegplay']=0;
	if (get_module_setting("everyday")==0){
		if ($allprefs['treegrowth']>0) output("`n`@You recall that your %s Tree`@ still has `^%s`@ more %s to grow.`n",$names[$tree+1],$allprefs['treegrowth'],translate_inline($allprefs['treegrowth']>1?"days":"day"));
	}else{
		if ($allprefs['treegrowth']>0){
			$allprefs['treegrowth']=$allprefs['treegrowth']-1;
			if ($allprefs['treegrowth']>0) output("`n`@You recall that your %s Tree`@ still has `^%s`@ more %s to grow.`n",$names[$tree+1],$allprefs['treegrowth'],translate_inline($allprefs['treegrowth']>1?"days":"day"));
			else{
				$allprefs['tree']++;
				output("`n`@You recall that your %s Tree`@ should be fully grown by now, perhaps you should visit the orchard and take a look.`n",$names[$tree+1]);
			}
		}
	}
	set_module_pref('allprefs',serialize($allprefs));
?>