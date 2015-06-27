<?php

#This is the lotgd configuration file which you have to edit for several settings

$game_dir='';
// enter the directory of your game installation, this cannot be done elsewhere
// for example 
// $game_dir='/var/www/html/lotgd';
// This DEPENDS on your hosting! It is 100% sure if you use shared hosting this is different
// If you have a rootserver, you'd know what your directory is =)

$DB_TYPE='mysql';
/* * * *
 * Avaiable values for DBTYPE:
 *
 * - mysql:				The default value. Are you unsure take this.
 * - mysqli_oos:		The MySQLi extension of PHP5, object oriented style
 * - mysqli_proc:		The MySQLi extension of PHP5, procedural style
 *
 If you encounter problems with one of them, please let the developer know at http://nb-core.org
 Any other than "mysql" seem to be not perfectly integrated somehow, but work most of the time.
 */

$gz_handler_on=0;
/* set to 1 if you want to enable gzip compression to save bandwith (~30-50%), but it costs slightly more processor power for PHP to get it done. z_lib in apache is favoured if you have direct access to your machine. 
Actually, if you can set this to 0 and add these lines in i.e. /etc/php5/apache2/conf.d into a randomly named .ini file:
zlib.output_compression = 1
zlib.output_compression_level = 7
for instance. And then do an "apache2 -k graceful" and check with phpinfo() to see if it worked.
*/

/* The default skin which gets selected if you have NO skin accessible / configured or the database simply does not exist to select a default skin.

This will also be used in cases of database outages and so forth

*/
$_defaultskin="modern.htm";


?>
