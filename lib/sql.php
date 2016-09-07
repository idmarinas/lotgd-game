<?php
// translator ready
// addnews ready
// mail ready

// A slightly higher level SQL error reporting function.
function sql_error($sql){
	global $session;
	return output_array($session)."SQL = <pre>$sql</pre>".DB::error();//Eliminado el LINK, ya no es necesario
}

?>
