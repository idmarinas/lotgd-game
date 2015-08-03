<?php
/***********************************************
 World Map
 Originally by: Aes
 Updates & Maintenance by: Kevin Hatfield - Arune (khatfield@ecsportal.com)
 Updates & Maintenance by: Roland Lichti - klenkes (klenkes@paladins-inn.de)
 http://www.dragonprime.net
 Updated: Feb 23, 2008
 ************************************************/

$worldmapen_globals = array(
"terrainDefs"	=> null,
"map"			=> null,
"colors"		=> null,
);				// global vars as array (only one global var needed)


// -----------------------------------------------------------------------
// BEGIN - FUNCTIONS
// -----------------------------------------------------------------------
// -----------------------------------------------------------------------
// BEGIN - worldmapen_defaultcityloc determines the default city locations
//         for all cities in the game!
// -----------------------------------------------------------------------
function worldmapen_defaultcityloc(){
	global $session;
	$i = 0;
	$citylocX = 0;
	$citylocY = 0;
	$citylocations = array();
	$citylocations[][] = "";
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname]= "village";
	$vloc = modulehook("validlocation", $vloc);
	foreach($vloc as $loc=>$val) {
		$k = 0;
		while ($k == 0){
			foreach($citylocations as $val1){
				if (($val1[0] == $citylocX) && ($val1[1] == $citylocY)){
					$k = 0;

					$citylocX = e_rand(1, get_module_setting("worldmapsizeX"));
					$citylocY = e_rand(1, get_module_setting("worldmapsizeY"));
				}else{
					$k++;
					$citylocations[$i][0] = $citylocX;
					$citylocations[$i][1] = $citylocY;
					set_module_setting($loc.'X', $citylocX);
					set_module_setting($loc.'Y', $citylocY);
					set_module_setting($loc.'Z', "1");
				}
			}
		}
		$i++;
	}
}
// -----------------------------------------------------------------------
// END - worldmapen_defaultcityloc determines the default city locations
//         for all cities in the game!
// -----------------------------------------------------------------------
// -----------------------------------------------------------------------
// BEGIN - worldmapen_viewmap allows players to view the world map if they
//         have purchased one from the Gypsy or Item Shop
// -----------------------------------------------------------------------
function worldmapen_viewmap($showloc){
	global $session;
	$op = httpget("op");
	$subop = httpget("subop");
	$act = httpget("act");

	if (get_module_pref("worldmapbuy") == 1 || ($session['user']['superuser'] & SU_EDIT_USERS)){
		$colorUserLoc = get_module_setting("colorUserLoc");
		
		$vloc = array();
		$vname = getsetting("villagename", LOCATION_FIELDS);
		$vloc[$vname] = "village";
		
		$vloc = modulehook("validlocation", $vloc);
		
		$loc = get_module_pref("worldXYZ");
		list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);
		
		$sizeX = get_module_setting("worldmapsizeX");
		$sizeY = get_module_setting("worldmapsizeY");
		$rowspanY = $sizeY+1;

		output("`^`c`bWorld Map`b`c`n");

		$map = worldmapen_loadMap();
		$colors = worldmapen_getColorDefinitions();
		
//		debug("map="); debug($map);
//		debug("colors="); debug($colors);

		
		// -----------------------------------------------------------------------
		// BEGIN - Display the simple map
		// -----------------------------------------------------------------------
		if ($op=="viewmap" || $subop=="" || $subop=="regen"){
			rawoutput("<div class='worldmapen'><table class='map1'>");

			for ($y = $sizeY; $y > 0; $y--) {
				rawoutput("<tr>");
				rawoutput("<td width='20' align='right'>$y</td>");
				for ($x = 1; $x <= $sizeX; $x++){
					$cellvalue = $map[$x][$y];
					$terrainName = translate_inline($map[$x][$y]);
					
					foreach($vloc as $loc=>$val) {
						if ($y == get_module_setting($loc."Y") && $x == get_module_setting($loc."X")) {
							$cityText = "<i class='fa fa-home fa-fw' data-uk-tooltip title='$loc ({$x}, {$y}) ($terrainName)'></i>";
							$city = true;
							break;
						}
						else
						{
							$cityText = "";
							$city = false;
						}
					}
					if ($showloc){
						if ($y == $worldmapY && $x == $worldmapX){
							rawoutput("<td class='{$colorUserLoc}'>");
						}else{
							if (!$city)
								rawoutput("<td class='{$colors[$cellvalue]}' data-uk-tooltip alt='({$x}, {$y}) ($terrainName)' title='({$x}, {$y}) $terrainName'>");
							else
								rawoutput("<td class='{$colors[$cellvalue]}'>");
						}
					}else{
						if (!$city)
							rawoutput("<td class='{$colors[$cellvalue]}' data-uk-tooltip alt='({$x}, {$y}) ($terrainName)' title='({$x}, {$y}) $terrainName'>");
						else
							rawoutput("<td class='{$colors[$cellvalue]}'>");
					}
					rawoutput($cityText);
					rawoutput("</td>");
				}
				rawoutput("</tr>");
			}
			rawoutput("<tr>");
			output_notl("<td>`6`bY/X`b`0</td>",true);
			for ($x = 1; $x <= $sizeX ; $x++){
				rawoutput("<td>{$x}</td>",true);
			}
			rawoutput("</tr>");
			rawoutput("</table>");
			
			if (get_module_setting("showcompass") == 1){
				rawoutput("<hr><table><tr><td>");
				worldmapen_showcompass();
				rawoutput("</td></tr></table>");
			}
			rawoutput("</div>");
		}

		// -----------------------------------------------------------------------
		// END - Display the simple map
		// -----------------------------------------------------------------------

		// -----------------------------------------------------------------------
		// BEGIN - Display the advanced map with terrain editor
		// -----------------------------------------------------------------------
		if ($subop=="terrain"){
			rawoutput("<form method='post' action='runmodule.php?module=worldmapen&op=edit&subop=terrain&act=save'>",true);
			rawoutput("<div class='worldmapen text-center'>");
			rawoutput("<table class='map2'>");
			for ($y = $sizeY; $y > 0; $y--){
				rawoutput("<tr>");
				rawoutput("<td>{$y}</td>");
				for ($x = 1; $x <= $sizeX; $x++){
						
					$cellvalue = $map[$x][$y];
					$terrainName = translate_inline($map[$x][$y]);
					// We do y x y for the id to address issues when x = 11 y = 5 and x = 1 and y = 15
					rawoutput("<td id=\"".$x."-".$y."\" onclick=\"changeColor(this.id);\" class=\"{$colors[$cellvalue]}\" align=center valign=middle alt='({$x}, {$y}) $terrainName' title='({$x}, {$y}) $terrainName'><input type=\"hidden\" id=\"".$x."-".$y."b\" name=\"".$x.".".$y."\" value=\"{$cellvalue}\">",true);
					foreach($vloc as $loc=>$val) {
						if ($y == get_module_setting($loc."Y") && $x == get_module_setting($loc."X")) {
							rawoutput("<i class='fa fa-home fa-fw' data-uk-tooltip title='$loc ({$x}, {$y}) $terrainName'></i>");
							break;
						}
					}
				}
				rawoutput("</tr>");
			}
			
			rawoutput("<tr>");
			output_notl("<td>`6`bY/X`b`0</td>",true);
			for ($x = 1; $x <= $sizeX; $x++){
				rawoutput("<td>{$x}</td>",true);
			}
			rawoutput("</tr>");
			rawoutput("</table>");
			output_notl("`n<input type='submit' value=\"".translate_inline("Save Terrain")."\">`c",true);
			rawoutput("</form>");
			addnav("","runmodule.php?module=worldmapen&op=edit&subop=terrain&act=save");
		}
		// -----------------------------------------------------------------------
		// END - Display the advanced map with terrain editor
		// -----------------------------------------------------------------------
		worldmapen_viewmapkey(true,true);
	}
}
// -----------------------------------------------------------------------
// END - worldmapen_viewmap allows players to view the world map if they
//         have purchased one from the Gypsy or Item Shop
// -----------------------------------------------------------------------


