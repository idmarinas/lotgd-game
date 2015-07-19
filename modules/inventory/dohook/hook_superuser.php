<?php
	global $session;
	if ($session['user']['superuser'] & SU_EDIT_USERS) {
		addnav("Editores");
		addnav("X?Editor Objetos", "runmodule.php?module=inventory&op=editor");
	}
?>