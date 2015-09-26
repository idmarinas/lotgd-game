<?
output
(
	"A young man wearing a feather in his hat comes to you and asks you where you would like him to announce your inprisonment. 
	He will do his best alert the people in that village."
);
$vloc = array();
$vname = getsetting("villagename", LOCATION_FIELDS);
$vloc[$vname] = "village";
$vloc = modulehook("validlocation", $vloc);
ksort($vloc);
reset($vloc);
foreach($vloc as $loc=>$val)
{
	addnav(array("%s", $loc), "runmodule.php?module=jail&op=towncry2&area=".htmlentities($val)."");
}
?>