<?php

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;

$body = '$adapter = ['  . PHP_EOL;
$body .= '	\'driver\' => \'' . (isset($session['dbinfo']['DB_DRIVER'])?$session['dbinfo']['DB_DRIVER']:null) . '\','  . PHP_EOL;
$body .= '	\'hostname\' => \'' . (isset($session['dbinfo']['DB_HOST'])?$session['dbinfo']['DB_HOST']:null) . '\','  . PHP_EOL;
$body .= '	\'database\' => \'' . (isset($session['dbinfo']['DB_NAME'])?$session['dbinfo']['DB_NAME']:null) . '\','  . PHP_EOL;
$body .= '	\'charset\' => \'utf8\','  . PHP_EOL;
$body .= '	\'username\' => \'' . (isset($session['dbinfo']['DB_USER'])?$session['dbinfo']['DB_USER']:null) . '\','  . PHP_EOL;
$body .= '	\'password\' => \'' . $session['dbinfo']['DB_PASS'] . '\'' . PHP_EOL;
$body .= '];' . PHP_EOL . PHP_EOL;
$body .= '$DB_PREFIX = \'' . (isset($session['dbinfo']['DB_PREFIX'])?$session['dbinfo']['DB_PREFIX']:null) . '\';' . PHP_EOL;
$body .= '$DB_USEDATACACHE = \'' . (isset($session['dbinfo']['DB_USEDATACACHE'])?$session['dbinfo']['DB_USEDATACACHE']:null) . '\';' . PHP_EOL;
$body .= '$DB_DATACACHEPATH = \'' . (isset($session['dbinfo']['DB_DATACACHEPATH'])?$session['dbinfo']['DB_DATACACHEPATH']:null) . '\';' . PHP_EOL;

$file = FileGenerator::fromArray([
	'docblock' => DocBlockGenerator::fromArray([
		'shortDescription' => 'This file is automatically created by installer.php',
		'longDescription' => null,
		'tags' => [
			[
				'name' => 'create',
				'description' => date('M d, Y h:i a'),
			],
		],
	]),
	'body' => $body,
]);
unset($body);

$code = $file->generate();

if (file_exists('dbconnect.php'))
{
	$success = true;
	$initial = false;
}
else
{
	$initial = true;
	output("`@`c`bWriting your dbconnect.php file`b`c");
	output("`2I'm attempting to write a file named 'dbconnect.php' to your site root.");
	output("This file tells LoGD how to connect to the database, and is necessary to continue installation.`n");

	$result = file_put_contents('dbconnect.php', $code);

	if (false !== $result) output("`n`@Success!`2  I was able to write your dbconnect.php file, you can continue on to the next step.");
	else $failure = true;

	if ($failure)
	{
		output("`n`\$Unfortunately, I was not able to write your dbconnect.php file.");
		output("`2You will have to create this file yourself, and upload it to your web server.");
		output("The contents of this file should be as follows:`3");
		rawoutput('<blockquote><pre>'.htmlentities($code, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</pre></blockquote>');
		output("`2Create a new file, past the entire contents from above into it`2 ).");
		output("When you have that done, save the file as 'dbconnect.php' and upload this to the location you have LoGD at.");
		output("You can refresh this page to see if you were successful.");
	}
	else $success = true;
}

if(! $success) $session['stagecompleted'] = 5;

if (! $initial)
{
	output('`$The file "dbconnect.php" was created before. If you want update with new data, please delete it.`n');
	output('For now, game cant do update installation.`0`n');
}