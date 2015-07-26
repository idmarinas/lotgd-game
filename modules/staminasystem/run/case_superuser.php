<?php

require_once "lib/superusernav.php";

page_header("Actions Management");

output("`c`bActions Management`b`c`n`n");
output("Here you can see all the Actions that have been installed, along with their default values.`n`n");
rawoutput("<table border='0' cellpadding='2' cellspacing='0' border='1 px solid #000000'>");
rawoutput("<tr><td>Action Name</td><td>Max Cost</td><td>Min Cost</td><td>EXP per usage</td><td>EXP for level up</td><td>Cost--/lvl</td><td>DK carryover %%</td></tr>");

$actions = get_default_action_list();

foreach($actions AS $action => $values ){
	rawoutput("<tr><td>".$action."</td><td>".$values['maxcost']."</td><td>".$values['mincost']."</td><td>".$values['expperrep']."</td><td>".$values['expforlvl']."</td><td>".$values['costreduction']."</td><td>".$values['dkpct']."</td></tr>");
}
rawoutput("</table>");
addnav("Edit Player","runmodule.php?module=staminasystem&op=editplayer");
superusernav();

debug($actions);
page_footer();

?>