function worldmapen_viewsmallmap(){
	$colorUserLoc = get_module_setting("colorUserLoc");
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname] = "village";
	$vloc = modulehook("validlocation", $vloc);
	$loc = get_module_pref("worldXYZ");
	list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);
	$sizeX = get_module_setting("worldmapsizeX");
	$sizeY = get_module_setting("worldmapsizeY");
	$viewRadius = get_module_setting("viewRadius");
	$smallmapsize = (2 * $viewRadius) + 1;
	$rowspanY = $sizeY + 1;
	$smallmapY = $worldmapY + floor($smallmapsize / 2);
	$i=0;
	
	
	rawoutput("<div class='worldmapen text-center'>");
	rawoutput("<hr>");
	rawoutput("<table><tr>");
	if (get_module_setting("showcompass") == 1){
		rawoutput("<td>");
		worldmapen_showcompass();
		rawoutput("</td>");
	}
	rawoutput("<td><table class='map4'><tr>");

	$map = worldmapen_loadMap();
	$colors = worldmapen_getColorDefinitions();

//	debug("map="); debug($map);
//	debug("colors="); debug($colors);
//	debug("smallmapsize={$smallmapsize}.");

	$blindoutput = "";
	for ($y = $smallmapsize; $y > 0; $y--){
		$smallmapX = ($worldmapX - floor($smallmapsize / 2));
		rawoutput("<tr>");
		for ($x = 0; $x < $smallmapsize; $x++){
			$blind_dir_x = "";
			$blind_dir_y = "";
			$blind_dist_x = "Close ";
			$blind_dist_y = "Close ";
			$blind_sep = ", ";
			if( !isset( $map[$smallmapX][$smallmapY] ) )
			{
				$smallmapX++;
				$i++;
		    continue;
		  }
			$cellvalue = $map[$smallmapX][$smallmapY];
			$terrainName = translate_inline($map[$smallmapX][$smallmapY]);
			
			foreach($vloc as $loc=>$val) {
				if ($smallmapY == get_module_setting($loc."Y") && $smallmapX == get_module_setting($loc."X")) {
					$cityText = "<i class='fa fa-home fa-fw' data-uk-tooltip title='$loc ({$smallmapX}, {$smallmapY}) ($terrainName)'></i>";
					$city = true;
					break;
				}else{
					$cityText = "";
					$city = false;
				}
			}
			if ($i == floor($smallmapsize * $smallmapsize / 2)) {
				rawoutput("<td class='{$colorUserLoc}'>");
				$blindoutput.="Your current location: ";
			} else if ($x < $sizeX){
				if (!$city)
					rawoutput("<td class='{$colors[$cellvalue]}' data-uk-tooltip title='({$smallmapX}, {$smallmapY}) ($terrainName)'>");
				else
					rawoutput("<td class='{$colors[$cellvalue]}'>");
			}
			rawoutput($cityText);
			if ($smallmapY > $worldmapY+1 || $smallmapY < $worldmapY-1) $blind_dist_y = "Far ";
			if ($smallmapX > $worldmapX+1 || $smallmapX < $worldmapX-1) $blind_dist_x = "Far ";
			if ($smallmapX == $worldmapX){
				$blind_dist_x = "";
				$blind_sep="";
			}
			if ($smallmapY == $worldmapY){
				$blind_dist_y = "";
				$blind_sep="";
			}
			if ($smallmapY > $worldmapY) $blind_dir_y = "North";
			if ($smallmapY < $worldmapY) $blind_dir_y = "South";
			if ($smallmapX > $worldmapX) $blind_dir_x = "East";
			if ($smallmapX < $worldmapX) $blind_dir_x = "West";
			if (!$city) 
			{
				// rawoutput("<img src='images/trans.gif' height='18' width='20' alt='({$smallmapX}, {$smallmapY}) {$map[$smallmapX][$smallmapY]}' title='({$smallmapX}, {$smallmapY}) {$map[$smallmapX][$smallmapY]}'>");
				$blindoutput.=$blind_dist_y.$blind_dir_y.$blind_sep.$blind_dist_x.$blind_dir_x." - ".$map[$smallmapX][$smallmapY]."`n";
			}
			else 
			{
				$blindoutput.=$blind_dist_y.$blind_dir_y.$blind_sep.$blind_dist_x.$blind_dir_x." - ".$map[$smallmapX][$smallmapY]." - city of ".$loc."`n";
			}
			rawoutput("</td>");
			$smallmapX++;
			$i++;
		}
		rawoutput("</tr>");
		$smallmapY--;
	}
	rawoutput("</table></td></tr></table>");
	rawoutput("</div>");
	
	
	// if (get_module_pref("user_blindoutput","worldmapen")){
	// 	output_notl("%s",$blindoutput);
	// }
	modulehook("worldmap_belowmap");
	//worldmapen_viewmapkey(true, false);
}


