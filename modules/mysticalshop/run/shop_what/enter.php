<?php
if( !strstr( getenv( 'HTTP_REFERER' ), 'runmodule.php?module=mysticalshop' ) )
{
	if (get_module_setting("shopappear") == 1 AND get_module_pref("pass")==1){
		output("`2%s`2 nods as you hold up your token upon entering.`n`n", $shopkeep);
	}
	if ($shopdesc>""){
		output("%s`n`n",$shopdesc);
		//default message for the lazy admin
	}else{
		output("`2Coming in from the alleyway, your eyes take a moment to adjust to the dark atmosphere inside the shop.");
		output(" `2Looking about, you are amazed to see a wide range of items and equipment that line the walls on racks and in display cases.`n`n");
		output("\"Greetings, friend,\" says %s`2. \"Welcome to my shop!\"`n`n",$shopkeep);
	}
}
else
	output( '`2%s`2 looks at you expectantly, patiently waiting for you to examine the merchandise.`n`n', $shopkeep );
if ($hasgift == 1){
	output("`^%s `^notes you have a gift waiting for pickup.`n", $shopkeep);
	output("`^\"Perhaps you'll want to see it?\"`n`n");
}
output_notl( '`0' );
//Note to self: STOP removing this
modulehook("mysticalshop", array());
addnav("Merchandise");
$sql = 'SELECT category,MIN(dk) as dks FROM '.db_prefix( 'magicitems' ).' GROUP BY category ORDER BY category';
$result = db_query_cached( $sql, 'modules-mysticalshop-enter', 3600 );

$specialty = $session['user']['specialty'];
$userdk = $session['user']['dragonkills'];
$shortcuts = array( 'R', 't', 'W', 'A', 'C', 'H', 'G', 'B', 'M' );
while( $row = db_fetch_assoc( $result ) )
{
	if( $userdk >= $row['dks'] )
	{
		$category = $row['category'];
		if( $specialty != 'MN' || $category != '2' && $category != '3' )
			addnav( array( '%s?Examine %s`0', $shortcuts[$category], $names[$category] ),$from.'op=shop&what=viewgoods&cat='.$category );
	}
}
if ($hasgift == 1) {
	addnav("Special");
	addnav("Pick Up Gift",$from."op=gift&what=examinegift");
}
addnav("Exit");
villagenav();
?>