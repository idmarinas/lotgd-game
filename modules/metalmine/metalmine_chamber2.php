<?php
function metalmine_chamber2(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$locale = httpget('loc');
	$op2 = httpget('op2');
	$misc= array ('25a','8ball','aska','question');
	if (in_array($op2,$misc)){
		metalmine_misc($op2);
	}
	page_header("Secret Chamber");
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
			$maze = array(16,16,16,1,16,16,16,6,13,13,15,13,13,7,11,15,15,35,15,15,12,8,14,14,15,14,14,9,16,16,16,2,16,16,16);
			$umaze = implode($maze,",");
			set_module_pref("maze", $umaze);
		}
	}
	if ($op2 <> ""){
		if ($op2 == "n") {
			$locale+=7;
			redirect("runmodule.php?module=metalmine&op=chamber2&loc=$locale");
		}
		if ($op2 == "s"){
			$locale-=7;
			redirect("runmodule.php?module=metalmine&op=chamber2&loc=$locale");
		}
		if ($op2 == "w"){
			$locale-=1;
			redirect("runmodule.php?module=metalmine&op=chamber2&loc=$locale");
		}
		if ($op2 == "e"){
			$locale+=1;
			redirect("runmodule.php?module=metalmine&op=chamber2&loc=$locale");
		}
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
				if ($locale=="32") addnav("Leave the Chamber","runmodule.php?module=metalmine&op=leavechamber2&op2=top");
				else output("`b`^`cSecret Chamber`b`c`0");
				
				if ($locale=="4") addnav("Leave the Chamber","runmodule.php?module=metalmine&op=leavechamber2&op2=bottom");
				$allprefs=unserialize(get_module_pref('allprefs'));
				if ($locale=="25" && $allprefs['loc25a']==0) redirect("runmodule.php?module=metalmine&op=chamber2&op2=25a");

				if ($locale=="18") {
					addnav("Examine");
					addnav("Examine the Black Ball","runmodule.php?module=metalmine&op=chamber2&op2=8ball");
					addnav("Navigation");
				}
				output("`n`cYou may go");
				$umazeturn++;
				$allprefs['mazeturn']=$allprefs['mazeturn']+$umazeturn;
				set_module_pref('allprefs',serialize($allprefs));
				$allprefs=unserialize(get_module_pref('allprefs'));
				$navcount = 0;
				$north=translate_inline("North");
				$south=translate_inline("South");
				$east=translate_inline("East");
				$west=translate_inline("West");
				$directions="";
				if ($navigate=="1" or $navigate=="5" or $navigate=="6"or $navigate=="7" or $navigate=="11" or $navigate=="12"or $navigate=="13" or $navigate=="15" or $navigate=="19"or $navigate=="20" or $navigate=="21" || $navigate=="35") {
					addnav("North","runmodule.php?module=metalmine&op=chamber2&op2=n&loc=$locale");
					$directions.=" $north";
					$navcount++;
				}
				if ($navigate=="2" or $navigate=="5" or $navigate=="8"or $navigate=="9" or $navigate=="11" or $navigate=="12"or $navigate=="14" or $navigate=="15" or $navigate=="18" or $navigate=="19" || $navigate=="35") {
					addnav("South","runmodule.php?module=metalmine&op=chamber2&op2=s&loc=$locale");
					$navcount++;
					if ($navcount > 1) $directions.=",";
					$directions.=" $south";						
				}
				if ($navigate=="4" or $navigate=="7" or $navigate=="9"or $navigate=="10" or $navigate=="12" or $navigate=="13"or $navigate=="14" or $navigate=="15" or $navigate=="17" or $navigate=="21" or $navigate=="28" || $navigate=="35") {
					addnav("West","runmodule.php?module=metalmine&op=chamber2&op2=w&loc=$locale");
					$navcount++;
					if ($navcount > 1) $directions.=",";
					$directions.=" $west";
				}
				if ($navigate=="3" or $navigate=="6" or $navigate=="8"or $navigate=="10" or $navigate=="11" or $navigate=="13"or $navigate=="14" or $navigate=="15" or $navigate=="17" or $navigate=="18" or $navigate=="20" or $navigate=="28" or $navigate=="29" || $navigate=="35") {
					addnav("East","runmodule.php?module=metalmine&op=chamber2&op2=e&loc=$locale");
					$navcount++;
					if ($navcount > 1) $directions.=",";
					$directions.=" $east";
				}
				output_notl(" %s.`c",$directions);				
			}else{
				addnav("Continue","shades.php");
			}
			$mazemap=$navigate;
			$mazemap.="maze.gif";
			output_notl("`n`c");
			rawoutput("<small>");
			$mapkey2="<table style=\"height: 230px; width: 210px; text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td style=\"vertical-align: top;\">";
			$mapkey="";
			for ($i=0;$i<36;$i++){
				$keymap=ltrim($maze[$i]);
				$mazemap=$keymap;
				$mazemap.="maze.gif";
				if ($i==$locale-1){
					$mapkey.="<img src=\"./modules/metalmine/metalmineimg/mcyan.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";
				}else{
					//lights
					if ($i==17 && $allprefs['loc25a']==0) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/15maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
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