function worldmapen_showcompass() {
	global $nlink, $elink, $wlink, $slink, $nelink, $nwlink, $selink, $swlink;
	addnav( '', $nlink );
	addnav( '', $elink );
	addnav( '', $wlink );
	addnav( '', $slink );
	addnav( '', $nelink );
	addnav( '', $nwlink );
	addnav( '', $selink );
	addnav( '', $swlink );
	$n = translate_inline( 'North' );
	$nw = translate_inline( 'Northwest' );
	$ne = translate_inline( 'Northeast' );
	$s = translate_inline( 'South' );
	$sw = translate_inline( 'Southwest' );
	$se = translate_inline( 'Southeast' );
	$w = translate_inline( 'West' );
	$e = translate_inline( 'East' );
	$compass = translate_inline( 'Compass' );
	// rawoutput("<hr>");
	rawoutput('<IMG SRC="images/compass.png" WIDTH="198" HEIGHT="234" BORDER="0" ALT="'.$compass.'" USEMAP="#compass_Map">', true);
	rawoutput('<MAP NAME="compass_Map">');
	rawoutput('<AREA SHAPE="poly" ALT="'.$nw.'" title="'.$nw.'" COORDS="67,109, 14,53, 31,39, 84,95" HREF="' . $nwlink . '">');
	rawoutput('<AREA SHAPE="poly" ALT="'.$w.'" title="'.$w.'" COORDS="67,138, 0,138, 0,116, 66,115" HREF="' . $wlink . '">');
	rawoutput('<AREA SHAPE="poly" ALT="'.$sw.'" title="'.$sw.'" COORDS="70,139, 85,156, 30,210, 18,201" HREF="' . $swlink . '">');
	rawoutput('<AREA SHAPE="poly" ALT="'.$s.'" title="'.$s.'" COORDS="109,157, 107,234, 89,234, 87,157" HREF="' . $slink . '">');
	rawoutput('<AREA SHAPE="poly" ALT="'.$se.'" title="'.$se.'" COORDS="125,144, 180,201, 167,210, 111,155" HREF="' . $selink . '">');
	rawoutput('<AREA SHAPE="poly" ALT="'.$e.'" title="'.$e.'" COORDS="130,115, 198,118, 198,136, 129,140" HREF="' . $elink . '">');
	rawoutput('<AREA SHAPE="poly" ALT="'.$ne.'" title="'.$ne.'" COORDS="179,51, 167,39, 111,99, 126,112" HREF="' . $nelink . '">');
	rawoutput('<AREA SHAPE="poly" ALT="'.$n.'" title="'.$n.'" COORDS="110,0, 86,0, 87,95, 109,95" HREF="' . $nlink . '">');
	rawoutput('</MAP>');
}
// -----------------------------------------------------------------------
// BEGIN - World map key
// -----------------------------------------------------------------------
function worldmapen_viewmapkey($showloc,$small){
	global $session;
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname] = "village";
	$vloc = modulehook("validlocation", $vloc);
	$cities = translate_inline("Cities");
	$mapkey = translate_inline("MAP KEY");
	$colorUserLoc = get_module_setting("colorUserLoc");
	
	output_notl("`n`n",true);
	rawoutput("<div class='worldmapen legend'>");
	rawoutput("<table class='map6'>");
	rawoutput("<tr>");
	output_notl("<td>`b`6{$mapkey}`0`b</td>",true);
	rawoutput("</tr>");
	rawoutput("</table>");
	
	
	$loc = get_module_pref("worldXYZ");
	list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);

	$terrain = worldmapen_getTerrain($worldmapX,$worldmapY,$worldmapZ);
