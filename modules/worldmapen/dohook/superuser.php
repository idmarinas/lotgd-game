<?php
if (($session['user']['superuser'] & SU_EDIT_USERS) || get_module_pref("canedit")) {
		addnav("Module Configurations");
		addnav("World Map Editor","runmodule.php?module=worldmapen&op=edit&admin=true");
}
?>