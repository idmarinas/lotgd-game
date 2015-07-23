<?php
if( $session['user']['superuser'] & SU_EDIT_MOUNTS )
{
	addnav( 'Editors' );

	$ed_cats = db_fetch_assoc(
			db_query_cached( 'SELECT category FROM '.db_prefix( 'magicitems' ).
						' GROUP BY category ORDER BY category',
					'modules-mysticalshop-editorcats', 3600
				)
		);
	$cat = $ed_cats['category'];
	if( $cat == trim( '' ) || $cat === false )
		$cat = 100;

	addnav( 'Equipment Editor', $from.'op=editor&what=view&cat='.$cat );
}
?>