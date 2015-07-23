<?php
$postname = httppost( 'name' );
$postdescribe = httppost( 'description' );
$postbigdesc = httppost( 'bigdesc' );
$postname = stripslashes( $postname );
$postdescribe = stripslashes( $postdescribe );
$postbigdesc = stripslashes( $postbigdesc );
$displayname = $postname;
if( function_exists( 'mysql_real_escape_string' ) )
{
	$name = mysql_real_escape_string( $postname );
	$describe = mysql_real_escape_string( $postdescribe );
	$bigdesc = mysql_real_escape_string( $postbigdesc );
}
else
{
	$postname = addslashes( $postname );
	$postdescribe = addslashes( $postdescribe );
	$postbigdesc = addslashes( $postbigdesc );
}
//catch the submitted value, check to see if it's empty. if it is, declare it as 0 and move on
//if it isn't, use original value instead
$itemid = httppost( 'id' );
$postcat = httppost( 'category' );
$gold = httppost( 'gold' );
$gems = httppost( 'gems' );
$dk = httppost( 'dk' );
$attack = httppost( 'attack' );
$defense = httppost( 'defense' );
$charm = httppost( 'charm' );
$hitpoints = httppost( 'hitpoints' );
$turns = httppost( 'turns' );
$favor = httppost( 'favor' );
$rare = httppost( 'rare' );
$rarenum = httppost( 'rarenum' );

if( !is_numeric( $itemid ) ) $itemid = 0;
if( !is_numeric( $postcat ) ) $postcat = 0;
if( !is_numeric( $gold ) ) $gold = 0;
if( !is_numeric( $gems ) ) $gems = 0;
if( !is_numeric( $dk ) ) $dk = 0;
if( !is_numeric( $attack ) ) $attack = 0;
if( !is_numeric( $defense ) ) $defense = 0;
if( !is_numeric( $charm ) ) $charm = 0;
if( !is_numeric( $hitpoints ) ) $hitpoints = 0;
if( !is_numeric( $turns ) ) $turns = 0;
if( !is_numeric( $favor ) ) $favor = 0;
if( !is_numeric( $rare ) ) $rare = 0;
if( !is_numeric( $rarenum ) ) $rarenum = 0;
//
if ($itemid>0){
	$sql = "UPDATE ".db_prefix("magicitems")."
		SET category=$postcat,
		name='$name',
		description='$describe',
		gold=$gold,
		gems=$gems,
		dk=$dk,
		attack=$attack,
		defense=$defense,
		charm=$charm,
		hitpoints=$hitpoints,
		turns=$turns,
		favor=$favor,
		bigdesc='$bigdesc',
		rare=$rare,
		rarenum=$rarenum
		WHERE id=$itemid";
	db_query( $sql );
	output( '`6The item "`^%s`6" has been successfully edited.`n`n', $displayname );
	if( getsetting( 'usedatacache', false ) && $cat != $postcat )
	{
	  $cat = (int)$cat;
		require_once( './modules/mysticalshop/libcoredup.php' );
		invalidatedatacache( 'modules-mysticalshop-view-'.$cat );
		mysticalshop_massinvalidate( 'modules-mysticalshop-viewgoods-'.$cat );
	}
}else{
	$sql = 'LOCK TABLES '.db_prefix( 'magicitems' ).' WRITE;';
	db_query( $sql );
	$sql = "INSERT INTO ".db_prefix("magicitems")."
	(category,name,description,gold,gems,dk,attack,defense,charm,hitpoints,turns,favor,bigdesc,rare,rarenum)
	VALUES ($postcat,'$name','$describe',$gold,$gems,$dk,$attack,$defense,$charm,$hitpoints,$turns,$favor,'$bigdesc',$rare,$rarenum)";
	db_query( $sql );
	$itemid = db_insert_id();
	$sql = 'UNLOCK TABLES;';
	db_query( $sql );
	output( '`6The item "`^%s`6" has been saved to the database.`n`n', $displayname );
}

output( 'Would you like to <a href="'.htmlentities( $fromeditor.'preview&id='.$itemid.'&cat=' ).$postcat.'">[ Review ]</a> or <a href="'.htmlentities( $fromeditor.'edit&id='.$itemid.'&cat=' ).$postcat.'">[ Edit ]</a> this item?`0', true );
addnav( '', $fromeditor.'edit&id='.$itemid.'&cat='.$postcat );
addnav( '', $fromeditor.'preview&id='.$itemid.'&cat='.$postcat );

if( getsetting( 'usedatacache', false ) )
{
	invalidatedatacache( 'modules-mysticalshop-editorcats' );
	invalidatedatacache( 'modules-mysticalshop-view-'.$postcat );
	invalidatedatacache( 'modules-mysticalshop-enter' );
	require_once( './modules/mysticalshop/libcoredup.php' );
	mysticalshop_massinvalidate( 'modules-mysticalshop-viewgoods-'.$postcat );
}
httpset( 'id', $itemid );
httpset( 'cat', $postcat );
$cat = $postcat;
?>