<?php
if ($args['loc'] == "World") { 
	$args['handled'] = 1;
	
	if ($args['count'] == 1) {
		output("`&There is `^1`& person camping in the wilderness whom you might find interesting.`0`n");
	} else {
		output("`&There are `^%s`& people camping in the wilderness whom you might find interesting.`0`n", $args['count']);
	}
}
?>