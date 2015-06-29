<?php
	global $session;
	if ($session['user']['superuser'] & SU_EDIT_USERS) {
		addnav("Editors");
		addnav("X?Item Editor", "runmodule.php?module=inventory&op=editor");
	}
?>