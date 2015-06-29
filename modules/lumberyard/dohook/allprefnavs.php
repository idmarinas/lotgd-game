<?php
	$id=httpget('userid');
	addnav("Forest Modules");
	addnav("Lumber Yard","runmodule.php?module=lumberyard&op=superuser&subop=edit&userid=$id");
?>