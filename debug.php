<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/dhms.php");
require_once("lib/http.php");

tlschema("debug");

check_su_access(SU_EDIT_CONFIG);

require_once("lib/superusernav.php");
superusernav();
addnav("Debug Options");
addnav("",$_SERVER['REQUEST_URI']);
$sort = httpget('sort');
addnav("Get Pageruntimes","debug.php?debug=pageruntime&sort=".URLEncode($sort));
addnav("Get Modulehooktimes","debug.php?debug=hooksort&sort=".URLEncode($sort));


page_header("Debug Analysis");
$order = "sum";
if ($sort!="") $order=$sort;
$debug=httpget('debug');
if ($debug=='') $debug='pageruntime';
$ascdesc_raw=(int)httpget('direction');
if ($ascdesc_raw) $ascdesc="ASC";
	else $ascdesc="DESC";
addnav("Sorting");
addnav("By Total","debug.php?debug=".$debug."&sort=sum&direction=".$ascdesc_raw);
addnav("By Average","debug.php?debug=".$debug."&sort=medium&direction=".$ascdesc_raw);
addnav("Switch ASC/DESC","debug.php?debug=".$debug."&sort=".URLEncode($sort)."&direction=".(!$ascdesc_raw));


switch ($debug) {

	case "hooksort":
		$sql = "Select category,subcategory, value+0 as sum, (value+0)/count(id) as medium,count(id) AS counter FROM ".db_prefix('debug')." WHERE type='hooktime' group by type,category,subcategory order by $order $ascdesc limit 30";
		$category=translate_inline("Setting");
		$subcategory=translate_inline("Module Name");
		$sum_desc=translate_inline("Total Seconds");
		$med_desc=translate_inline("Average per Hit");
		$hits=translate_inline("Hits");
		break;

	case "pageruntime":

	default:
	$sql = "Select category,subcategory, value+0 as sum, (value+0)/count(id) as medium,count(id) AS counter FROM ".db_prefix('debug')." WHERE type='pagegentime' group by type,category,subcategory order by $order $ascdesc limit 30";
	$category=translate_inline("Setting");
	$subcategory=translate_inline("Module Name");
	$sum_desc=translate_inline("Total Seconds");
	$med_desc=translate_inline("Average per Hit");
	$hits=translate_inline("Hits");
}
$none = translate_inline("`iNone`i");
$notset = translate_inline("`iNot set`i");
rawoutput("<table border=0 cellpadding=2 cellspacing=1><tr class='trhead'><td>$category</td><td>$subcategory</td><td>$sum_desc</td><td>$med_desc</td><td>$hits</td></tr>");
debug($sql);
$result = db_query($sql);
$i=true;
while ($row = db_fetch_assoc($result)) {
	$i=!$i;
	rawoutput("<tr class='".($i?'trdark':'trlight')."'><td valign='top'>");
	output_notl("`b".$row['category']."`b");
	rawoutput("</td><td valign='top'>");
	output_notl("`b".$row['subcategory']."`b");
	rawoutput("</td><td valign='top'>");
	output_notl($row['sum']);
	rawoutput("</td><td valign='top'>");
	output_notl($row['medium']);
	rawoutput("</td><td valign='top'>");
	output_notl($row['counter']);
	rawoutput("</td></tr>");
}
rawoutput("</table>");
page_footer();
?>
