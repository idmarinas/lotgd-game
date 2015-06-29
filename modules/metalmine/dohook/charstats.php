<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	$metal1=$allprefs['metal1'];
	$metal2=$allprefs['metal2'];
	$metal3=$allprefs['metal3'];
	if($metal1>0 || $metal2>0 || $metal3>0){
		$stat=get_module_pref("user_stat");
		if ($metal1==0) $metal1=translate_inline("None");
		if ($metal2==0) $metal2=translate_inline("None");
		if ($metal3==0) $metal3=translate_inline("None");
		if ($stat==1) setcharstat("Personal Info", "`)Iron Ore", "`^$metal1");
		elseif ($stat==2) setcharstat("Materials", "`)Iron Ore", "`^$metal1");
		if ($stat==1) setcharstat("Personal Info", "`QCopper", "`^$metal2");
		elseif ($stat==2) setcharstat("Materials", "`QCopper", "`^$metal2");
		if ($stat==1) setcharstat("Personal Info", "`&Mithril", "`^$metal3");
		elseif ($stat==2) setcharstat("Materials", "`&Mithril", "`^$metal3");
	}
	if ($allprefs['inmine']==1){
		$pickaxe=$allprefs['pickaxe'];
		$helmet=$allprefs['helmet'];
		$mineturns=get_module_setting("mineturnset")-$allprefs['usedmts'];
		if ($pickaxe==1) $type1="General ";
		if ($pickaxe==2) $type1="Standard ";
		if ($pickaxe==3) $type1="Quality ";
		if ($helmet==1) $type2="General ";
		if ($helmet==2) $type2="Standard ";
		if ($helmet==3) $type2="Quality ";
		if ($pickaxe==0) $pick="None";
		else $pick="Pickaxe";
		if ($helmet==0) $helm="None";
		else $helm="Helmet";
		if ($allprefs['canary']!="") $canary=$allprefs['canary'];
		else $canary="None";
		$pickaxestat=$type1.$pick;
		$helmetstat=$type2.$helm;
		if($helmet>0){
			if ($allprefs['oil']<250) $helmetstat=$type2.$helm." `@(Full Oil)";
			elseif ($allprefs['oil']<750) $helmetstat=$type2.$helm." `^(Medium Oil)";
			elseif ($allprefs['oil']<1000) $helmetstat=$type2.$helm." `\$(Low Oil)";
			else $helmetstat=$type2.$helm." `)(No Oil)";
		}
		addcharstat("Metal Mining");
		setcharstat("Metal Mining","Pickaxe","`^$pickaxestat");
		setcharstat("Metal Mining","Helmet","`^$helmetstat");
		setcharstat("Metal Mining","Canary","`^$canary");
		setcharstat("Metal Mining","Mine Turns","`^$mineturns");
	}
?>