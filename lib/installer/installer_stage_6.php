<?php

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;

$body = '$adapter = ['  . PHP_EOL;
$body .= '	\'driver\' => \'' . $session['dbinfo']['DB_DRIVER'] . '\','  . PHP_EOL;
$body .= '	\'hostname\' => \'' . $session['dbinfo']['DB_HOST'] . '\','  . PHP_EOL;
$body .= '	\'database\' => \'' . $session['dbinfo']['DB_NAME'] . '\','  . PHP_EOL;
$body .= '	\'charset\' => \'utf8\','  . PHP_EOL;
$body .= '	\'username\' => \'' . $session['dbinfo']['DB_USER'] . '\','  . PHP_EOL;
$body .= '	\'password\' => \'' . $session['dbinfo']['DB_PASS'] . '\'' . PHP_EOL;
$body .= '];' . PHP_EOL . PHP_EOL;
$body .= '$DB_PREFIX = \'' . $session['dbinfo']['DB_PREFIX'] . '\';' . PHP_EOL;
$body .= '$DB_USEDATACACHE = \'' . $session['dbinfo']['DB_USEDATACACHE'] . '\';' . PHP_EOL;
$body .= '$DB_DATACACHEPATH = \'' . $session['dbinfo']['DB_DATACACHEPATH'] . '\';' . PHP_EOL;

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

/*
if ($success && ! $initial)
{
	$version = getsetting('installer_version', '-1');
	$sub = substr($version, 0, 5);
	$sub = (int)str_replace(".", "", $sub);
	if ($sub < 110) {
		$sql = "SELECT setting, value FROM ".db_prefix("settings")." WHERE setting IN ('usedatacache', 'datacachepath')";
		$result = db_query($sql);
		$fp = @fopen("dbconnect.php","r+");
		if ($fp){
			while(!feof($fp)) {
				$buffer = fgets($fp, 4096);
				if (strpos($buffer, "\$DB") !== false) {
					@eval($buffer);
				}
			}
			fclose($fp);
		}
		while ($row = db_fetch_assoc($result)) {
			if ($row['setting'] == 'datacachepath') {
				$DB_DATACACHEPATH = $row['value'];
			}
			if ($row['setting'] == 'usedatacache') {
				$DB_USEDATACACHE = $row['value'];
			}
		}
		$dbconnect =
			"<?php\n"
			."//This file automatically created by installer.php on ".date("M d, Y h:i a")."\n"
			."\$DB_HOST = \"{$DB_HOST}\";\n"
			."\$DB_USER = \"{$DB_USER}\";\n"
			."\$DB_PASS = \"{$DB_PASS}\";\n"
			."\$DB_NAME = \"{$DB_NAME}\";\n"
			."\$DB_PREFIX = \"{$DB_PREFIX}\";\n"
			."\$DB_USEDATACACHE = ". ((int)$DB_USEDATACACHE).";\n"
			."\$DB_DATACACHEPATH = \"".addslashes($DB_DATACACHEPATH)."\";\n"
			."?>\n";
		// Check if the file is writeable for us. If yes, we will change the file and notice the admin
		// if not, they have to change the file themselves...
		$fp = @fopen("dbconnect.php","w+");
		if ($fp){
			if (fwrite($fp, $dbconnect)!==false){
				output("`n`@Success!`2  I was able to write your dbconnect.php file.");
			}else{
				$failure=true;
			}
			fclose($fp);
		}else{
			$failure=true;
		}
		if ($failure) {
			output("`2With this new version the settings for datacaching had to be moved to `idbconnect.php`i.");
			output("Due to your system settings and privleges for this file, I was not able to perform the changes by myself.");
			output("This part involves you: We have to ask you to replace the content of your existing `idbconnect.php`i with the following code:`n`n`&");
			rawoutput("<blockquote><pre>".htmlentities($dbconnect, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</pre></blockquote>");
			output("`2This will let you use your existing datacaching settings.`n`n");
			output("If you have done this, you are ready for the next step.");
		} else {
			output("`n`^You are ready for the next step.");
		}
	} else {
		output("`n`^You are ready for the next step.");
	}
}else if(!$success) {
	$session['stagecompleted']=5;
}
*/