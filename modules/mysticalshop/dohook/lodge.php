<?php

if (get_module_setting("shopappear") == 1 && get_module_pref("pass") == 0){
	$pts = get_module_setting( 'pointsreq' );
	if( $pts == 0 )
		$pts = translate_inline( 'Free' );
	elseif( $pts == 1 )
		$pts = translate_inline( '1 Point' );
	else
		$pts = sprintf_translate( '%s Points', $pts );
	addnav( array( 'Get Pass to %s`0 (%s)', get_module_setting( 'shopname' ), $pts ), $from.'op=lodge&what=buypass' );
}
?>