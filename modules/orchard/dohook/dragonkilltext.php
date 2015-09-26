<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	$seed=$allprefs['seed'];
	if (get_module_setting("dryenable")==3){
		$allprefs['dietreehit']=0;
		set_module_pref('allprefs',serialize($allprefs));
	}
	if ($seed==10){
		output("You notice a small Cherry seed in the grass beside you, for some reason you decide to pick it up, perhaps it will be useful somewhere...");
		require_once("modules/orchard/orchard_func.php");
		orchard_findseed();
	}
	if ($seed==20){
		$dsage=$allprefs['dragonseedage'];
		$allprefs['dragonseedage']=$allprefs['dragonseedage']+1;
		set_module_pref('allprefs',serialize($allprefs));
		if ($dsage==0){
			output("`n`n`#You have a dim memory of doing something before you woke up.  You feel in your pockets and feel that they're empty; remembering something about a dragon seed.");
			output("You recollect hiding the seed amongst some dragon eggs.  Soon enough, you forget about the whole incident.");
		}elseif ($dsage==1 || $dsage==2) output("`n`n`#You have a dim memory of seeing a Dragon Seed sitting comfortably in a patch of dragon eggs. You try to remember to ask `!Elendir`# at the orchard what to do next.");			
		elseif ($dsage==3){
			output("`n`n`#You feel in your pocket and smile, feeling the warmth of a happy Dragon Seed in your pocket.");
			$allprefs['dragonseedage']=0;
			set_module_pref('allprefs',serialize($allprefs));
			require_once("modules/orchard/orchard_func.php");
			orchard_findseed();
		}
	}
?>