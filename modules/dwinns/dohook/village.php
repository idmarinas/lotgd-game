<?
	$location = $session['user']['location'];
	$dwellings = db_prefix("dwellings");
	$dwinns = db_prefix("dwinns");
	$sql = "SELECT $dwellings.name AS name, $dwellings.dwid AS dwid FROM $dwellings INNER JOIN $dwinns ON $dwellings.dwid = $dwinns.dwid WHERE $dwellings.location ='$location' AND $dwinns.villageadd>0 AND $dwinns.closed=0";
	$result = db_query($sql);
	tlschema($args['schemas']['tavernnav']);
	addnav($args['tavernnav']);
	tlschema();
	while ($row = db_fetch_assoc($result)) {
		addnav(array("%s", $row['name']), "runmodule.php?module=dwellings&op=enter&dwid=".$row['dwid']);
	}
?>
