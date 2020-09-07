<?php
	$success = true;

	$version = '4.2';//-- Version of LoGD
	$phpVersion = '7.2.5';//-- PHP version need

	//-- Need 128MB
	$_memoryLimit = @ini_get('memory_limit');
	$memoryLimit = $_memoryLimit;
	preg_match( "#^(\d+)(\w+)$#", strtolower($memoryLimit), $match );
	if( $match[2] == 'g' )
	{
		$memoryLimit = intval( $memoryLimit ) * 1024 * 1024 * 1024;
	}
	else if ( $match[2] == 'm' )
	{
		$memoryLimit = intval( $memoryLimit ) * 1024 * 1024;
	}
	else if ( $match[2] == 'k' )
	{
		$memoryLimit = intval( $memoryLimit ) * 1024;
	}
	else
	{
		$memoryLimit = intval( $memoryLimit );
	}

	$executionTime = @ini_get('max_execution_time');

	//-- Extensions
	$extensions = [
		"bcmath" => "BC Math",
		"curl" => "cURL",
		"exif" => "Exif",
		"gd" => "GD",
		"intl" => "Intl",
		"json" => "Json",
		"mbstring" => "Multibyte String",
		"pdo" => "PDO",
		"pdo_mysql" => "PDO MySQL",
		"session" => "Session",
	];

	class CustomMysqli extends mysqli
	{
		public function __construct()
		{
			parent::init();
			parent::options( MYSQLI_OPT_CONNECT_TIMEOUT, 5 );
			return call_user_func_array( 'parent::__construct', func_get_args() );
		}
	}

	$phpString = '';

	if (version_compare( PHP_VERSION, $phpVersion ) >= 0)
	{
		$phpString .= '<li class="success">PHP version '.PHP_VERSION.'.</li>';
	}
	else
	{
		$success = false;
		$phpString .= '<li class="fail">You are not running a compatible version of PHP. You need PHP '.$phpVersion.' or above (Your server have '.PHP_VERSION.'). You should contact your hosting provider or system administrator to ask for an upgrade.</li>';
	}

	if ($memoryLimit >= 128 * 1024 * 1024)
	{
		$phpString .= '<li class="success">'.$_memoryLimit.' memory limit.</li>';
	}
	else
	{
		$success = false;
		$phpString .= '<li class="fail">Your PHP memory limit is too low. It needs to be set to 128M or more. You should contact your hosting provider or system administrator to ask for this to be changed.</li>';
	}

	if ($executionTime >= 30 && 0 != $executionTime)
	{
		$phpString .= '<li class="success">'.$executionTime.' seconds of max execution time.</li>';
	}
	else
	{
		$success = false;
		$phpString .= '<li class="fail">Your PHP max execution time limit is too low. It needs to be set to 30 seg or more. You should contact your hosting provider or system administrator to ask for this to be changed.</li>';
	}

	//-- Check require extensions
	foreach($extensions as $ext => $name)
	{
		if (extension_loaded($ext))
		{
			$phpString .= '<li class="success">'.$name.' extension loaded.</li>';
		}
		else
		{
			$success = false;
			$phpString .= '<li class="fail">You do not have the '.$name.' PHP extension loaded which is required. You should contact your hosting provider or system administrator to ask for it to be enabled.</li>';
		}
	}

	$mysql = @new CustomMysqli('localhost');
	$mysqlString = '';
	if ($mysql->connect_errno)
	{
		$mysqlString .= '<li class="advisory">MySQL connection could not be established to perform version check. Make sure your MySQL Server version is 5.5.3 or above (5.6.2 or above recommended).</li>';
	}
	else
	{
		if (version_compare( $mysql->server_info, '5.6.2' ) >= 0)
		{
			$mysqlString .= '<li class="success">MySQL version '.$mysql->server_info.'.</li>';
		}
		else
		{
			$success = false;
			$mysqlString .= '<li class="fail">You are not running a compatible version of MySQL. You need MySQL 5.5.3 or above. You should contact your hosting provider or system administrator to ask for an upgrade.</li>';
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <title>Check requeriments for install of Legend of the Green Dragon</title>

        <style>
			body {
				background: #003800;
				font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
				width: 95%;
				margin: 0 auto;
				padding-top: 3%;
				color: white;
			}
			h1#header {
				background: #003200;
				font-size: 22px;
				font-weight: 200;
				padding: 0 15px;
				line-height: 58px;
			}
			h1#header img {
				display: inline-block;
				width: 30px;
				height: 30px;
				vertical-align: middle;
				margin: -3px 5px 0 0;
			}
			#main {
				background: #194b19;
				padding: 30px;
			}
			section {
				margin-bottom: 50px;
			}

			h1 {
				font-size: 26px;
				font-weight: 300;
				line-height: 1.2;
				margin: 0;
			}
			h2 {
				font-size: 18px;
				color: #ccc;
				line-height: 24px;
				font-weight: 400;
				display: inline-block;
				margin: 0;
			}
			hr {
				margin: 15px 0px;
				height: 0;
				padding: 0;
				border-width: 2px 0 0 0;
				border-style: solid;
				border-color: #ebebeb;
			}
			li {
				list-style: none;
				margin-bottom: 10px;
			}
			li.success {
				color: limegreen;
			}
			li.success:before {
				font-family: 'FontAwesome';
				content: '\f00c';
				margin:0 8px 0 -24px;
			}
			li.fail {
				color: indianred;
			}
			li.fail:before {
				font-family: 'FontAwesome';
				content: '\f057';
				margin:0 9px 0 -23px;
			}
			li.advisory {
				color: limegreen;
			}
			li.advisory:before {
				font-family: 'FontAwesome';
				content: '\f05a';
				margin:0 9px 0 -23px;
			}
			a.phpinfo {
				float: right;
				color: #868686;
				font-size: 11px;
			}
			p.success, p.fail {
				padding: 15px 15px 15px 45px;
				border-radius: 2px;
				position: relative;
				color: #fff;
				font-size: 14px;
			}
			p.success:before, p.fail:before {
				font-family: 'FontAwesome';
				position: absolute;
				top: 15px;
				left: 15px;
				font-size: 18px;
			}
			p.success a, p.fail a {
				color: #fff;
				border-bottom: 1px dotted #fff;
				text-decoration: none;
			}
			p.fail {
				background: #b52b38;
			}
			p.fail:before {
				content: '\f057';
			}
			p.success {
				background: #62874d;
			}
			p.success:before {
				content: '\f00c';
			}
			p.smaller {
				font-size: 11px;
			}
		</style>
    </head>

    <body>
		<h1 id="header">
			<img alt="" src="data:image/gif;base64,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" />
			Test results
		</h1>
		<div id="main">
			<?php if ($success): ?>
				<p class="success">You are ready to install/upgrade Legend of the Green Dragon <?= $version ?>!</p>
			<?php else: ?>
				<p class="fail">You are not ready to install/upgrade Legend of the Green Dragon <?= $version ?>. See the information below for instructions how to fix or <a rel="noopener noreferrer" href="https://github.com/idmarinas/lotgd-game/issues" target="_blank">create issue</a> for additional assistance.</p>
			<?php endif; ?>
		</div>
		<h1 id="header">
			<img alt="" src="data:image/gif;base64,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" />
			Legend of the Green Dragon <?= $version ?> test
		</h1>
        <div id="main">
			<section>
				<h2>PHP Requirements</h2>
				<ul><?= $phpString ?></ul>
			</section>
			<hr>
			<section>
				<h2>MySQL Requirements</h2>
				<ul>
					<?= $mysqlString ?>
				</ul>
			</section>
		</div>
    </body>
</html>
