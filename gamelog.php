<?php
// translator ready
// addnews ready
// mail ready

// Written by Christian Rutsch

require_once("common.php");
require_once("lib/http.php");

check_su_access(SU_EDIT_CONFIG);

tlschema("gamelog");

page_header("Game Log");
addnav("Navigation");
require_once("lib/superusernav.php");
superusernav();

$step= 500; // hardcoded stepping
$category = httpget('cat');
$start = (int)httpget('start'); //starting
$sortorder = (int) httpget('sortorder'); // 0 = DESC 1= ASC
$sortby = httpget('sortby');
if ($category > "") {
	$cat = "&cat=$category";
	$sqlcat = "AND ".db_prefix("gamelog").".category = '$category'";
} else {
	$cat='';
	$sqlcat='';
}

$asc_desc=($sortorder==0?"DESC":"ASC");

if ($sortby!='') {
	$sqlsort=" ORDER BY ".$sortby." ".$asc_desc;
}

$sql = "SELECT count(logid) AS c FROM ".db_prefix("gamelog")." WHERE 1 $sqlcat";
$result = db_query($sql);
$row = db_fetch_assoc($result);
$max = $row['c'];


$sql = "SELECT ".db_prefix("gamelog").".*, ".db_prefix("accounts").".name AS name FROM ".db_prefix("gamelog")." LEFT JOIN ".db_prefix("accounts")." ON ".db_prefix("gamelog").".who = ".db_prefix("accounts").".acctid WHERE 1 $sqlcat $sqlsort LIMIT $start,$step";
$next = $start+$step;
$prev = $start-$step;
addnav("Operations");
addnav("Refresh", "gamelog.php?start=$start$cat&sortorder=$sortorder&sortby=$sortby");
if ($category > "") addnav("View all", "gamelog.php");
addnav("Game Log");
if ($next < $max) {
	addnav("Next page","gamelog.php?start=$next$cat&sortorder=$sortorder&sortby=$sortby");
}
if ($start > 0) {
	addnav("Previous page", "gamelog.php?start=$prev$cat&sortorder=$sortorder&sortby=$sortby");
}
$result = db_query($sql);
$odate = "";
$categories = array();

$i=0;
while ($row = db_fetch_assoc($result)) {
	$dom = date("D, M d",strtotime($row['date']));
	if ($odate != $dom){
		output_notl("`n`b`@%s`0`b`n", $dom);
		$odate = $dom;
	}
	$time = date("H:i:s", strtotime($row['date']))." (".reltime(strtotime($row['date'])).")";
	if ($row['name']!='') {
		output_notl("`7(`\$%s`7) %s `7(`&%s`7) (`v%s`7)", $row['category'], $row['message'], $row['name'],$time);
	} else {
		output_notl("`7(`\$%s`7) %s `7(`v%s`7)", $row['category'], $row['message'],$time);

	}
	if (!isset($categories[$row['category']]) && $category == "") {
		addnav("Operations");
		addnav(array("View by `i%s`i", $row['category']), "gamelog.php?cat=".$row['category']);
		$categories[$row['category']] = 1;
	}
	output_notl("`n");
}
addnav("Sorting");
addnav("Sort by date ascending","gamelog.php?start=$start$cat&sortorder=1&sortby=date");
addnav("Sort by date descending","gamelog.php?start=$start$cat&sortorder=0&sortby=date");
addnav("Sort by category ascending","gamelog.php?start=$start$cat&sortorder=1&sortby=category");
addnav("Sort by category descending","gamelog.php?start=$start$cat&sortorder=0&sortby=category");

page_footer();

?>