//	debug($terrain);
	
	$currentTerrain = $terrain["type"];
	$terrainColor   = $terrain["color"];

	if ($showloc){
		rawoutput("<table class='map7'><tr>");
		rawoutput("<td class='terrain {$colorUserLoc}'>&nbsp;</td><td>"); output("Current Location"); rawoutput("</td>");
		rawoutput("</tr><tr>");
		if (get_module_setting("enableTerrains")==1){
			rawoutput("<td class='terrain {$terrainColor}'>&nbsp;</td><td>");
			output("Current Terrain: %s", translate_inline($currentTerrain));
			rawoutput("</td>");
			rawoutput("</tr><tr>");
		}
		rawoutput("</table>");
	}
	
	if (get_module_setting("showcities") == 1){
		output("`n");
		rawoutput("<table class='8' cellpadding=0 cellspacing=5 border=0><tr>");
		output_notl("<td>`b{$cities}`b</td>",true);
		rawoutput("</tr>");
		foreach($vloc as $loc=>$val) {
			rawoutput("<tr><td>"); output_notl(substr($loc,0,3)); rawoutput("</td>");
			rawoutput("<td>= "); output_notl($loc); rawoutput("</td></tr>");
		}
		rawoutput("</table>");
	}
	
	if (get_module_setting("enableTerrains") ==1){
		rawoutput("<hr>");
		rawoutput("<table class='map9'>");
		rawoutput("<tr>");
		rawoutput("<td>",true); output("Terrains"); rawoutput("</td>",true);
		rawoutput("</tr><tr>");
		output_notl("<td>(" . translate_inline("Terrain Color") .", ". translate_inline("Terrain Type") .", ". translate_inline("Terrain Movement Cost") .")</td>",true);
		rawoutput("</tr><tr><br>");
		rawoutput("<table class='map10'><tr>");
		
		$terrainDef = worldmapen_loadTerrainDefs();
//		debug($terrainDef);
		
		$i = 0;
		foreach ($terrainDef AS $name=>$terrain) {
			if ($i % 6 == 0) {
				rawoutput("</tr><tr>");
	   		}
			//Blame CMJ for this mess
			if (get_module_setting("usestamina")==1){
				require_once('modules/staminasystem/lib/lib.php');
				switch ($terrain['type']){
					case "Plains":
						$terrain["moveCost"] = stamina_getdisplaycost("Travelling - Plains")."%";
						break;
					case "Forest":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - Forest")."%";
						break;
					case "River":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - River")."%";
						break;
					case "Ocean":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - Ocean")."%";
						break;
					case "Earth":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - Earth")."%";
						break;
					case "Desert":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - Desert")."%";
						break;
					case "Swamp":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - Swamp")."%";
						break;
					case "Mountains":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - Mountains")."%";
						break;
					case "Snow":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - Snow")."%";
						break;
					case "Air":
						$terrain['moveCost'] = stamina_getdisplaycost("Travelling - Air")."%";
						break;
				}
			}
			$terrainName = translate_inline($name);
			rawoutput("<td class='terrain {$terrain['color']}'>&nbsp;</td><td>{$terrainName} - ".$terrain["moveCost"]."</td>");
			
			$i++;
		}
		rawoutput("<td bgcolor='#111111' height='5' width='10'>&nbsp;</td><td>".translate_inline("Map Edge")."</td>");
		rawoutput("</tr></table>");
	}
	rawoutput("</div>");
}
// -----------------------------------------------------------------------
// END - World map key
// -----------------------------------------------------------------------
// -----------------------------------------------------------------------
// BEGIN - worldmapen_determinenav determines in which direction a player
//         may move in the world.  North, East, South, West
// -----------------------------------------------------------------------
function worldmapen_determinenav(){
	global $session, $nlink, $elink, $wlink, $slink, $nelink, $nwlink, $selink, $swlink;
	$minX = 1;
	$minY = 1;
	$maxX = get_module_setting("worldmapsizeX");
	$maxY = get_module_setting("worldmapsizeY");
	
	if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
		addnav("X?`bSuperuser Grotto`b","superuser.php");
	}

	$campingAllowed = 1;
	$loc = get_module_pref('worldXYZ');
	$oloc = $loc;
	list($x, $y, $z) = explode(",", $loc);
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname] = "village";
	$vloc = modulehook("validlocation", $vloc);
	foreach($vloc as $loc=>$val) {
		$cx = get_module_setting($loc.'X');
		$cy = get_module_setting($loc.'Y');
		$cz = get_module_setting($loc.'Z');
		if ($x == $cx && $y == $cy && $z == $cz) {
			$session['user']['location']=$loc;
			set_module_pref("lastCity", "");
			addnav("Outpost Area");
			addnav(array("O?Enter %s", $loc), "village.php");
			//Al bosque se entra desde la ciudad
			// addnav("Enter Forest","forest.php");
			$campingAllowed = 0;
		}
	}
	$args = modulehook("count-travels", array('available'=>0, 'used'=>0));
	$free = max(0, $args['available'] - $args['used']);
	if (get_module_setting("usestamina")==1){
		$free = 100;
	}
	if ($free != 0 or $free < 0) {
		addnav("You can go");
		$plusX=$x+1;
		$plusY=$y+1;
		$minusX=$x-1;
		$minusY=$y-1;
		$checkN = $plusY <= $maxY;
		$checkE = $plusX <= $maxX;
		$checkS = $minusY >= $minY;
		$checkW = $minusX >= $minX;
		$noTravel = 0; // because they'll never get there and 0 will make sure boundary message is triggered
		// Might be a better way of getting the terrain movement cost for the adjacent squares
		$NterrainCost = $checkN ? worldmapen_terrain_cost($x,$plusY,$z) : $noTravel;
		$NEterrainCost = ($checkN && $checkE) ? worldmapen_terrain_cost($plusX,$plusY,$z) : $noTravel;
		$NWterrainCost = ($checkN && $checkW) ? worldmapen_terrain_cost($minusX,$plusY,$z) : $noTravel;
		$EterrainCost = $checkE ? worldmapen_terrain_cost($plusX,$y,$z) : $noTravel;
		$SterrainCost = $checkS ? worldmapen_terrain_cost($x,$minusY,$z) : $noTravel;
		$SEterrainCost = ($checkS && $checkE) ? worldmapen_terrain_cost($plusX,$minusY,$z) : $noTravel;
		$SWterrainCost = ($checkS && $checkW) ? worldmapen_terrain_cost($minusX,$minusY,$z) : $noTravel;
		$WterrainCost = $checkW ? worldmapen_terrain_cost($minusX,$y,$z) : $noTravel;
		if ($y + 1 <= $maxY && $NterrainCost <=  $free){
			$nlink = "runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=north";
			addnav(array("T?North (`Q%s%%`0)", $NterrainCost),$nlink);
		}elseif ($NterrainCost >  $free){
			output("Can't move North`n");
			$nlink = "#";
		}else{
			$nlink = "#";
			output_notl("`c`n`!%s`0`n`c",get_module_setting("nBoundary"));
		}
		if ($x + 1 <= $maxX && $EterrainCost <=  $free){
			$elink = "runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=east";
			addnav(array("H?East (`Q%s%%`0)", $EterrainCost),$elink);
		}elseif ($EterrainCost >  $free){
			output("Can't move East`n");
			$elink = "#";
		}else{
			$elink = "#";
			output_notl("`c`n`!%s`0`c`n",get_module_setting("eBoundary"));
		}
		if ($y - 1 >= $minY && $SterrainCost <=  $free){
			$slink = "runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=south";
			addnav(array("B?South (`Q%s%%`0)", $SterrainCost),$slink);
		}elseif ($SterrainCost >  $free){
			output("Can't move South`n");
			$slink = "#";
		}else{
			$slink = "#";
			output_notl("`c`n`!%s`0`c`n",get_module_setting("sBoundary"));
		}
		if ($x - 1 >= $minX && $WterrainCost <=  $free){
			$wlink = "runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=west";
			addnav(array("F?West (`Q%s%%`0)", $WterrainCost),$wlink);
		}elseif ($WterrainCost >  $free){
			output("Can't move West`n");
			$wlink = "#";
		}else{
			$wlink = "#";
			output_notl("`n`c`!%s`0`n`c",get_module_setting("wBoundary"));
		}
		if (get_module_setting("compasspoints") == "1"){
			if ($y + 1 <= $maxY && $x + 1 <= $maxX && $NEterrainCost <=  $free){
				$nelink = "runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=northeast";
				addnav(array("Y?North-East (`Q%s%%`0)", $NEterrainCost),$nelink);
			}elseif ($NEterrainCost > $free){
				output("Can't move Northeast`n");
				$nelink = "#";
			} else {
				$nelink = "#";
			}
			if ($y + 1 <= $maxY && $x - 1 >= $minX && $NWterrainCost <= $free){
				$nwlink = "runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=northwest";
				addnav(array("R?North-West (`Q%s%%`0)", $NWterrainCost),$nwlink);
			}elseif ($NWterrainCost > $free){
				output("Can't move Northwest`n");
				$nwlink = "#";
			} else {
				$nwlink = "#";
			}
			if ($y - 1 >= $minY && $x + 1 <= $maxX && $SEterrainCost <=  $free){
				$selink = "runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=southeast";
				addnav(array("N?South-East (`Q%s%%`0)", $SEterrainCost),$selink);
			}elseif ($SEterrainCost > $free){
				output("Can't move Southeast`n");
				$selink = "#";
			} else {
				$selink = "#";
			}
			if ($y - 1 >= $minY && $x - 1 >= $minX && $SWterrainCost <=  $free){
				$swlink = "runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=southwest";
				addnav(array("V?South-West (`Q%s%%`0)", $SWterrainCost),$swlink);
			}elseif ($SWterrainCost > $free){
				output("Can't move Southwest`n");
				$swlink = "#";
			} else {
				$swlink = "#";
			}
		}
	}
	else
	{
		output( 'You are too tired to go anywhere.`n' );
		$nlink = $elink = $wlink = $slink = $nelink = $nwlink = $selink = $swlink = '#';
	}
	if (get_module_setting("turntravel")!=0){
		addnav("Prolonged Travel");
		if ($session['user']['turns']>0 && get_module_setting("turntravel")!=0){
			addnav("Trade a Turn for Travel Points","runmodule.php?module=worldmapen&op=tradeturn");
		}
	}
	if ($session['user']['superuser'] & SU_EDIT_USERS){
		addnav("Superuser");
		foreach($vloc as $loc=>$val) {
			if ($loc == $session['user']['location']) continue;
			addnav(array("Go to %s", $loc), "runmodule.php?module=worldmapen&op=destination&cname=".htmlentities($loc));
		}
	}
	if ($session['user']['superuser'] & SU_EDIT_USERS) {
		if (get_module_setting("manualmove") == 1){
			addnav("--");
			addnav("Superuser");
			if ($y + 1 <= $maxY){
				addnav("Safe North",
				"runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=north&su=1");
			}
			if ($x + 1 <= $maxX){
				addnav("Safe East",
				"runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=east&su=1");
			}
			if ($y - 1 >= $minY){
				addnav("Safe South",
				"runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=south&su=1");
			}
			if ($x - 1 >= $minX){
				addnav("Safe West",
				"runmodule.php?module=worldmapen&op=move&oloc=".rawurlencode ( $oloc )."&dir=west&su=1");
			}
		}
	}
	if ($session['user']['superuser'] & SU_INFINITE_DAYS) {
		addnav("Superuser");
		addnav("/?New Day", "newday.php");
	}
	if (get_module_pref("worldmapbuy") == 1 || ($session['user']['superuser'] & SU_EDIT_USERS)){
		addnav("Map");
		addnav("M?World Map","runmodule.php?module=worldmapen&op=viewmap");
	}
	if ($campingAllowed){
		worldmapen_camp_list();
	}
	//cmj edit: worldnav now passes user location in args, to save getting the modulepref again in modules that query the user's location
	$hook = array(
		"x"=>$x,
		"y"=>$y,
		"z"=>$z
	);
	$hook = modulehook("worldnav",$hook);
	// addnav("Inventory");
	// addnav("View your Inventory","inventory.php?items_context=worldmap");
}
// -----------------------------------------------------------------------
// END - worldmapen_determinenav determines in which direction a player
//         may move in the world.  North, East, South, West
// -----------------------------------------------------------------------
// -----------------------------------------------------------------------
// BEGIN - World Map camping routine
// -----------------------------------------------------------------------
function worldmapen_camp_list(){
	global $session, $pvptime, $pvptimeout;
	addnav("Quit");
	addnav("Set up camp","runmodule.php?module=worldmapen&op=camp");
	if (getsetting("pvp",1) == 0) return;
	$loc = get_module_pref("worldXYZ");
	$lev1 = $session['user']['level']-1;
	$lev2 = $session['user']['level']+2;
	$days = getsetting("pvpimmunity", 5);
	$exp = getsetting("pvpminexp", 1500);
	$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$id = $session['user']['acctid'];
	$location = addslashes($session['user']['location']);
	$sql = "SELECT 
		a.acctid, a.clanid, a.name, a.alive, a.sex, a.level, a.laston, a.loggedin, a.login, a.pvpflag, b.value as location, c.clanshort, a.clanrank 
	FROM 
		".db_prefix("accounts")." AS a  
	LEFT JOIN ".db_prefix("clans")." AS c ON c.clanid=a.clanid 
	LEFT JOIN ".db_prefix("module_userprefs")." AS b ON a.acctid=b.userid  
	
	WHERE 
		b.value='$loc' 
		AND (locked=0) AND (slaydragon=0) 
		AND (age>$days OR dragonkills>0 OR pk>0 OR experience>$exp) 
		AND (level>=$lev1 AND level<=$lev2) AND (alive=1) 
		AND (laston<'$last' OR loggedin=0) AND (acctid<>$id) 
		AND a.location='$location' 
	ORDER BY level DESC, experience DESC, dragonkills DESC";
	//PvP Display
	$_SERVER['REQUEST_URI'] = preg_replace( '/op=[a-z]*/', 'op=continue', $_SERVER['REQUEST_URI'] );
	// ^- That's a hack to prevent stop cheaters from clicking BIO and back to get gold, turns, etc.
	require_once("lib/pvplist.php");
	output('`n`c');
	pvplist($loc,"runmodule.php?module=worldmapen", "&op=combat&pvp=1", $sql);
	output('`c');
}
// -----------------------------------------------------------------------
// END - World Map camping routine
// -----------------------------------------------------------------------

