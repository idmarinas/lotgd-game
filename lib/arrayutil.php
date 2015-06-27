<?php
// translator ready
// addnews ready
// mail ready
function createstring($array){
	if (is_array($array)){
		$out = serialize($array);
	}
	return $out;
}

?>
