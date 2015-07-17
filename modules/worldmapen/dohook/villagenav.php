<?php
if ($session['user']['location'] == 'World') {
	addnav("V?Return to the World", "runmodule.php?module=worldmapen&op=continue");
	$args['handled'] = 1;
}
?>