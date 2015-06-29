<?php
require_once("lib/tabledescriptor.php");
if (!is_module_active('dwinns')){
	if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO)
		output_notl("`4Installing dwellings Module: dwitems.`n");
}else{
	if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO)
		output_notl("`4Updating dwellings Module: dwitems.`n");
}

/*
 * TYPE
 * 0 = charm
 * 1 = gold
 * 2 = gem
 * 3 = hp
 * 4 = maxhp
 * 5 = favor
 * 6 = turn
 * 7 = script
 */
$dwitems = array(
	'itemid'=>array('name'=>'itemid', 'type'=>'int unsigned'),
	'name'=>array('name'=>'name', 'type'=>'varchar(50)'),
	'type'=>array('name'=>'type', 'type'=>'int unsigned'),
	'amount'=>array('name'=>'amount', 'type'=>'int'),
	'chance'=>array('name'=>'chance', 'type'=>'int unsigned'),
	'goldcost'=>array('name'=>'goldcost', 'type'=>'int unsigned'),
	'gemcost'=>array('name'=>'gemcost', 'type'=>'int unsigned'),
	'mindk'=>array('name'=>'mindk', 'type'=>'varchar(50)'),
	'newdaytext'=>array('name'=>'newdaytext', 'type'=>'varchar(200)','null'=>'1'),
	'dwellingtext'=>array('name'=>'dwellingtext', 'type'=>'varchar(200)','null'=>'1'),
	'dwellingtextplural'=>array('name'=>'dwellingtextplural', 'type'=>'varchar(200)','null'=>'1'),
	'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'itemID'),
);
$dwellingsitems = array(
	'itemid'=>array('name'=>'itemid', 'type'=>'int unsigned'),
	'dwid'=>array('name'=>'dwid', 'type'=>'int unsigned'),
	'quantity'=>array('quantity'=>'dwid', 'type'=>'int unsigned'),
);

synctable(db_prefix('dwitems'), $dwitems, true);
synctable(db_prefix('dwellingsitems'), $dwellingsitems, true);

$sql = "SELECT itemid FROM " . db_prefix("dwitems");
$result = db_query($sql);
if(db_num_rows($result) == 0){
	$sql = "INSERT INTO " . db_prefix("dwitems") . " VALUES (0, 'ming vase', 0, 1, 10, 200, 2, 1, '`3Violet comes for a visit and sees your ming vase. She is impressed by your taste in foreign art.`n', '`2A ming vase is standing in the corner.`0`n', '`2Nicely arranged across the room are %s ming vases.`0`n')";
	db_query($sql);
	$sql = "INSERT INTO " . db_prefix("dwitems") . " VALUES (1, 'pillow', 3, 5, 10, 50, 0, 0, '`4Your pillow felt very fluffy last night. You feel well rested.`n', '`2On the bed lies a small pillow.`0`n', '`2The bed is cushioned with %s pillows`0`n')";
	db_query($sql);
}

module_addhook("village");
module_addhook_priority("newday", 10);
module_addhook("dwellings-inside");
module_addhook("superuser");
?>