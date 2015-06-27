<?php
function clan_nextrank($ranks,$current) {
	$temp=array_pop($ranks);
	$ranks=array_keys($ranks);
	while (count($ranks)>0) {
		$key=array_shift($ranks);
		if ($key>$current) return $key;
	}
	return 30;

}

function clan_previousrank($ranks,$current) {
	$temp=array_pop($ranks);
	$ranks=array_keys($ranks);
	while (count($ranks)>0) {
		$key=array_pop($ranks);
		if ($key<$current) return $key;
	}
	return 0;
}



?>