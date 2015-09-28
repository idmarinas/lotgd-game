<?php
addnav("Be a witness", "runmodule.php?module=jail&op=witness"); 
addnav("Be the Barrister", "runmodule.php?module=jail&op=barrister"); 
addnav("Forget it", "runmodule.php?module=jail"); 
output
(
	"The defendant will need a barrister to state his case, and also 2 witnesses to go on the stand.`n
	The barrister must have %s dragonkills more than the defendant, and all witnesses must be of level 
	%s or greater.", get_module_setting('bardk'), get_module_setting("minlvl")
); 
?>