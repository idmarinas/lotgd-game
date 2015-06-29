<?php
function metalmine_chamber(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$locale = httpget('loc');
	$op2 = httpget('op2');
	$misc= array ('24','17','9','2','11','search','1','sarc');
	if (in_array($op2,$misc)){
		metalmine_misc($op2);
	}
	page_header("Secret Chamber");
	if ($session['user']['hitpoints'] <= 0) redirect("shades.php");
	$umaze = get_module_pref('maze');
	$umazeturn = $allprefs['mazeturn'];
	$upqtemp = get_module_pref('pqtemp');
	if ($op2 == "" && $locale == "") {
		$locale=29;
		output("`c`b`^Secret Chamber`0`b`c");
		output("`2You climb down into the Secret Chamber and let your eyes adjust to the darkness.");
		output("You can't see much from where you're standing.`n`n");
		$umazeturn = 0;
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['found']=1;
		$allprefs['mazeturn']=0;
		set_module_pref('allprefs',serialize($allprefs));
		if (!isset($maze)){
			$maze = array(29,17,28,13,4,16,16,16,5,16,1,16,6,15,7,11,10,15,15,12,2,16,8,15,9,16,16,16,2,16);
			$umaze = implode($maze,",");
			set_module_pref("maze", $umaze);
		}
	}
	if ($op2 <> ""){
		if ($op2 == "n") {
			$locale+=5;
			redirect("runmodule.php?module=metalmine&op=chamber&loc=$locale");
		}
		if ($op2 == "s"){
			$locale-=5;
			redirect("runmodule.php?module=metalmine&op=chamber&loc=$locale");
		}
		if ($op2 == "w"){
			$locale-=1;
			redirect("runmodule.php?module=metalmine&op=chamber&loc=$locale");
		}
		if ($op2 == "e"){
			$locale+=1;
			redirect("runmodule.php?module=metalmine&op=chamber&loc=$locale");
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
				$allprefs=unserialize(get_module_pref('allprefs'));
				if ($locale=="29") addnav("Leave the Chamber","runmodule.php?module=metalmine&op=leavechamber");
				if ($allprefs['loc24']==1) output("`b`^`cSecret Chamber`b`c`0");

				if($locale=="24" && $allprefs['loc24']==0) redirect("runmodule.php?module=metalmine&op=chamber&op2=24");
				elseif($locale=="17" && $allprefs['loc17']==0) redirect("runmodule.php?module=metalmine&op=chamber&op2=17");
				elseif($locale=="9" && $allprefs['loc9']==0) redirect("runmodule.php?module=metalmine&op=chamber&op2=9");
				elseif($locale=="2" && $allprefs['loc2']==0) redirect("runmodule.php?module=metalmine&op=chamber&op2=2");
				elseif($locale=="11" && $allprefs['loc11']==0) redirect("runmodule.php?module=metalmine&op=chamber&op2=11");
				elseif($locale=="1" && $allprefs['loc1']==0) redirect("runmodule.php?module=metalmine&op=chamber&op2=1");

				output("`n`cYou may go");
				$umazeturn++;
				$allprefs['mazeturn']=$umazeturn;
				set_module_pref('allprefs',serialize($allprefs));
				$allprefs=unserialize(get_module_pref('allprefs'));
				$navcount = 0;
				$north=translate_inline("North");
				$south=translate_inline("South");
				$east=translate_inline("East");
				$west=translate_inline("West");
				$directions="";
				if ($navigate=="1" or $navigate=="5" or $navigate=="6"or $navigate=="7" or $navigate=="11" or $navigate=="12"or $navigate=="13" or $navigate=="15" or $navigate=="19"or $navigate=="20" or $navigate=="21") {
					addnav("North","runmodule.php?module=metalmine&op=chamber&op2=n&loc=$locale");
					$directions.=" $north";
					$navcount++;
				}
				if ($navigate=="2" or $navigate=="5" or $navigate=="8"or $navigate=="9" or $navigate=="11" or $navigate=="12"or $navigate=="14" or $navigate=="15" or $navigate=="18" or $navigate=="19") {
					addnav("South","runmodule.php?module=metalmine&op=chamber&op2=s&loc=$locale");
					$navcount++;
					if ($navcount > 1) $directions.=",";
					$directions.=" $south";
				}
				if ($navigate=="4" or $navigate=="7" or $navigate=="9"or $navigate=="10" or $navigate=="12" or $navigate=="13"or $navigate=="14" or $navigate=="15" or $navigate=="17" or $navigate=="21" or $navigate=="28") {
					addnav("West","runmodule.php?module=metalmine&op=chamber&op2=w&loc=$locale");
					$navcount++;
					if ($navcount > 1) $directions.=",";
					$directions.=" $west";
				}
				if ($navigate=="3" or $navigate=="6" or $navigate=="8"or $navigate=="10" or $navigate=="11" or $navigate=="13"or $navigate=="14" or $navigate=="15" or $navigate=="17" or $navigate=="18" or $navigate=="20" or $navigate=="28" or $navigate=="29") {
						addnav("East","runmodule.php?module=metalmine&op=chamber&op2=e&loc=$locale");
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
			for ($i=0;$i<30;$i++){
				$keymap=ltrim($maze[$i]);
				$mazemap=$keymap;
				$mazemap.="maze.gif";
				if ($i==$locale-1){
					$chance=e_rand(1,200);
					if ($chance==1) $figure="mcyan3.gif";
					else $figure="mcyan.gif";
					$mapkey.="<img src=\"./modules/metalmine/metalmineimg/$figure\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";
				}else{
					//lights
					$allprefs=unserialize(get_module_pref('allprefs'));
					if (($i==22 || $i==23 ||$i==24||$i==17 || $i==18 ||$i==19||$i==12 || $i==13 ||$i==14) && $allprefs['loc24']==0) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/16maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif (($i==2 || $i==3 ||$i==4||$i==8) && $allprefs['loc9']==0) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/16maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif (($i==20 || $i==10 ||$i==15||$i==16) && $allprefs['loc17']==0) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/16maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif ($i==1 && $allprefs['loc2']==1) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/10maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif ($i==0 && $allprefs['loc2']==0) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/16maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif ($i==2 && $allprefs['loc2']==1) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/10maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}elseif ($i==0 && $allprefs['loc1']==1) {
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/3maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					//main map production
					}else{
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/$mazemap\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";
					}
				}
				if ($i==4 or $i==9 or $i==14 or $i==19 or $i==24 or $i==29){
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