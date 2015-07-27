<?php
/***********************************************
 World Map
 Originally by: Aes
 Updates & Maintenance by: Kevin Hatfield - Arune (khatfield@ecsportal.com)
 Updates & Maintenance by: Roland Lichti - klenkes (klenkes@paladins-inn.de)
 http://www.dragonprime.net
 Updated: Feb 23, 2008
 ************************************************/

require_once('modules/worldmapen/lib.php');
global $nlink, $elink, $wlink, $slink, $nelink, $nwlink, $selink, $swlink;
$nlink = $elink = $wlink = $slink = $nelink = $nwlink = $selink = $swlink = '#';

function worldmapen_editor_real(){
	global $session;

	page_header("World Editor");
	require_once("lib/superusernav.php");
	superusernav();

	// initialize the internal static maps
	worldmapen_loadMap();
	worldmapen_loadTerrainDefs();

	$op = httpget("op");
	$act = httpget("act");
	$subop = httpget("subop");
	debug("op={$op}, act={$act}, subop={$subop}");
	switch ($subop) {
		case "regen":	worldmapen_editor_regen($op, $subop, $act); break;
		case "manual":	worldmapen_editor_manual($op, $subop, $act); break;
		case "terrain":	worldmapen_editor_terrain($op, $subop, $act); break;

		default:		worldmapen_viewmap(false); break;
	}

	addnav("Replace Cities","runmodule.php?module=worldmapen&op=edit&subop=regen");
	addnav("Manually Place Cities","runmodule.php?module=worldmapen&op=edit&subop=manual");
	addnav("Edit terrain type","runmodule.php?module=worldmapen&op=edit&subop=terrain");
	page_footer();
}

function worldmapen_editor_regen($op, $subop, $act) {
	worldmapen_defaultcityloc();
	worldmapen_viewmap(false);
}

function worldmapen_editor_manual($op, $subop, $act) {
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname] = "village";
	$vloc = modulehook("validlocation", $vloc);
	ksort($vloc);

	if ($act == "save"){
		foreach($vloc as $loc=>$val) {
			$space_valx = preg_replace('/\s/', '_',$loc.'X');
			$space_valy = preg_replace('/\s/', '_',$loc.'Y');
			set_module_setting($loc.'X',httppost($space_valx));
			set_module_setting($loc.'Y',httppost($space_valy));
			set_module_setting($loc.'Z', 1);
			// Eventually we'll do the Z coord too
			// set_module_setting($loc.'Z',
			//        httppost($loc."Z"));
		}
		output("`^`bSettings saved successfully.`b`n");
		reset($vloc);
	}
	output("`^Maximum X value is `b%s`b`n", get_module_setting("worldmapsizeX"));
	output("`^Maximum Y value is `b%s`b`n", get_module_setting("worldmapsizeY"));

	$worldarray=array("World Locations,title");
	foreach($vloc as $loc=>$val) {
		$mapx=get_module_setting("worldmapsizeX");
		$mapy=get_module_setting("worldmapsizeY");
		//Added to allow setting cities outside of the map. - Making cities inaccessible via normal travel.
		$myx = $mapx+1;
		$worldarray[] = array("Locations for %s,title", $loc);
		$worldarray[$loc.'X']=array("X Coordinate,range,1,$myx,1");
		$worldarray[$loc.'Y']=array("Y coordinate,range,1,$mapy,1");
	}
	rawoutput("<form method='post' action='runmodule.php?module=worldmapen&op=edit&subop=manual&act=save&admin=true'>");

	require_once("lib/showform.php");
	global $module_settings;
	showform($worldarray, $module_settings['worldmapen']);
	rawoutput("</form>");

	addnav("","runmodule.php?module=worldmapen&op=edit&subop=manual&act=save&admin=true");
	addnav("E?Return to World Map Editor","runmodule.php?module=worldmapen&op=edit&admin=true");
}

function worldmapen_editor_terrain($op, $subop, $act) {
	global $_POST, $worldmapen_globals;

	if ($act == "save"){
		$terrainDefs = worldmapen_loadTerrainDefs();
		
		reset($_POST);
		foreach ($_POST AS $key=>$value) {
			list($x,$y) = explode("_", $key, 2);
			
			if (is_numeric($x) && is_numeric($y)) {
				worldmapen_setTerrain($x, $y, 1, $value);
			}
		}
		worldmapen_saveMap();
		
		output("Worldmap saved");
	}
	$colors = array();
	foreach( $worldmapen_globals['terrainDefs'] as $defs )
	{
		$colors[]= $defs['color'];
	}
	// -----------------------------------------------------------------------
	// BEGIN - Java script to determine the terrain type by clicking on a td cell
	// -----------------------------------------------------------------------
	rawoutput('<script type="text/javascript">colors = '.json_encode($colors).';function changeColor(target){var length = colors.length;	var value = parseInt($("#"+target+"b").val());	value = isNaN(value) ? 0 : value+1;	if (value >= length) { value = 0; }$("#"+target).removeClass().addClass(colors[value]);$("#"+target+"b").val(value);}</script>');
	// -----------------------------------------------------------------------
	// END - Java script to determine the terrain type by clicking on a td cell
	// -----------------------------------------------------------------------
	rawoutput( '<noscript>'.translate_inline("JavaScript must be enabled for the terrain editor to work.").'</noscript>', true );
	worldmapen_viewmap(false);
}
?>