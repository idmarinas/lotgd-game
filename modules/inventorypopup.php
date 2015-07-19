<?php
// translator ready
// addnews ready
// mail ready

function inventorypopup_getmoduleinfo(){
	$info = array(
		"name"=>"Inventory Popup System",
		"version"=>"1.0",
		"author"=>"Christian Rutsch",
		"category"=>"Inventory",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1033",
		"override_forced_nav"=>true,
	);
	return $info;
}
function inventorypopup_install(){
	module_addhook("charstats");
	return true;
}

function inventorypopup_uninstall(){
	return true;
}

function inventorypopup_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
		case "charstats":
			$open = translate_inline("Abrir Inventario");
			addnav("runmodule.php?module=inventorypopup");
			addcharstat("Información Equipo");
			addcharstat("Inventario", "<a href='runmodule.php?module=inventorypopup&TB_iframe=true&height=500&width=650' rel='sexylightbox' target='inventory'>$open</a>");
			break;
	}
	return $args;
}

function inventorypopup_run(){
	global $session;
	popup_header("Tu inventario");
	define("OVERRIDE_FORCED_NAV", true);
	require_once("lib/itemhandler.php");
	mydefine("HOOK_NEWDAY", 1);
	mydefine("HOOK_FOREST", 2);
	mydefine("HOOK_VILLAGE", 4);
	mydefine("HOOK_SHADES", 8);
	mydefine("HOOK_FIGHTNAV", 16);
	mydefine("HOOK_TRAIN", 32);
	mydefine("HOOK_INVENTORY", 64);

	require_once("lib/sanitize.php");
	$item = db_prefix("item");
	$inventory = db_prefix("inventory");
	$op2 = httpget('op2');
	$id = httpget('id');
	switch($op2) {
		case "equip":
		$thing = get_item((int)$id);
		$sql = "SELECT $inventory.itemid FROM $inventory INNER JOIN $item ON $inventory.itemid = $item.itemid WHERE $item.equipwhere = '".$thing['equipwhere']."' AND $inventory.equipped = 1";
		$result = db_query($sql);
		while ($row = db_fetch_assoc($result)) $wh[] = $row['itemid'];
		if (is_array($wh) && count($wh)) {
			modulehook("unequip-item", array("ids"=>$wh));
			$sql = "UPDATE $inventory SET equipped = 0 WHERE itemid IN (".join(",",$wh).")";
			db_query($sql);
		}
		modulehook("equip-item", array("id"=>$id));
		$sql = "UPDATE $inventory SET equipped = 1 WHERE itemid = $id AND userid = {$session['user']['acctid']} LIMIT 1";
		$result = db_query($sql);
		break;
		case "unequip":
		modulehook("unequip-item", array("ids"=>array($id)));
		$sql = "UPDATE $inventory SET equipped = 0 WHERE itemid = $id AND userid = {$session['user']['acctid']}";
		$result = db_query($sql);
		break;
		case "drop":
		$id = httpget('id');
		$invid = httpget('invid');
		remove_item((int)$id, 1, false, $invid);
		break;
		case "dropall":
		$id = httpget('id');
		$qty = httpget('qty');
		remove_item((int)$id, $qty);
		break;
		case "activate":
		$id = httpget('id');
		$acitem = get_inventory_item((int)$id);
		require_once("lib/buffs.php");
		if ($acitem['buffid'] > 0)
		apply_buff($acitem['name'], get_buff($acitem['buffid']));
		if ($acitem['charges'] > 1)
		uncharge_item((int)$id, 1);
		else
		remove_item((int)$id);
		if ($acitem['execvalue'] > "") {
			if ($acitem['exectext'] > "") {
				output($acitem['exectext'], $acitem['name']);
			} else {
				output("¡Activaste %s!", $acitem['name']);
			}
			require_once("lib/itemeffects.php");
			output_notl("`n`n%s", get_effect($acitem));
		}
		break;
	}
	require_once("lib/objetos.php");
	require_once("lib/penalizacion.php");
	output("`n`&Tienes un total de `^%s`& objetos en tu inventario.`nEl máximo de objetos que puedes llevar es %s`&.`n`n",$totalcount,$maxcount);
	output("El peso total de los objetos que llevas es `^%s`&.`nEl peso total que puedes llevar es %s.`nPorcentaje de peso $color%s%%.`n`n",$totalweight,$maxweight,$peso);
	output("`7Estás llevando los siguientes objetos:`n`n");
	$layout = array(
		//	"Weapons,title",
		//		"righthand",
		//		"lefthand",
		//	"Armor,title",
		//		"head",
		//		"body",
		//		"arms",
		//		"legs",
		//		"feet",
	"Varios,title",
		//		"ring1",
		//		"ring2",
		//   	"ring3",
		//		"neck",
		//		"belt",
	//		"Unequippables,title",
	"Unequippables",
	);
	$sql = "SELECT $item.*,
					MAX($inventory.equipped) AS equipped,
					COUNT($inventory.equipped) AS quantity,
					$inventory.sellvaluegold AS sellvaluegold,
					$inventory.sellvaluegems AS sellvaluegems,
					$inventory.invid AS invid
				FROM $item
				INNER JOIN $inventory ON $inventory.itemid = $item.itemid
				WHERE  $inventory.userid = {$session['user']['acctid']}
				GROUP BY $item.class ASC, $item.equipwhere  ASC, $item.name";
	/*$item.equippable = 0 AND*/
	$result = db_query($sql);
	$inventory = array();
	while($row = db_fetch_assoc($result)) {
		if ($row['equippable'] == false)
		$inventory['Unequippables'][] = $row;
		else
		$inventory[$row['equipwhere']][] = $row;
	}
	inventory_showform($layout, $inventory);
	popup_footer();
}

