<?php
function metalmine_tunnel(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$locale = httpget('loc');
	$op2 = httpget('op2');
	$misc= array ('25t','dead');
	if (in_array($op2,$misc)){
		metalmine_misc($op2);
	}
	page_header("Secret tunnel");
	if ($session['user']['hitpoints'] <= 0) redirect("shades.php");
	$umaze = get_module_pref('maze');
	$umazeturn = $allprefs['mazeturn'];
	$upqtemp = get_module_pref('pqtemp');
	if ($op2 == "" && $locale == "") {
		$locale=32;
		$allprefs['found']=1;
		output("`c`b`^Secret Chamber`0`b`c");
		output("`2`nYou enter a Secret Chamber and wonder what this room holds in store for you.`n");
		$umazeturn = 0;
		$allprefs['mazeturn']=0;
		set_module_pref('allprefs',serialize($allprefs));
		$allprefs=unserialize(get_module_pref('allprefs'));
		if (!isset($maze)){
			$maze = array(16,16,16,31,16,16,16,6,13,13,30,13,13,7,11,15,15,15,15,15,12,8,14,33,32,34,14,9,16,16,16,2,16,16,16);
			$umaze = implode($maze,",");
			set_module_pref("maze", $umaze);
		}
	}
	if ($op2 <> ""){
		if ($op2 == "n") {
			$locale+=7;
			redirect("runmodule.php?module=metalmine&op=tunnel&loc=$locale");
		}
		if ($op2 == "s"){
			$locale-=7;
			redirect("runmodule.php?module=metalmine&op=tunnel&loc=$locale");
		}
		if ($op2 == "w"){
			$locale-=1;
			redirect("runmodule.php?module=metalmine&op=tunnel&loc=$locale");
		}
		if ($op2 == "e"){
			$locale+=1;
			redirect("runmodule.php?module=metalmine&op=tunnel&loc=$locale");
		}
		redirect("runmodule.php?module=metalmine&op=tunnel&loc=$locale");
	}else{
		if ($locale <> ""){
			$maze=explode(",", $umaze);
			if ($locale=="") $locale = $upqtemp;
			$upqtemp = $locale;
			set_module_pref("pqtemp", $upqtemp);
			for ($i=0;$i<$locale-1;$i++){
			}
			$navigate=ltrim($maze[$i]);
			output("`7");
			if ($session['user']['hitpoints'] > 0){
				$allprefs=unserialize(get_module_pref('allprefs'));
				$allprefs['mazeturn']=$allprefs['mazeturn']+1;
				set_module_pref('allprefs',serialize($allprefs));
				$turn=$allprefs['mazeturn'];
				if ($locale=="32") addnav("Leave the Chamber","runmodule.php?module=metalmine&op=leavetunnel&op2=start");
				if ($locale=="4") addnav("Leave the Chamber","runmodule.php?module=metalmine&op=leavetunnel&op2=end");
				if ($allprefs['loc25t']==1) output("`b`^`cSecret tunnel`b`c`0");

				if($locale=="25" && $allprefs['loc25t']==0) redirect("runmodule.php?module=metalmine&op=tunnel&op2=25t");
				if ($locale=="32") output("`n`cYou are at the entrance with a passage to the South.`c");
				elseif ($locale=="4") output("`n`cYou safely make it out of the room!!`c");
				elseif (($locale=="17" || $locale=="19") && $turn=="5"){
					output("`n`cYou're pushed towards the center of the room... This looks bad for you.`c");
					$locale=18;
				}elseif ($turn==6 && $locale!=4) redirect("runmodule.php?module=metalmine&op=tunnel&op2=dead");
				else output("`n`cYou realize the safest thing for you to do is try to run south as quickly as possible!`c");

				if ($locale!="4") output("`n`cYou may go");
				$navcount = 0;
				$north=translate_inline("North");
				$south=translate_inline("South");
				$east=translate_inline("East");
				$west=translate_inline("West");
				$directions="";
				if ($navigate=="1" or $navigate=="5" or $navigate=="6"or $navigate=="7" or $navigate=="11" or $navigate=="12"or $navigate=="13" or $navigate=="15" or $navigate=="19"or $navigate=="20" or $navigate=="21" or $navigate=="30") {
					addnav("North","runmodule.php?module=metalmine&op=tunnel&op2=n&loc=$locale");
					$directions.=" $north";
					$navcount++;
				}
				if ($navigate=="2" or $navigate=="5" or $navigate=="8"or $navigate=="9" or $navigate=="11" or $navigate=="12"or $navigate=="14" or $navigate=="15" or $navigate=="18" or $navigate=="19" or $navigate=="30" or $navigate=="32" or $navigate=="33" or $navigate=="34") {
					addnav("South","runmodule.php?module=metalmine&op=tunnel&op2=s&loc=$locale");
					$navcount++;
					if ($navcount > 1) $directions.=",";
					$directions.=" $south";						
				}
				if ($navigate=="4" or $navigate=="7" or $navigate=="9"or $navigate=="10" or $navigate=="12" or $navigate=="13"or $navigate=="14" or $navigate=="15" or $navigate=="17" or $navigate=="21" or $navigate=="28" or $navigate=="32" or $navigate=="34") {
					addnav("West","runmodule.php?module=metalmine&op=tunnel&op2=w&loc=$locale");
					if (($locale=="18"||$locale=="25") && $turn>4) {
						blocknav("runmodule.php?module=metalmine&op=tunnel&op2=w&loc=$locale");
					}else{
						$navcount++;
						if ($navcount > 1) $directions.=",";
						$directions.=" $west";						
					}
				}
				if ($navigate=="3" or $navigate=="6" or $navigate=="8"or $navigate=="10" or $navigate=="11" or $navigate=="13"or $navigate=="14" or $navigate=="15" or $navigate=="17" or $navigate=="18" or $navigate=="20" or $navigate=="28" or $navigate=="29" or $navigate=="32" or $navigate=="33") {
					addnav("East","runmodule.php?module=metalmine&op=tunnel&op2=e&loc=$locale");
					if (($locale=="18"||$locale=="25") && $turn>4) {
						blocknav("runmodule.php?module=metalmine&op=tunnel&op2=e&loc=$locale");
					}else{
						$navcount++;
						if ($navcount > 1) $directions.=",";
						$directions.=" $east";						
					}
				}
				if ($locale!="4") output_notl(" %s.`c",$directions);

			}else{
				addnav("Continue","shades.php");
			}
			$mazemap=$navigate;
			$mazemap.="maze.gif";
			output_notl("`n`c");
			rawoutput("<small>");
			$mapkey2="<table style=\"height: 230px; width: 210px; text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td style=\"vertical-align: top;\">";
			$mapkey="";
			for ($i=0;$i<35;$i++){
				$keymap=ltrim($maze[$i]);
				$mazemap=$keymap;
				$mazemap.="maze.gif";
				if ($i==$locale-1){
					$mapkey.="<img src=\"./modules/metalmine/metalmineimg/mcyan.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";
				}else{
					//lights
					$temp=get_module_pref("pqtemp");
					$allprefs=unserialize(get_module_pref('allprefs'));
					$turn=$allprefs['mazeturn'];
					if (($i==31 || $i==7 || $i==14 ||$i==21 || $i==13 || $i==20 || $i==27) && get_module_pref("pqtemp")==25) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/16maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif (($i==31 || $i==7 || $i==14 ||$i==21 || $i==13 || $i==20 || $i==27 || $i==8 || $i==15 ||$i==22 || $i==12 || $i==19 || $i==26 || $i==9 || $i==16 ||$i==23 || $i==11 || $i==18 || $i==25 || $i==10 || $i==17|| $i==24) && $turn==6) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/16maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif (($i==31 || $i==7 || $i==14 ||$i==21 || $i==13 || $i==20 || $i==27 || $i==8 || $i==15 ||$i==22 || $i==12 || $i==19 || $i==26 || $i==9 || $i==16 ||$i==23 || $i==11 || $i==18 || $i==25 ) && $turn==5) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/16maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif (($i==31 || $i==7 || $i==14 ||$i==21 || $i==13 || $i==20 || $i==27 || $i==8 || $i==15 ||$i==22 || $i==12 || $i==19 || $i==26 ) && $turn==4) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/16maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					//main map production
					}else{
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/$mazemap\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";
					}
				}
				if ($i==6 or $i==13 or $i==20 or $i==27 or $i==34){
					$mapkey="`n".$mapkey;
					$mapkey2=$mapkey.$mapkey2;
					$mapkey="";
				}
			}
			$mapkey2.="</td></tr></tbody></table>";
			output_notl($mapkey2,true);
			output_notl("`c");
		}
	}
}
?>