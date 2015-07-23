<?php
if( is_numeric( $id ) )
{
	$sql = 'SELECT * FROM '.db_prefix( 'magicitems' ).' WHERE id='.$id.' LIMIT 1';
	$result = db_query( $sql );
	$row = array_merge( db_fetch_assoc( $result ), $itemarray_extra_vals );

	rawoutput( '<form action="'.htmlentities( $fromeditor.'save&id='.$id.'&cat=' ).$cat.'" method="POST">' );
	addnav( '', $fromeditor.'save&id='.$id.'&cat='.$cat );
	require_once( 'lib/showform.php' );
	showform( $itemarray, $row );
	rawoutput( '</form>' );
}
else
	output( 'Nothing to edit.' );
?>