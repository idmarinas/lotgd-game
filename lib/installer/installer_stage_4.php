<?php

$request = \LotgdLocator::get(\Lotgd\Core\Http::class);

if ($request->isPost())
{
    $session['installer']['dbinfo']['DB_DRIVER'] = (string) $request->getPost('DB_DRIVER', '');
    $session['installer']['dbinfo']['DB_HOST'] = (string) $request->getPost('DB_HOST', '');
    $session['installer']['dbinfo']['DB_USER'] = (string) $request->getPost('DB_USER', '');
    $session['installer']['dbinfo']['DB_PASS'] = (string) $request->getPost('DB_PASS', '');
    $session['installer']['dbinfo']['DB_NAME'] = (string) $request->getPost('DB_NAME', '');
}

//-- Settings for Database Adapter
$adapter = new Lotgd\Core\Db\Dbwrapper([
    'driver' => $session['installer']['dbinfo']['DB_DRIVER'],
    'hostname' => $session['installer']['dbinfo']['DB_HOST'],
    'database' => $session['installer']['dbinfo']['DB_NAME'],
    'charset' => 'utf8mb4',
    'collate' => 'utf8mb4_general_ci',
    'username' => $session['installer']['dbinfo']['DB_USER'],
    'password' => $session['installer']['dbinfo']['DB_PASS']
]);

//-- Configure DB
DB::wrapper($adapter);

$params = [
    'connected' => true,
    'test' => []
];

if (false === DB::connect())
{
    $params['dberror'] = DB::error();
    $params['connected'] = false;

    $session['installer']['stagecompleted'] = 3;
}
else
{
    //- Try to destroy the table if it's already here.
    DB::query('DROP TABLE IF EXISTS logd_environment_test', false);

    $issues = 0;//-- Count issues

    //-- Test create
    DB::query('CREATE TABLE logd_environment_test (a int(11) unsigned not null)');
    $params['test']['create'] = true;
    if ($error = DB::error())
    {
        $params['test']['create'] = $error;
        $issues++;
    }

    //-- Test alter
    DB::query('ALTER TABLE logd_environment_test CHANGE a b varchar(50) not null');
    $params['test']['alter'] = true;
    if ($error = DB::error())
    {
        $params['test']['alter'] = $error;
        $issues++;
    }

    //-- Test index
    DB::query('ALTER TABLE logd_environment_test ADD INDEX(b)');
    $params['test']['index'] = true;
    if ($error = DB::error())
    {
        $params['test']['index'] = $error;
        $issues++;
    }

    //-- Test insert
    DB::query("INSERT INTO logd_environment_test (b) VALUES ('testing')");
    $params['test']['insert'] = true;
    if ($error = DB::error())
    {
        $session['installer']['stagecompleted'] = 3;
        $params['test']['insert'] = $error;
        $issues++;
    }

    //-- Test select
    DB::query('SELECT * FROM logd_environment_test');
    $params['test']['select'] = true;
    if ($error = DB::error())
    {
        $session['installer']['stagecompleted'] = 3;
        $params['test']['select'] = $error;
        $issues++;
    }


    //-- Test update
    DB::query("UPDATE logd_environment_test SET b='MightyE'");
    $params['test']['update'] = true;
    if ($error = DB::error())
    {
        $session['installer']['stagecompleted'] = 3;
        $params['test']['update'] = $error;
        $issues++;
    }

    //-- Test delete
    DB::query('DELETE FROM logd_environment_test');
    $params['test']['delete'] = true;
    if ($error = DB::error())
    {
        $session['installer']['stagecompleted'] = 3;
        $params['test']['delete'] = $error;
        $issues++;
    }

    //-- Test lock
    DB::query('LOCK TABLES logd_environment_test WRITE');
    $params['test']['lock'] = true;
    if ($error = DB::error())
    {
        $session['installer']['stagecompleted'] = 3;
        $params['test']['lock'] = $error;
        $issues++;
    }

    //-- Test unlock
    DB::query('UNLOCK TABLES');
    $params['test']['unlock'] = true;
    if ($error = DB::error())
    {
        $session['installer']['stagecompleted'] = 3;
        $params['test']['unlock'] = $error;
        $issues++;
    }

    //-- Test drop
    DB::query('DROP TABLE logd_environment_test');
    $params['test']['drop'] = true;
    if ($error = DB::error())
    {
        $params['test']['unlock'] = $error;
        $issues++;
    }

    //-- Test cache
    $config = LotgdLocator::get('GameConfig');
    $cacheDir = $options['lotgd_core']['cache']['base_cache_dir'] ?? 'storage/cache';

    $params['test']['cache'] = true;
    $fp = @fopen("{$cacheDir}/dummy.php", 'w+');

    if ($fp)
    {
        if (false === fwrite($fp, 'Dummy test'))
        {
            $params['test']['cache'] = false;
            $issues++;
        }
        fclose($fp);
        @unlink("{$cacheDir}/dummy.php");
    }
    else
    {
        $params['test']['cache'] = false;
        $issues++;
    }

    $params['issues'] = $issues;
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/installer/stage-4.twig', $params));
