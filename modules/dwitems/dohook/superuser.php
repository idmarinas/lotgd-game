<?php
if ($session['user']['superuser'] & SU_EDIT_USERS) {
	addnav("Editors");
	addnav("Dwelling Items", "runmodule.php?module=dwitems&op=editor");
}
?>