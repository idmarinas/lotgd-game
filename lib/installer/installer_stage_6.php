<?php

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ValueGenerator;

$success = true;
$initial = false;
if (! file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
{
    $initial = true;
    output('`@`c`bWriting your "%s" file´b´c', \Lotgd\Core\Application::FILE_DB_CONNECT);
    output("`2I'm attempting to write a file named '%s' to your site root.", \Lotgd\Core\Application::FILE_DB_CONNECT);
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

    $result = file_put_contents(\Lotgd\Core\Application::FILE_DB_CONNECT, $code);

    $failure = true;
    if (false !== $result)
    {
        $failure = false;
        output('`n`@Success!`2  I was able to write your "%s" file, you can continue on to the next step.', \Lotgd\Core\Application::FILE_DB_CONNECT);
    }

    if ($failure)
    {
        $success = false;
        output('`n`$Unfortunately, I was not able to write your "%s" file.', \Lotgd\Core\Application::FILE_DB_CONNECT);
        output('`2You will have to create this file yourself, and upload it to your web server.');
        output('The contents of this file should be as follows:`3');
        rawoutput('<blockquote><pre>'.htmlentities($code, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</pre></blockquote>');
        output('`2Create a new file, past the entire contents from above into it`2 ).');
        output('When you have that done, save the file as "%s" and upload this to the location you have LoGD at.', \Lotgd\Core\Application::FILE_DB_CONNECT);
        output('You can refresh this page to see if you were successful.');
        output('`nIf have problems, you can try delete file "%s".', \Lotgd\Core\ServiceManager::CACHE_FILE);
    }
}

//-- Delete the old cache file from the Service Manager and force it to generate it again.
if (file_exists(\Lotgd\Core\ServiceManager::CACHE_FILE))
{
    unlink(\Lotgd\Core\ServiceManager::CACHE_FILE);
}

if (! $success)
{
    $session['installer']['stagecompleted'] = 5;
}

if (! $initial)
{
    output('`$The file "%s" was created before. If you want update with new data, please delete it.`n', \Lotgd\Core\Application::FILE_DB_CONNECT);
    output('For now, game cant do update installation.`0`n');
}
