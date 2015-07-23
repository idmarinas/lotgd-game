<?php
if( get_module_setting( 'shopappear' ) == 1 )
{
	$args['count']++;
	$format = $args['format'];
	$match = array();
	$color = '`0';
	if( preg_match( '/(`.)\s*(`[bic])*%s/', $format, $match ) )
	  $color = $match[1];
	$pts = get_module_setting( 'pointsreq' );
	if( $pts == 0 )
		$pts = translate_inline( 'free once you\'ve made a donation of any amount' );
	elseif( $pts == 1 )
		$pts = translate_inline( 'only 1 point' );
	else
		$pts = sprintf_translate( 'only %s points', $pts );
	output( $format,
			sprintf_translate( 'Access to %s%s for %s.`0', get_module_setting( 'shopname' ), $color, $pts ),
			true );
}
?>