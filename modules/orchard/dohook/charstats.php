<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if(get_module_pref("user_stat")>0){
		$names=translate_inline(array("","`\$Apple","`QOrange","`6Pear","`QApricot","`^Banana","`QPeach","`5Plum","`qFig","`^Mango","`\$Cherry","`QTangerine","`^Grapefruit","`^Lemon","`@Avocado","`2Lime","`\$Pomegranate","`qKiwi","`4Cranberry","`^Star Fruit","`@Dragon`\$fruit"));
		$tree=$allprefs['tree'];
		if($tree>0) {
			if (get_module_pref("user_stat")==2){
				$fruitpic=".gif";
				$fruitimg=$tree.$fruitpic;
				$fruit="<img src=\"./modules/orchard/orchardimg/$fruitimg\" title=\"\" alt=\"\" style=\"width: 30px; height: 30px;\">";
				setcharstat("Personal Info", "Tree", $fruit);
			}else{
				setcharstat("Personal Info", "Tree", "`@" . $names[$tree]);				
			}
		}
	}
?>