<?php
$id = httpget('id');
$fromeditor = $from.'op=editor&what=';
if( $what == 'add' || $what == 'edit' )
{
	$itemarray = array(
		"Item Properties,title",
			"id"=>"Item ID,hidden",
			"category"=>"Item Category,enum,0,Ring,1,Amulet,2,Weapon,3,Armor,4,Cloak,5,Helmet,6,Gloves,7,Boots,8,Miscellaneous",
			"name"=>"Item Name,Name",
			"description"=>"Short Description,Desc",
			"gold"=>"Item Cost in Gold,int",
			"gems"=>"Item Cost in Gems,int",
			"dk"=>"Dragon Kills Needed to Own,int",
		"Item Stats,title",
			"Values can be positive or negative; 1 or -1 for example. Only numeric values are accepted.,note",
			"Use this feature with caution.,note",
			"attack"=>"Bonus/Penalty to Attack,int",
			"defense"=>"Bonus/Penalty to Defense,int",
			"charm"=>"Bonus/Penalty to Charm,int",
			"hitpoints"=>"Bonus/Penalty to Hitpoints,int",
			"turns"=>"Bonus/Penalty to Turns,int",
			"favor"=>"Bonus/Penalty to Favor,int",
		"Verbose Description,title",
			"Add a longer description here for when examining the item in the shop. You may you use formatting and line breaks and color codes as well. This part is optional.,note",
			"bigdesc"=>",textarea",
		"Rare Item Stats,title",
			"This part is optional and will allow you to sell a limited number of items.,note",
			"rare"=>"Is this item Rare or Limited?,enum,0,No,1,Yes",
			"rarenum"=>"How many of these items are there for sale?,int",
	);
	$itemarray_extra = modulehook( 'mysticalshop-editor-itemsettings' );
	$itemarray_extra_fields = array();
	$itemarray_extra_vals = array();
	foreach( $itemarray_extra as $var=>$desc )
	{
		$itemarray_extra_data = explode( '|', $desc );
		$itemarray_extra_fields[$var] = trim( $itemarray_extra_data[0] );
		if( array_key_exists( 1, $itemarray_extra_data ) )
		{
			$extra_data = trim( $itemarray_extra_data[1] );
			if( $extra_data != '' )
				$itemarray_extra_vals[$var] = $extra_data;
		}
	}
	//debug($itemarray_extra_fields);
	$itemarray = array_merge( $itemarray, $itemarray_extra_fields );
}
require_once("modules/mysticalshop/run/editor_what/$what.php");
//here's a module hook, if anyone ever needs it
modulehook("mysticalshop-editor", array());
addnav("Actions");
//let's just display items that are actually available.
$sql = 'SELECT category FROM '.db_prefix( 'magicitems' ).' GROUP BY category ORDER BY category';
$result = db_query_cached( $sql, 'modules-mysticalshop-editorcats', 3600 );
$shortcuts = array( 'g', 't', 'W', 'o', 'C', 'H', 'v', 'B', 'M' );
while( $row = db_fetch_assoc( $result ) ){
	$category = $row['category'];
	addnav( array( '%s?Examine %s`0', $shortcuts[$category], $names[$category] ), $fromeditor.'view&cat='.$category );
}

addnav("Admin Tools");
addnav( 'Add an Item', $fromeditor.'add&cat='.$cat );
addnav("Refresh List", $fromeditor."view&cat=$cat");
addnav("Other");
addnav("Return to the Grotto", "superuser.php");
?>