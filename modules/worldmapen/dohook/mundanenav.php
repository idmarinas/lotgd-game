<?php
if ($session['user']['location'] == 'World') { 
	addnav("M?Return to the Mundane", "runmodule.php?module=worldmapen&op=continue");
	$args['handled'] = 1;
}
?>