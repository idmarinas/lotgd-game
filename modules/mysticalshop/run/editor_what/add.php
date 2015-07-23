<?php
$row = array(
	'id'=>'New','category'=>$cat,'name'=>'','description'=>'','gold'=>'','gems'=>'','dk'=>'',
	'attack'=>'','defense'=>'','charm'=>'','hitpoints'=>'','turns'=>'','favor'=>'',
	'bigdesc'=>'','rare'=>false,'rarenum'=>''
);
$row = array_merge( $row, $itemarray_extra_vals );

rawoutput( '<form action="'.htmlentities( $fromeditor ).'save" method="POST">' );
addnav( '', $fromeditor.'save' );
require_once( 'lib/showform.php' );
showform( $itemarray, $row );
rawoutput( '</form>' );
?>