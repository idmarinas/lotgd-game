<?php
if ($args['setting'] == "villagename") {
	if ($args['old'] == get_module_setting("shoploc")) {
		set_module_setting("shoploc", $args['new']);
	}
}
elseif( $args['setting'] == 'shownum' )
{
	if( getsetting( 'usedatacache', false ) )
	{
		require_once( './modules/mysticalshop/libcoredup.php' );
		mysticalshop_massinvalidate( 'modules-mysticalshop-viewgoods-' );
	}
}
?>