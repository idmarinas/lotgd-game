<?php
	if ($session['user']['superuser'] & SU_EDIT_USERS){
		$id=httpget('userid');
		addnav("Village Modules");
		addnav("Fruit Orchard XL","runmodule.php?module=orchard&op=superuser&subop=edit&userid=$id");
	}
?>