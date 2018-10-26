<?php

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ValueGenerator;

if (file_exists('config/autoload/local/dbconnect.php'))
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

    $configuration = [
        'lotgd_core' => [
            'db' => [
                'adapter' => [
                    'driver' => $session['dbinfo']['DB_DRIVER'],
                    'hostname' => $session['dbinfo']['DB_HOST'],
                    'database' => $session['dbinfo']['DB_NAME'],
                    'charset' => 'utf8',
                    'username' => $session['dbinfo']['DB_USER'],
                    'password' => $session['dbinfo']['DB_PASS']
                ],
                'prefix' => $session['dbinfo']['DB_PREFIX']
            ],
            'cache' => [
                'active' => $session['dbinfo']['DB_USEDATACACHE'],
                'config' => [
                    'namespace' => 'lotgd',
                    'ttl' => 900,
                    'cache_dir' => $session['dbinfo']['DB_DATACACHEPATH'],
                ]
            ]
        ]
    ];

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
        'body' => 'return '.new ValueGenerator($configuration, ValueGenerator::TYPE_ARRAY_SHORT).';',
    ]);
    unset($configuration);

    $code = $file->generate();

    $result = file_put_contents('config/autoload/local/dbconnect.php', $code);

    if (false !== $result)
    {
        $failure = false;
        output('`n`@Success!`2  I was able to write your "config/autoload/local/dbconnect.php" file, you can continue on to the next step.');
    }
    else
    {
        $failure = true;
    }

    if ($failure)
    {
        output('`n`$Unfortunately, I was not able to write your "config/autoload/local/dbconnect.php" file.');
        output('`2You will have to create this file yourself, and upload it to your web server.');
        output('The contents of this file should be as follows:`3');
        rawoutput('<blockquote><pre>'.htmlentities($code, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</pre></blockquote>');
        output('`2Create a new file, past the entire contents from above into it`2 ).');
        output('When you have that done, save the file as "config/autoload/local/dbconnect.php" and upload this to the location you have LoGD at.');
        output('You can refresh this page to see if you were successful.');
        output('`nIf have problems, you can try delete file "cache/service-manager.config.php".');
    }
    else
    {
        $success = true;
    }

    //-- Delete the old cache file from the Service Manager and force it to generate it again.
    unlink('cache/service-manager.config.php');
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
