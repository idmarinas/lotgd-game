<?php
	if ($session['user']['superuser'] & SU_EDIT_USERS){
		$id=httpget('userid');
		addnav("Forest Modules");
		addnav("Metal Mine","runmodule.php?module=metalmine&op=superuser&subop=edit&userid=$id");
	}
?>