<?php
/* This is a library function for the equipment shop.  It replicates some of
 * the core functionality to ensure compatibility with older core versions.
 */

/* massinvalidate erases all cache files that contain $name in the file name. */
function mysticalshop_massinvalidate( $name )
{
	if( getsetting( 'usedatacache', false ) )
	{
		$name = DATACACHE_FILENAME_PREFIX.$name;
		global $datacachefilepath;
		if( $datacachefilepath == '' )
			$datacachefilepath = getsetting( 'datacachepath', '/tmp' );
		if( @is_dir( $datacachefilepath ) )
		{
			$dir = dir( $datacachefilepath );
			while( ( $file = $dir->read() ) !== false )
				if( strpos( $file, $name ) !== false )
					invalidatedatacache( str_replace( DATACACHE_FILENAME_PREFIX, '', $file ) );
			$dir->close();
		}
	}
}
?>