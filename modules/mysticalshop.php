<?php
/**************
Name: Equipment Shop
Author: Eth - ethstavern(at)gmail(dot)com 
Version: 3.814
About: A shop that sells a wide array of different items.
       Complete with editor and separate table in db.
Translation compatible. Mostly.
*****************/

function mysticalshop_getmoduleinfo(){
	require( './modules/mysticalshop/getmoduleinfo.php' );
	return $info;
}
function mysticalshop_install(){
	require( './modules/mysticalshop/install.php' );
	return true;
}
function mysticalshop_uninstall(){
	require_once( './modules/mysticalshop/run/editor_what/delete.php' );
	$items = db_query( 'SELECT id FROM '.db_prefix( 'magicitems' ) );
	while( $item = db_fetch_assoc( $items ) )
		mysticalshop_delete_item( $item['id'] );

	$sql = 'DROP TABLE IF EXISTS '.db_prefix( 'magicitems' );
	db_query( $sql );

	if( getsetting( 'usedatacache', false ) )
	{
		require_once( './modules/mysticalshop/libcoredup.php' );
		mysticalshop_massinvalidate( 'modules-mysticalshop-' );
	}
	
	return true;
}
function mysticalshop_dohook($hookname,$args){
	global $session;
	$from = 'runmodule.php?module=mysticalshop&';
	require( "./modules/mysticalshop/dohook/$hookname.php" );
	return $args;
}
function mysticalshop_run(){
	global $session;
	$shop = get_module_setting( 'shopname' );
	$op = httpget( 'op' );
	$from = 'runmodule.php?module=mysticalshop&';
	page_header( full_sanitize( $shop ) );
	$what = httpget( 'what' );
	$cat = httpget( 'cat' );
	if( !is_numeric( $cat ) )
		$cat = 0;
	$names = translate_inline( array(0=>'Rings',1=>'Amulets',2=>'Weapons',3=>'Armor',4=>'Cloaks',5=>'Helmets',6=>'Gloves',7=>'Boots',8=>'Miscellanea') );
	require_once( "./modules/mysticalshop/run/case_$op.php" );
	page_footer();
}
?>