function worldmapen_terrain_cost($x, $y, $z=1) {
	global $session;
	$terrain = worldmapen_getTerrain($x, $y, $z);
	//Little bit to interact with Expanded Stamina System, added by Caveman Joe
	if (get_module_setting("usestamina") == 1){
		require_once('modules/staminasystem/lib/lib.php');
		switch ($terrain['type']){
			case "Plains":
				return stamina_getdisplaycost("Travelling - Plains",2);
				break;
			case "Forest":
				return stamina_getdisplaycost("Travelling - Forest",2);
				break;
			case "River":
				return stamina_getdisplaycost("Travelling - River",2);
				break;
			case "Ocean":
				return stamina_getdisplaycost("Travelling - Ocean",2);
				break;
			case "Earth":
				return stamina_getdisplaycost("Travelling - Earth",2);
				break;
			case "Air":
				return stamina_getdisplaycost("Travelling - Air",2);
				break;
			case "Desert":
				return stamina_getdisplaycost("Travelling - Desert",2);
				break;
			case "Swamp":
				return stamina_getdisplaycost("Travelling - Swamp",2);
				break;
			case "Mountains":
				return stamina_getdisplaycost("Travelling - Mountains",2);
				break;
			case "Snow":
				return stamina_getdisplaycost("Travelling - Snow",2);
				break;
		}
	}
		return $terrain["moveCost"];
}

