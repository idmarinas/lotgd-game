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
			$open = translate_inline("Open Inventory");
			addnav("runmodule.php?module=inventorypopup");
			addcharstat("Equipment Info");
			addcharstat("Inventory", "<a href='runmodule.php?module=inventorypopup' target='inventory' onClick=\"".popup("runmodule.php?module=inventorypopup&op=charstat").";return false;\">$open</a>");
			break;
	}
	return $args;
}

function inventorypopup_run(){
	global $session;
	
	require_once("lib/itemhandler.php");
	require_once("lib/sanitize.php");
	
	popup_header("Your Inventory");
	
	mydefine("HOOK_NEWDAY", 1);
	mydefine("HOOK_FOREST", 2);
	mydefine("HOOK_VILLAGE", 4);
	mydefine("HOOK_SHADES", 8);
	mydefine("HOOK_FIGHTNAV", 16);
	mydefine("HOOK_TRAIN", 32);
	mydefine("HOOK_INVENTORY", 64);

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
					output("You activate %s!", $acitem['name']);
				}
				require_once("lib/itemeffects.php");
				output_notl("`n`n%s`n", get_effect($acitem));
			}
		break;
	}
	output("Your are currently wearing the following items:`n`n");
	$sql = "SELECT $item.*,
					MAX($inventory.equipped) AS equipped,
					COUNT($inventory.equipped) AS quantity,
					$inventory.sellvaluegold AS sellvaluegold,
					$inventory.sellvaluegems AS sellvaluegems,
					$inventory.invid AS invid
				FROM $item
				INNER JOIN $inventory ON $inventory.itemid = $item.itemid
				WHERE  $inventory.userid = {$session['user']['acctid']}
				GROUP BY $inventory.itemid
				ORDER BY $item.class ASC, $item.name ASC";
	/*$item.equippable = 0 AND*/
	$result = db_query($sql);
	$inventory = array();
	$layout = array();
	while($row = db_fetch_assoc($result)) {
		$layout[] = $row['class'] . ",title";
		$layout[] = $row['class'];
		$inventory[$row['class']][] = $row;
	}
	$layout = array_unique($layout);
	$materials = collect_materials($layout, $inventory);
	$layout = $materials['layout'];
	$inventory = $materials['inventory'];
	inventory_showform($layout, $inventory);
	// echo '<pre style="color:white">'. var_export($inventory, true) .'</pre>';
	popup_footer();
}

