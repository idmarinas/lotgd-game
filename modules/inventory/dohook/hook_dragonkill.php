<?php
	require_once("lib/itemhandler.php");
	$inventory = get_inventory();
	$count=0;
	while ($item = db_fetch_assoc($inventory)) {
		$destroyed = 0;
		for($c=0;$c<$item['quantity'];$c++) {
			if($item['dkloosechance'] >= e_rand(1,100)) $destroyed++;

		}
		if ($destroyed) remove_item((int)$item['itemid'], $destroyed);
		$count+=$destroyed;
	}
	if ($count == 1) {
		output("`n`2Roto en pedazos estan los restos de lo que un día fue un objeto poderoso por el poder de las llamas del dragón. ");
	} else if ($count > 1) {
		output("`n`2Rotos en pedazos estan los restos de lo que un día fue unos objetos poderosos por el poder de las llamas del dragón. ");
		output("Parece que se han destruido `^%s objetos`2.", $count);
	}
?>