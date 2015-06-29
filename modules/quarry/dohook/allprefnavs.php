<?php
	$id=httpget('userid');
	addnav("Village Modules");
	addnav("Quarry","runmodule.php?module=quarry&op=superuser&subop=edit&userid=$id");
?>