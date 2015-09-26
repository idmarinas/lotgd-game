<?
$area = httpget("area");
$playerloca = translate_inline($session['user']['location']);
if (get_module_setting('eventid') > 0)
{
	$eventid = get_module_setting('eventid');
	$sql = "INSERT INTO ".db_prefix("commentary")." (postdate,section,author,comment) VALUES (now(),'$area',$eventid,\"::`#A young man on a unicycle comes in yelling, ".$session['user']['name']."`# needs legal representation in the $playerloca jail!\")";
}
else
{
	$acctid = $session['user']['acctid'];
	$sql = "INSERT INTO ".db_prefix("commentary")." (postdate,section,author,comment) VALUES (now(),'$area', $acctid,\":: has sent a town crier in hopes someone will come to $playerloca with legal representation.\")"; 
}
db_query($sql); 
set_module_pref('towncries', get_module_pref('towncries') - 1); 
output
(
//	"The crier heads off on his unicycle to seek legal representation for you in %s. Now all you can do is sit and hope someone 
//	is will to help.", $village
); 
injailnav(); 
?>