function inventory_showform($layout,$row)
{
	global $session;
 	static $showform_id=0;
 	static $title_id=0;
	if (is_array($layout) && empty($layout))
	{
		rawoutput("<table class='inventory'>");
		rawoutput("<tr><td>".translate_inline("The inventory is empty")."</td></tr>");
		rawoutput("</table>");
		
		return;
	}
	
 	$showform_id++;
 	$formSections = array();
	$returnvalues = array();
	$wheres = array("righthand"=>"Right Hand","lefthand"=>"Left Hand","head"=>"Your Head","body"=>"Upper Body","arms"=>"Your Arms","legs"=>"Lower Body","feet"=>"Your Feet","ring1"=>"First Ring","ring2"=>"Second Ring","ring3"=>"Third Ring","neck"=>"Around your Neck","belt"=>"Around your Waist");
	
	rawoutput("<table class='inventory'>");
	rawoutput("<tr><td><div id='showFormSection$showform_id'></div></td></tr>");
	rawoutput("<tr><td>");
	$i = 0;
	foreach ($layout as $key=>$val) {
		$pretrans = 0;
		if ($keypref !== false) $keyout = sprintf($keypref, $key);
		else $keyout = $key;
		if (is_array($val)) {
			$v = $val[0];
			$info = explode(",", $v);
			$val[0] = $info[0];
			$info[0] = $val;
		} else {
			$info = explode(",",$val);
		}
		if (is_array($info[0])) {
			$info[0] = $info[0];
		} else {
			$info[0] = $info[0];
		}
		if (isset($info[1])) $info[1] = trim($info[1]);
		else $info[1] = "";
		
		//Titulo
		if ($info[1]=="title")
		{
		 	$title_id++;
		 	$formSections[$title_id] = translate_inline($info[0]);
		 	rawoutput("<table id='showFormTable$title_id' cellpadding='2' cellspacing='0'>");
			$i=0;
		}
		//Items agrupados según categoría
		else
		{	
			rawoutput("<tr><td>");
			if (is_array($row[$info[0]]))
			{
				$itemI = 0;
				$class = "";
				foreach($row[$info[0]] as $value)
				{
					debug($info[0]);
					if ('Material' != $info[0])
					{
						showRowItem($value, $itemI);
					}
					else
					{
						show_material_row($value, $itemI);
					}
					$class = $value['class'];
					$itemI++;
				}
			}
			else
			{
				output("Nothing in this category");
			}
			rawoutput("</td></tr><td>");
			$i++;
		}
		rawoutput("</td></tr>",true);
	}
	rawoutput("</table>",true);
	if ($showform_id==1){
		$startIndex = (int)httppost("showFormTabIndex");
		if ($startIndex == 0){
			$startIndex = 1;
		}
		if (isset($session['user']['prefs']['tabconfig']) &&
				$session['user']['prefs']['tabconfig'] == 0) {
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
			 			theTable.style.display='inline-table';
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
		 				theTable.style.display='inline-table';
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
	if (isset($session['user']['prefs']['tabconfig']) &&
			$session['user']['prefs']['tabconfig'] == 0) {
	} else {
		rawoutput("<script language='JavaScript'>");
		rawoutput("formSections[$showform_id] = new Array();");
		foreach ($formSections as $key=>$val) {
			rawoutput("formSections[$showform_id][$key] = '".addslashes($val)."';");
		}
		rawoutput("
		prepare_form($showform_id);
		</script>");
	}
	rawoutput("</td></tr></table>");

	return $returnvalues;
}

//** Mostrar un item
function showRowItem($itsval, $i)
{
	$equip = translate_inline("Equip");
	$unequip = translate_inline("Unequip");
	$activate = translate_inline("Activate");
	$drop = translate_inline("Drop");
	$dropall = translate_inline("All");
	
	rawoutput("<table class='items-list ".($i%2?'trlight':'trdark')."'><tr><td>");
	rawoutput($itsval['equipped']?"<i class='fa fa-asterisk fa-fw'></i>":"");
	output_notl("`0%s (%s)", $itsval['name'], $itsval['quantity']);
	rawoutput("</td>");
	//## Opciones
	rawoutput("<td>");
		if ($itsval['equipped'] && $itsval['equippable']) {
			rawoutput("[&nbsp;<a href='runmodule.php?module=inventorypopup&op2=unequip&id={$itsval['itemid']}'>$unequip</a>&nbsp;]");
			addnav("", "runmodule.php?module=inventorypopup&op2=unequip&id={$itsval['itemid']}");
		} else if ($itsval['equippable'] == 1) {
			rawoutput("[&nbsp;<a href='runmodule.php?module=inventorypopup&op2=equip&id={$itsval['itemid']}'>$equip</a>&nbsp;]");
			addnav("", "runmodule.php?module=inventorypopup&op2=equip&id={$itsval['itemid']}");
		}
		if ($itsval['activationhook'] & 64) {
			rawoutput("[&nbsp;<a href='runmodule.php?module=inventorypopup&op2=activate&id={$itsval['itemid']}'>$activate</a>&nbsp;]");
			addnav("", "runmodule.php?module=inventorypopup&op2=activate&id={$itsval['itemid']}");
		}
		if ($itsval['droppable'] == true) {
			rawoutput("[&nbsp;<a href='runmodule.php?module=inventorypopup&op2=drop&id={$itsval['itemid']}&invid={$itsval['invid']}'>$drop</a>&nbsp;|&nbsp;<a href='runmodule.php?module=inventorypopup&op2=dropall&id={$itsval['itemid']}&qty={$itsval['quantity']}'>$dropall</a>&nbsp;]");
			addnav("", "runmodule.php?module=inventorypopup&op2=drop&id={$itsval['itemid']}&invid={$itsval['invid']}");
			addnav("", "runmodule.php?module=inventorypopup&op2=dropall&id={$itsval['itemid']}&qty={$itsval['quantity']}");
		}
	rawoutput("</td>");
	
	rawoutput("<td nowrap>");
	output("(Gold value: %s, Gem Value: %s)", max($itsval['gold'],$itsval['sellvaluegold']), max($itsval['gems'], $itsval['sellvaluegems']));
	$tl_desc = translate_inline($itsval['description']);
	rawoutput("</td></tr>");
	if ('' != $itsval['description']) 
	{
		rawoutput('<tr><td colspan="3">');
		output_notl("`i%s`i", $tl_desc, true);
		rawoutput('</td></tr>');
	}
	rawoutput("</table>");
}

/**
 * Extraer los materiales disponibles
 */
function collect_materials($layout, $inventory)
{	
	global $session;
	
	//Madera disponible
	$allprefs = unserialize(get_module_pref('allprefs','lumberyard'));
	$squarepay = get_module_setting("squarepay",'lumberyard');

	if (get_module_setting("leveladj",'lumberyard')==1) $squarepay=round($squarepay*$session['user']['level'] / 15);
	$squares = array (
		'class' => 'Material',
		'name' => 'Pieza de madera',
		'quantity' => (int) $allprefs['squares'],
		'gold' => $squarepay
	);
		
	if ($squares['quantity'] > 0)
	{
		$layout[] = 'Material,title';
		$layout[] = 'Material';
		$inventory['Material'][] = $squares;
	}
	
	return array('layout' => $layout, 'inventory' => $inventory);

// 	//BEGIN CHECK AND DISPLAY FOR QUARRY BY DAVES
// 	if (is_module_installed("quarry")) {
// 		$allprefs=unserialize(get_module_pref('allprefs','quarry'));
// 		$blocks=$allprefs['blocks'];
// 		if ($blocks >= '1') { 
// 			$ti_sackcat_buildingmaterials=1;
// 			$stone=1;
// 		}
// 	}
// 
// 	//BEGIN CHECK AND DISPLAY FOR METALMINE BY DAVES
// 	if (is_module_installed("metalmine")) {
// 		$allprefs=unserialize(get_module_pref('allprefs','metalmine'));
// 		$metal1=$allprefs['metal1'];
// 		$metal2=$allprefs['metal2'];
// 		$metal3=$allprefs['metal3'];
// 		if ($squares>=1 or $blocks>=1 or $metal1>=1 or $metal2>=1 or $metal3>=1) {
// 			$ti_sackcat_buildingmaterials=1;
// 			$metal=1;
// 		}
// 	}
}

/**
 * Mostrar los materiales
 */
function show_material_row($itsval, $i)
{
	rawoutput("<table class='items-list ".($i%2?'trlight':'trdark')."'><tr><td>");
	rawoutput($itsval['equipped']?"<i class='fa fa-asterisk fa-fw'></i>":"");
	output_notl("`0%s (%s)", $itsval['name'], $itsval['quantity']);
	rawoutput("</td>");
	rawoutput("<td nowrap>");
	output("Valor oro: %s. Este precio puede variar.", $itsval['gold']);
	$tl_desc = translate_inline($itsval['description']);
	rawoutput('</td></tr></table>');
}
?>