function inventory_showform($layout,$row){
	global $session;
	static $showform_id=0;
	static $title_id=0;
	$showform_id++;
	$formSections = array();
	if ($showform_id==1){
		$startIndex = (int)httppost("showFormTabIndex");
		if ($startIndex == 0){
			$startIndex = 1;
		}
		if (isset($session['user']['prefs']['tabconfig']) && $session['user']['prefs']['tabconfig'] == 0) {
		} else {
			rawoutput("
	 	<script language='JavaScript'>
	 	function prepare_form(id){
	 		var theTable;
	 		var theDivs='';
	 		var x=0;
	 		var weight='';
	 		for (x in formSections[id]){
	 			theTable = document.getElementById('showFormTable'+x);
	 			if (x != $startIndex ){
		 			theTable.style.visibility='hidden';
		 			theTable.style.display='none';
		 			weight='';
		 		}else{
		 			theTable.style.visibility='visible';
		 			theTable.style.display='inline';
		 			weight='color: yellow;';
		 		}
		 		theDivs += \"<div id='showFormButton\"+x+\"' class='trhead' style='\"+weight+\"float: left; cursor: pointer; cursor: hand; padding: 5px; border: 1px solid #000000;' onClick='showFormTabClick(\"+id+\",\"+x+\");'>\"+formSections[id][x]+\"</div>\";
	 		}
	 		theDivs += \"<div style='display: block;'>&nbsp;</div>\";
			theDivs += \"<input type='hidden' name='showFormTabIndex' value='$startIndex' id='showFormTabIndex'>\";
	 		document.getElementById('showFormSection'+id).innerHTML = theDivs;
	 	}
	 	function showFormTabClick(formid,sectionid){
	 		var theTable;
	 		var theButton;
	 		for (x in formSections[formid]){
	 			theTable = document.getElementById('showFormTable'+x);
	 			theButton = document.getElementById('showFormButton'+x);
	 			if (x == sectionid){
	 				theTable.style.visibility='visible';
	 				theTable.style.display='inline';
	 				theButton.style.fontWeight='normal';
	 				theButton.style.color='yellow';
					document.getElementById('showFormTabIndex').value = sectionid;
	 			}else{
	 				theTable.style.visibility='hidden';
	 				theTable.style.display='none';
	 				theButton.style.fontWeight='normal';
	 				theButton.style.color='';
	 			}
	 		}
	 	}
	 	formSections = new Array();
		</script>");
		}
	}
	rawoutput("<table width='100%' cellpadding='0' cellspacing='0'><tr><td>");
	rawoutput("<div id='showFormSection$showform_id'></div>");
	rawoutput("</td></tr><tr><td>&nbsp;</td></tr><tr><td>");
	$i = 0;
	$where = translate(array("righthand"=>"Mano Derecha","lefthand"=>"Mano Izquierda","head"=>"Cabeza","body"=>"Cuerpo","arms"=>"Brazos","legs"=>"Piernas","feet"=>"Pies","ring"=>"Primer Anillo","ring2"=>"Segundo Anillo","ring3"=>"Tercer Anillo","neck"=>"Collar","belt"=>"Cinturón"));
	$equip = translate_inline("Equipar");
	$unequip = translate_inline("Desequipar");
	$activate = translate_inline("Activar");
	$drop = translate_inline("Tirar");
	$dropall = translate_inline("Tirar Todo");
	while(list($key,$val)=each($layout)){
		if (is_array($val)) {
			$v = $val[0];
			$info = split(",", $v);
			$val[0] = $info[0];
			$info[0] = $val;
		} else {
			$info = split(",",$val);
		}
		if (is_array($info[0])) {
			$info[0] = call_user_func_array("sprintf_translate", $info[0]);
		} else {
			$info[0] = translate($info[0]);
		}
		if (isset($info[1])) $info[1] = trim($info[1]);
		else $info[1] = "";
			if ($info[1]=="title"){
			$title_id++;
			$formSections[$title_id] = $info[0];
			rawoutput("<table id='showFormTable$title_id' cellpadding='2' cellspacing='0'>");
			rawoutput("<tr><td colspan='2' class='trhead'>",true);
			output_notl("`b%s`b", $info[0], true);
			rawoutput("</td></tr>",true);
			$i=0;
		} else {
			if (isset($row[$val])) {
				$item = $row[$val];
				//				rawoutput("<tr class='".($i%2?'trlight':'trdark')."'><td valign='top'>");
				//				output_notl("%s ->", $wheres[$info[0]],true);
				//				rawoutput("</td><td valign='top'>");
				rawoutput("<table border=0 cellpadding=1 cellspacing=0>");
				$class = "";
				while(list($itskey, $itsval) = each($item)) {
					if ($itsval['class'] != $class) {
						rawoutput("<tr class='trhead'><td colspan=5>");
						$tl_class = translate_inline($itsval['class']);
						output_notl("`b`^%s`b", $tl_class);
						rawoutput("</td></tr>");
					}
					$colcount = 1;
					if ($itsval['equipped'] && $itsval['equippable']) $colcount++;
					if ($itsval['equippable'] == 1) $colcount++;
					if ($itsval['activationhook'] & 64) $colcount++;
					if ($itsval['droppable'] == true) $colcount++;
					rawoutput("<tr><td colspan='".(5-$colcount)."'>");
					output_notl("%s`7%s`7 (%s)", $itsval['equipped']?"`^*":"", $itsval['name'], $itsval['quantity']);
					if ($itsval['equipped'] && $itsval['equippable']) {
						rawoutput("<td>");
						rawoutput("[&nbsp;<a href='runmodule.php?module=inventorypopup&op2=unequip&id={$itsval['itemid']}'>$unequip</a>&nbsp;]");
						addnav("", "runmodule.php?module=inventorypopup&op2=unequip&id={$itsval['itemid']}");
						rawoutput("</td>");
					} else if ($itsval['equippable'] == 1) {
						rawoutput("<td>");
						rawoutput("[&nbsp;<a href='runmodule.php?module=inventorypopup&op2=equip&id={$itsval['itemid']}'>$equip</a>&nbsp;]");
						addnav("", "runmodule.php?module=inventorypopup&op2=equip&id={$itsval['itemid']}");
						rawoutput("</td>");
					}
					if ($itsval['activationhook'] & 64) {
						rawoutput("<td>");
						rawoutput("[&nbsp;<a href='runmodule.php?module=inventorypopup&op2=activate&id={$itsval['itemid']}'>$activate</a>&nbsp;]");
						addnav("", "runmodule.php?module=inventorypopup&op2=activate&id={$itsval['itemid']}");
						rawoutput("</td>");
					}
					if ($itsval['droppable'] == true) {
						rawoutput("<td>");
						rawoutput("[&nbsp;<a href='runmodule.php?module=inventorypopup&op2=drop&id={$itsval['itemid']}&invid={$itsval['invid']}'>$drop</a>&nbsp;|&nbsp;<a href='runmodule.php?module=inventorypopup&op2=dropall&id={$itsval['itemid']}&qty={$itsval['quantity']}'>$dropall</a>&nbsp;]");
						addnav("", "runmodule.php?module=inventorypopup&op2=drop&id={$itsval['itemid']}&invid={$itsval['invid']}");
						addnav("", "runmodule.php?module=inventorypopup&op2=dropall&id={$itsval['itemid']}&qty={$itsval['quantity']}");
						rawoutput("</td>");
					}
					rawoutput("<td nowrap>");
					output("(Valor Oro: %s, Valor Gemas: %s)", max($itsval['gold'],$itsval['sellvaluegold']), max($itsval['gems'], $itsval['sellvaluegems']));
					rawoutput("</td></tr>");
					rawoutput("<tr><td colspan='5'>");
					$tl_desc = translate_inline($itsval['description']);
					output_notl("`i%s`i", $tl_desc, true);
					rawoutput("</tr></td>");
					$class = $itsval['class'];
				}
				$i++;
			}
		}
		rawoutput("</td></tr>",true);
	}
	rawoutput("</table><br>",true);
	if (isset($session['user']['prefs']['tabconfig']) && $session['user']['prefs']['tabconfig'] == 0) {
	} else {
		rawoutput("<script language='JavaScript'>");
		rawoutput("formSections[$showform_id] = new Array();");
		reset($formSections);
		while (list($key,$val)=each($formSections)){
			rawoutput("formSections[$showform_id][$key] = '".addslashes($val)."';");
		}
		rawoutput("
	prepare_form($showform_id);
	</script>");
	}
	rawoutput("</td></tr></table>");
}
?>