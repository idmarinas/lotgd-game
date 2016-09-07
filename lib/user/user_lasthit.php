<?php
$output="";
$sql = "SELECT output FROM " . DB::prefix("accounts_output") . " WHERE acctid='$userid'";
$result = DB::query($sql);
$row = DB::fetch_assoc($result);
if ($row['output']=='') {
	require_once("lib/output.php");
	$output=new output_collector();
	$text=$output->appoencode("`\$This user has had his navs fixed OR has an empty page stored. Nothing can be displayed to you -_-`0");
	$output="<html><head><link href=\"templates/common/colors.css\" rel=\"stylesheet\" type=\"text/css\"></head><body style='background-color: #000000'>$text</body></html>";
} else {
	$output=gzuncompress($row['output']);
}
echo str_replace(".focus();",".blur();",str_replace("<iframe src=","<iframe Xsrc=",$output));
exit(0);
?>
