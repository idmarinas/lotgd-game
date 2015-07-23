<?php
if( isset( $args['applyall'] ) && $args['applyall'] )
{
	$buffs = array( 'attack'=>'attack', 'defense'=>'defense', 'charm'=>'charm', 'hitpoints'=>'maxhitpoints', 'turns'=>'turns', 'favor'=>'deathpower' );
	require_once("modules/mysticalshop/lib.php");
	$args = mysticalshop_applyenh( $buffs, $args );
	$args['applyall'] = false;
}
?>