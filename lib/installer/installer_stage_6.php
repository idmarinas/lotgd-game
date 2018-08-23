<?php

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;

if (file_exists('dbconnect.php'))
{
    $success = true;
    $initial = false;
}
else
{
    $initial = true;
    output('`@`c`bWriting your dbconnect.php file`b`c');
    output("`2I'm attempting to write a file named 'dbconnect.php' to your site root.");
    output('This file tells LoGD how to connect to the database, and is necessary to continue installation.`n');

    $body = '$adapter = ['.PHP_EOL;
    $body .= '	\'driver\' => \''.$session['dbinfo']['DB_DRIVER'].'\','.PHP_EOL;
    $body .= '	\'hostname\' => \''.$session['dbinfo']['DB_HOST'].'\','.PHP_EOL;
    $body .= '	\'database\' => \''.$session['dbinfo']['DB_NAME'].'\','.PHP_EOL;
    $body .= '	\'charset\' => \'utf8\','.PHP_EOL;
    $body .= '	\'username\' => \''.$session['dbinfo']['DB_USER'].'\','.PHP_EOL;
    $body .= '	\'password\' => \''.$session['dbinfo']['DB_PASS'].'\''.PHP_EOL;
    $body .= '];'.PHP_EOL.PHP_EOL;
    $body .= '$DB_PREFIX = \''.$session['dbinfo']['DB_PREFIX'].'\';'.PHP_EOL;
    $body .= '$DB_USEDATACACHE = \''.$session['dbinfo']['DB_USEDATACACHE'].'\';'.PHP_EOL;
    $body .= '$DB_DATACACHEPATH = \''.$session['dbinfo']['DB_DATACACHEPATH'].'\';'.PHP_EOL;
    $body .= '$gz_handler_on = 0;'.PHP_EOL;

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

    $result = file_put_contents('dbconnect.php', $code);

    if (false !== $result)
    {
        $failure = false;
        output('`n`@Success!`2  I was able to write your dbconnect.php file, you can continue on to the next step.');
    }
    else
    {
        $failure = true;
    }

    if ($failure)
    {
        output('`n`$Unfortunately, I was not able to write your dbconnect.php file.');
        output('`2You will have to create this file yourself, and upload it to your web server.');
        output('The contents of this file should be as follows:`3');
        rawoutput('<blockquote><pre>'.htmlentities($code, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</pre></blockquote>');
        output('`2Create a new file, past the entire contents from above into it`2 ).');
        output("When you have that done, save the file as 'dbconnect.php' and upload this to the location you have LoGD at.");
        output('You can refresh this page to see if you were successful.');
    }
    else
    {
        $success = true;
    }
}

if (! $success)
{
    $session['stagecompleted'] = 5;
}

if (! $initial)
{
    output('`$The file "dbconnect.php" was created before. If you want update with new data, please delete it.`n');
    output('For now, game cant do update installation.`0`n');
}
