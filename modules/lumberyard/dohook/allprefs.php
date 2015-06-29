<?php
	if ($session['user']['superuser'] & SU_EDIT_USERS){
		$id=httpget('userid');
		addnav("Forest Modules");
		addnav("Lumber Yard","runmodule.php?module=lumberyard&op=superuser&subop=edit&userid=$id");
	}
?>