//Function to interact with Expanded Stamina System, added by Caveman Joe
function worldmapen_terrain_takestamina($x, $y, $z=1) {
	global $session;
	$terrain = worldmapen_getTerrain($x, $y, $z);
	require_once('modules/staminasystem/lib/lib.php');
	switch ($terrain['type']){
		case "Plains":
			$plains = process_action("Travelling - Plains");
			break;
		case "Forest":
			$forest = process_action("Travelling - Forest");
			break;
		case "River":
			$river = process_action("Travelling - River");
			break;
		case "Ocean":
			$ocean = process_action("Travelling - Ocean");
			break;
		case "Earth":
			$earth = process_action("Travelling - Earth");
			break;
		case "Air":
			$air = process_action("Travelling - Air");
			break;
		case "Desert":
			$desert = process_action("Travelling - Desert");
			break;
		case "Swamp":
			$swamp = process_action("Travelling - Swamp");
			break;
		case "Mountains":
			$mount = process_action("Travelling - Mountains");
			break;
		case "Snow":
			$snow = process_action("Travelling - Snow");
			break;
	}
	if ($plains['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across plains!  You are now level %s!  This action will cost fewer Stamina points now, so you can saunter across more fields per day.`b`c`n",$plains['lvlinfo']['newlvl']);
	}
	if ($forest['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across dense jungle!  You are now level %s!  This action will cost fewer Stamina points now, so you can navigate more thick jungle terrain per day.`b`c`n",$forest['lvlinfo']['newlvl']);
	}
	if ($river['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across rivers!  You are now level %s!  This action will cost fewer Stamina points now, so you can wade through more rivers in a single day.`b`c`n",$river['lvlinfo']['newlvl']);
	}
	if ($ocean['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across deep water!  You are now level %s!  This action will cost fewer Stamina points now, so you can swim more in a single day.`b`c`n",$ocean['lvlinfo']['newlvl']);
	}
	if ($earth['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across earth!  You are now level %s!  This action will cost fewer Stamina points now, so you can saunter across more beachy terrain in a single day.`b`c`n",$earth['lvlinfo']['newlvl']);
	}
	if ($desert['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across deserts!  You are now level %s!  This action will cost fewer Stamina points now, so you can saunter across more beachy terrain in a single day.`b`c`n",$desert['lvlinfo']['newlvl']);
	}
	if ($swamp['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across swamps!  You are now level %s!  This action will cost fewer Stamina points now, so you can wade through more swampy goo every day.`b`c`n",$swamp['lvlinfo']['newlvl']);
	}
	if ($mount['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across mountains!  You are now level %s!  This action will cost fewer Stamina points now, so you can scale more mountains in a single day.`b`c`n",$mount['lvlinfo']['newlvl']);
	}
	if ($snow['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across snow!  You are now level %s!  This action will cost fewer Stamina points now, so you can stomp through more snow in a single day.`b`c`n",$snow['lvlinfo']['newlvl']);
	}
	if ($air['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Travel across air!  You are now level %s!  This action will cost fewer Stamina points now, so you can fly through for air in a single day.`b`c`n",$air['lvlinfo']['newlvl']);
	}
}

function worldmapen_encounter($x, $y, $z=1) {
	global $session;
	$terrain = worldmapen_getTerrain($x, $y, $z);
	$id = $session['user']['hashorse'];
	// debug("Worldmap lib.php is debugging base terrain encounter rate");
	// debug($terrain['encounter']);
	if ($id!=0){
		switch ($terrain['type']){
			case "Plains":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterPlains");
				break;
			case "Forest":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterForest");
				break;
			case "River":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterRiver");
				break;
			case "Ocean":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterOcean");
				break;
			case "Desert":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterDesert");
				break;
			case "Swamp":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterSwamp");
				break;
			case "Mountains":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterMountains");
				break;
			case "Snow":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterSnow");
				break;
			case "Earth":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterEarth");
				break;
			case "Air":
				$terrain['encounter']=$terrain['encounter']*get_module_objpref("mounts",$id,"encounterAir");
				break;
		}
	}
	//Interaction with Stamina system - increases encounter rate by 1% for every percentage point of player's Amber stamina used
	if (get_module_setting("usestamina") == 1){
		require_once('modules/staminasystem/lib/lib.php');
		$amber = get_stamina();
		if ($amber < 100){
			output("`4You are getting tired`0.  Monsters have a tendency to swarm towards contestants who look like they're half-asleep.  You might want to consider setting up camp, or doing something to raise your Stamina.`n");
		}
		$add = 100 - $amber;
		$terrain['encounter'] += $add;
	}
	return $terrain['encounter'];
}

function worldmapen_getColorDefinitions() {
	$def = worldmapen_loadTerrainDefs();
	
	$retValue = array();

	foreach ($def as $key=>$value) {
		$retValue[$key] = $value["color"];
	}

	return $retValue;
}

function worldmapen_getTerrain($x, $y, $z) {
	global $worldmapen_globals;

	worldmapen_loadTerrainDefs();
	worldmapen_loadMap();

	$terrainType = $worldmapen_globals["map"][$z][$x][$y];
	return $worldmapen_globals["terrainDefs"][$terrainType];
}

function worldmapen_setTerrain($x, $y, $z=1, $type="Forest") {
	global $worldmapen_globals;

	worldmapen_loadTerrainDefs();

//	debug("x={$x}, y={$y}, z={$z}, type={$type}");

	if (is_numeric($type)) {
		switch ($type) {
			case 0: 	$type = "Plains"; break;
			case 1: 	$type = "Forest"; break;
			case 2: 	$type = "River";  break;
			case 3: 	$type = "Ocean";  break;
			case 4: 	$type = "Desert"; break;
			case 5: 	$type = "Swamp";  break;
			case 6: 	$type = "Mountains";  break;
			case 7: 	$type = "Snow";   break;
			
			case 8:		$type = "Earth";  break;
			case 9:		$type = "Air";	  break;
	
			default:
				debug("Invalid terrain type '{$type}'. Setting to 'Forest'.");
				$type = "Forest";
				break;
		}
	}

	if ($worldmapen_globals["map"][$z][$x][$y] != $type) {
		debug("Changing type of ({$x}, {$y}) from '{$worldmapen_globals["map"][$z][$x][$y]}' to '{$type}'.");
		
		$worldmapen_globals["map"][$z][$x][$y] = $type;
	}
}

function worldmapen_loadTerrainDefs() {
	global $worldmapen_globals;

	if ( ( !isset( $worldmapen_globals["terrainDefs"] ) ) || is_null($worldmapen_globals["terrainDefs"])) {
		$worldmapen_globals["terrainDefs"] = array(
			"Plains" => array("type" => "Plains", "color" => get_module_setting("colorPlains"), "moveCost" => get_module_setting("moveCostPlains"), "encounter" => get_module_setting("encounterPlains")),
			"Forest" => array("type" => "Forest", "color" => get_module_setting("colorForest"), "moveCost" => get_module_setting("moveCostForest"), "encounter" => get_module_setting("encounterForest")),
			"River"  => array("type" => "River",  "color" => get_module_setting("colorRiver"),  "moveCost" => get_module_setting("moveCostRiver"),  "encounter" => get_module_setting("encounterRiver")),
			"Ocean"  => array("type" => "Ocean",  "color" => get_module_setting("colorOcean"),  "moveCost" => get_module_setting("moveCostOcean"),  "encounter" => get_module_setting("encounterOcean")),
			"Desert" => array("type" => "Desert", "color" => get_module_setting("colorDesert"), "moveCost" => get_module_setting("moveCostDesert"), "encounter" => get_module_setting("encounterDesert")),
			"Swamp"  => array("type" => "Swamp",  "color" => get_module_setting("colorSwamp"),  "moveCost" => get_module_setting("moveCostSwamp"),  "encounter" => get_module_setting("encounterSwamp")),
			"Mountains"  => array("type" => "Mountains",  "color" => get_module_setting("colorMountains"),  "moveCost" => get_module_setting("moveCostMountains"),  "encounter" => get_module_setting("encounterMountains")),
			"Snow"   => array("type" => "Snow",   "color" => get_module_setting("colorSnow"),   "moveCost" => get_module_setting("moveCostSnow"),   "encounter" => get_module_setting("encounterSnow")),
			"Earth"	 => array("type" => "Earth",  "color" => get_module_setting("colorEarth"),  "moveCost" => get_module_setting("moveCostEarth"),  "encounter" => get_module_setting("encounterEarth")),
			"Air"    => array("type" => "Air",    "color" => get_module_setting("colorAir"),    "moveCost" => get_module_setting("moveCostAir"),    "encounter" => get_module_setting("encounterAir")),
		);
	}

	//	debug($worldmapen_globals["terrainDefs"]);
	return $worldmapen_globals["terrainDefs"];
}

function worldmapen_loadMap($z = 1) {
	global $worldmapen_globals;

	if ( ( !isset( $worldmapen_globals["map"] ) ) || is_null($worldmapen_globals["map"])) {
//		debug("Loading worldmap from database");
		$map = get_module_setting("TerrainDefinition","worldmapen");
//		debug("map={$map}");
		
		if (! is_null($map)) {
			$worldmapen_globals["map"] = unserialize($map);
		} else {
			$worldmapen_globals["map"] = worldmapen_generateNewMap("Forest");
			worldmapen_saveMap();
		}
	}

//	debug("Got worldmap:");
//	debug($worldmapen_globals["map"]);
	
	return $worldmapen_globals["map"][$z];
}

function worldmapen_saveMap() {
	global $worldmapen_globals;

	if (is_null($worldmapen_globals["map"])) {
		debug("Sorry, no map defined until now. Can't save a nonexisting map. Will generate a new world.");

		$worldmapen_globals["map"] = worldmapen_generateNewMap();
	}

	set_module_setting("TerrainDefinition", serialize($worldmapen_globals["map"]));
}

function worldmapen_generateNewMap($defaultTerrain = "Forest") {
	global $worldmapen_globals;
	$retValue = array();

	worldmapen_loadTerrainDefs();

	if (!array_key_exists($defaultTerrain, $worldmapen_globals["terrainDefs"])) {
		debug("Invalid terrain type '{$defaultTerrain}'. Using 'Forest' instead.");
		$defaultTerrain = "Forest";
	}

	// Level 0 will be "Earth", level 2 will be "Air" until otherwise defined.

	$maxX = get_module_setting("worldmapsizeX");
	$maxY = get_module_setting("worldmapsizeY");

	for ($x = 1; $x <= $maxX; $x++) {
		for ($y = 1; $y <= $maxY; $y++) {
			$retValue[0][$x][$y] = "Earth";
			$retValue[1][$x][$y] = $defaultTerrain;
			$retValue[2][$x][$y] = "Air";
		}
	}

	debug("Map with size {$maxX} x {$maxY} generated with default '{$defaultTerrain}'.");
	return $retValue;
}
// -----------------
// END - FUNCTIONS
// -----------------
?>