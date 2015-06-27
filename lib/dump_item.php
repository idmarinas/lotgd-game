<?php
// addnews ready
// translator ready
// mail ready
function dump_item($item){
	$out = "";
	if (is_array($item)) $temp = $item;
	else $temp = @unserialize($item);
	if (is_array($temp)) {
		$out .= "array(" . count($temp) . ") {<div style='padding-left: 20pt;'>";
		foreach ($temp as $key=>$val) {
			$out .= "'$key' = '" . dump_item($val) . "'`n";
		}
		$out .= "</div>}";
	} else {
		$out .= $item;
	}
	return $out;
}

function dump_item_ascode($item,$indent="\t"){
	$out = "";
	if (is_array($item)) $temp = $item;
	else $temp = @unserialize($item);
	if (is_array($temp)) {
		$out .= "array(\n$indent";
		$row = array();
		foreach ($temp as $key=>$val) {
			array_push($row,"'$key'=&gt;" . dump_item_ascode($val,$indent."\t"));
		}
		if (strlen(join(", ",$row)) > 80){
		 	$out .= join(",\n$indent",$row);
		}else{
		 	$out .= join(", ",$row);
		}
		$out .= "\n$indent)";
	} else {
		$out .= "'".htmlentities(addslashes($item), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'";
	}
	return $out;
}

?>
