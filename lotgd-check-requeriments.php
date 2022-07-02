<?php

    // ╔═╗┬ ┬┌─┐┌─┐┬┌─  ┬─┐┌─┐┌─┐ ┬ ┬┌─┐┬─┐┬┌┬┐┌─┐┌┐┌┌┬┐┌─┐  ┌─┐┌─┐┬─┐  ╦  ┌─┐╔╦╗╔═╗╔╦╗  ╦  ╦┌─┐┬─┐┌─┐┬┌─┐┌┐┌┌─┐
    // ║  ├─┤├┤ │  ├┴┐  ├┬┘├┤ │─┼┐│ │├┤ ├┬┘││││├┤ │││ │ └─┐  ├┤ │ │├┬┘  ║  │ │ ║ ║ ╦ ║║  ╚╗╔╝├┤ ├┬┘└─┐││ ││││└─┐
    // ╚═╝┴ ┴└─┘└─┘┴ ┴  ┴└─└─┘└─┘└└─┘└─┘┴└─┴┴ ┴└─┘┘└┘ ┴ └─┘  └  └─┘┴└─  ╩═╝└─┘ ╩ ╚═╝═╩╝   ╚╝ └─┘┴└─└─┘┴└─┘┘└┘└─┘

    class CustomMysqli extends mysqli
    {
        public function __construct()
        {
            parent::init();
            parent::options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);

            //-- You can change second and third param to connect your DataBase
            //-- second param => is the username for your DataBase
            //-- third param => is the password for your DataBase
            //-- four param => is the name of DataBase for queries
            parent::__construct('localhost', 'root');
        }
    }

    $_memoryLimit = @\ini_get('memory_limit');
    $memoryLimit  = return_bytes($_memoryLimit);

    //-- Extensions
    $extensions = [
        'bcmath'    => 'BC Math',
        'curl'      => 'cURL',
        'exif'      => 'Exif',
        'gd'        => 'GD',
        'intl'      => 'Intl',
        'json'      => 'Json',
        'mbstring'  => 'Multibyte String',
        'pdo'       => 'PDO',
        'pdo_mysql' => 'PDO MySQL',
        'session'   => 'Session',
        'ctype'     => 'Ctype',
        'iconv'     => 'Iconv',
    ];

    $default = [
        'php_version'    => '7.3.0',
        'memory_limit'   => 128 * 1024 * 1024, //-- Need 128 MB
        'execution_time' => 60, //-- 60 Seconds
        'extensions'     => $extensions,
        'mysql_version'  => '5.5.3',
    ];

    $requeriments = [
        //-- LotgdVersion => Requeriments
        '5.0' => $default,
        '7.1' => array_merge($default, ['php_version' => '7.4.0'])
    ];

    $results = [];

    $extensionsFullList = [];

    //-- Process requirements
    foreach ($requeriments as $lotgdVersion => $reqs)
    {
        $results[$lotgdVersion] = [];
        $reqResults             = [];
        $isPassed               = true;

        //-- Extract a list of extension for all versions
        $extensionsFullList = \array_merge($extensionsFullList, $reqs['extensions']);

        foreach ($reqs as $reqName => $value)
        {
            $res = $reqName($value);

            if ( ! $res['isPassed'])
            {
                $isPassed = false;
            }

            $reqResults[$reqName] = $res;
        }

        $results[$lotgdVersion] = [
            'isPassed' => $isPassed,
            'details'  => $reqResults,
        ];
    }

    \asort($extensionsFullList, SORT_STRING);

    //-- Compare PHP versions
    function php_version($version): array
    {
        return [
            'isPassed' => (\version_compare(PHP_VERSION, $version) >= 0),
            'current'  => PHP_VERSION,
            'need'     => $version,
        ];
    }

    //-- Check memory limit
    function memory_limit($value): array
    {
        global $_memoryLimit, $memoryLimit;

        return [
            'isPassed' => (-1 == $_memoryLimit || $memoryLimit >= $value),
            'current'  => $_memoryLimit,
            'need'     => format_bytes($value),
        ];
    }

    //-- Check execution time
    function execution_time($value): array
    {
        $executionTime = @\ini_get('max_execution_time');

        return [
            'isPassed' => $executionTime >= $value || 0 == $executionTime,
            'current'  => $executionTime,
            'need'     => $value,
        ];
    }

    //-- Check Extensions
    function extensions($extensions): array
    {
        $results  = [];
        $isPassed = true;

        foreach ($extensions as $ext => $name)
        {
            if ( ! \extension_loaded($ext))
            {
                $isPassed = false;
            }

            $results[$ext] = [
                'isPassed' => \extension_loaded($ext),
                'name'     => $name,
            ];
        }

        return [
            'isPassed' => $isPassed,
            'details'  => $results,
        ];
    }

    //-- Check MySQL Version
    function mysql_version($version): array
    {
        $mysql = @new CustomMysqli();

        return [
            'isError'  => (bool) $mysql->connect_errno,
            'isPassed' => $mysql->connect_errno ? true : \version_compare($mysql->server_info, $version) >= 0,
            'current'  => $mysql->connect_errno ? 'unknown' : $mysql->server_info,
            'need'     => $version,
        ];
    }

    function return_bytes($val)
    {
        $val  = \trim($val);
        $last = \strtolower($val[\strlen($val) - 1]);
        $val  = \substr($val, 0, -1);

        switch ($last)
        {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
                // no break
            case 'm':
                $val *= 1024;
                // no break
            case 'k':
                $val *= 1024;
                // no break
            default: break;
        }

        return $val;
    }

    function format_bytes($size, $precision = 0)
    {
        $base     = \log($size, 1024);
        $suffixes = ['', 'K', 'M', 'G', 'T'];

        return \round(\pow(1024, $base - \floor($base)), $precision).' '.$suffixes[\floor($base)];
    }

    $lotgdVersions = \array_keys($results);

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
                p.fail, p.advisory {
                    padding: 15px 15px 15px 45px;
                    border-radius: 2px;
                    position: relative;
                    color: #fff;
                    font-size: 14px;
                }
                p.fail:before {
                    font-family: 'FontAwesome';
                    position: absolute;
                    top: 15px;
                    left: 15px;
                    font-size: 18px;
                    content: '\f057';
                }
                p.fail {

                    background: #b52b38;
                }
                p.fail a {
                    color: #fff;
                    border-bottom: 1px dotted #fff;
                    text-decoration: none;
                }
                p.advisory {
                    background: #E09515;
                }
                p.advisory:before {
                    font-family: 'FontAwesome';
                    content: '\f05a';
                    top: 15px;
                    left: 15px;
                    font-size: 18px;
                    position: absolute;
                }

                table {
                    width: auto;
                    margin: 0 auto;
                    /* background: #fff; */
                    background: #003800;
                    /* border: 1px solid rgba(34,36,38,.15); */
                    /* -webkit-box-shadow: none; */
                    /* box-shadow: none; */
                    /* border-radius: .28571429rem; */
                    text-align: center;
                    vertical-align: middle;
                    color: rgba(255,255,255,.87);
                    border-collapse: separate;
                    border-spacing: 0;
                }

                table tr th, table tr td
                {
                    border-left: 1px solid rgba(34,36,38,.7);
                    border-top: 1px solid rgba(34,36,38,.7);
                    /* border: 1px solid rgba(34,36,38,.7); */
                    /* border-bottom: 1px solid rgba(34,36,38,.15); */
                    padding: .78571429em .78571429em;
                    text-align: inherit;
                    color: rgba(255,255,255,.87);
                    /* border-bottom: 1px solid rgba(34,36,38,.7); */
                }
                table tr th:last-child, table tr td:last-child
                {
                    /* border-left: 0px; */
                    border-right: 1px solid rgba(34,36,38,.7);
                }
                table tr:last-child th, table tr:last-child td
                {
                    /* border-left: 0px; */
                    border-bottom: 1px solid rgba(34,36,38,.7);
                }
                table tbody tr:first-child td, table tbody tr:first-child th
                {
                    /* border-left: 0px; */
                    border-top: 0;
                }

                table tr td.success, table tr td.fail, table tr td.warning {
                    padding-left: calc(inherit + 15px);
                    border-radius: 2px;
                    position: relative;
                    color: #fff;
                    font-size: 14px;
                }
                table tr td.success:before, table tr td.fail:before, table tr td.warning:before {
                    font-family: 'FontAwesome';
                    position: relative;
                    margin-right: 5px;
                    /* font-size: 18px; */
                }
                table tr td.fail {
                    background: #b52b38;
                }
                table tr td.fail:before {
                    content: '\f057';
                }
                table tr td.warning {
                    background: #E09515;
                }
                table tr td.warning:before {
                    content: '\f05a';
                }
                table tr td.success {
                    background: #62874d;
                }
                table tr td.success:before {
                    content: '\f00c';
                }
                table tr th.smaller {
                    font-size: 12px;
                }
                table > thead > tr > th.empty:first-child {
                    background: #194b19;
                    pointer-events: none;
                    font-weight: 400;
                    color: rgba(0,0,0,.4);
                    border: 0px;
                    border-left: 0px;
                }
                table > tbody > tr > th
                {
                    text-align: right;
                }
        </style>
    </head>

    <body>
        <h1 id="header">
            <img alt="" src="data:image/gif;base64,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" />
            Final conclusions
        </h1>
        <div id="main">
            <table>
                <caption>Fail/Pass for LoTGD versions in this server.</caption>
                <thead>
                    <tr>
                        <tr>
                            <th scope="colgroup" colspan="<?= count($lotgdVersions) ?>">LoTGD Version</th>
                        </tr>
                        <tr>
                            <?php foreach($lotgdVersions as $version): ?>
                                <th scope="col"><?= $version ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php $hasFail = false; ?>
                        <?php foreach($lotgdVersions as $version): ?>
                            <td class="<?= $results[$version]['isPassed'] ? 'success' : 'fail' ?>">
                                <?php if (!$results[$version]['isPassed']): ?>
                                    <?php $hasFail = true; ?>
                                <?php endif; ?>
                                <?= $results[$version]['isPassed'] ? 'Success' : 'Fail' ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
            <p>The table indicates for each version if the requirements for installing Legend of the Green Dragon on this server are met.</p>
            <p>The version indicated is the version in which the requirements are changed, so if you pass the tests for one version you will also pass them for the next, until otherwise indicated.</p>
            <?php if ($hasFail): ?>
                <p class="fail">See the information below for instructions how to fix or <a rel="noopener noreferrer" href="https://github.com/idmarinas/lotgd-game/issues" target="_blank">create issue</a> for additional assistance.</p>
            <?php endif; ?>
        </div>
        <h1 id="header">
            <img alt="" src="data:image/gif;base64,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" />
            Legend of the Green Dragon Requirements
        </h1>
        <div id="main">
            <section>
                <table>
                    <caption><h2>PHP Requirements</h2></caption>
                    <thead>
                        <tr>
                            <th scope="col" class="empty"></th>
                            <th scope="colgroup" colspan="<?= count($lotgdVersions) ?>">LoTGD Version</th>
                        </tr>
                        <tr>
                            <th scope="col">Requeriment</th>
                            <?php foreach($lotgdVersions as $version): ?>
                                <th scope="col"><?= $version ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="col" class="smaller">PHP version</th>
                            <?php foreach($lotgdVersions as $version): ?>
                                <?php $data = $results[$version]['details']['php_version'] ?>
                                <td class="<?= $data ['isPassed'] ? 'success' : 'fail' ?>">
                                    <?php if ($data ['isPassed']): ?>
                                        Success
                                    <?php else: ?>
                                        Need <?= $data['need'] ?><br>
                                        Server <?= $data['current'] ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th scope="col" class="smaller">Memory limit</th>
                            <?php foreach($lotgdVersions as $version): ?>
                                <?php $data = $results[$version]['details']['memory_limit'] ?>
                                <td class="<?= $data ['isPassed'] ? 'success' : 'fail' ?>">
                                    <?php if ($data ['isPassed']): ?>
                                        Success
                                    <?php else: ?>
                                        Need <?= $data['need'] ?><br>
                                        Server <?= $data['current'] ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th scope="col" class="smaller">Max execution time</th>
                            <?php foreach($lotgdVersions as $version): ?>
                                <?php $data = $results[$version]['details']['execution_time'] ?>
                                <td class="<?= $data ['isPassed'] ? 'success' : 'fail' ?>">
                                    <?php if ($data ['isPassed']): ?>
                                        Success
                                    <?php else: ?>
                                        Need <?= $data['need'] ?><br>
                                        Server <?= $data['current'] ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <thead>
                                <tr>
                                    <th scope="col">PHP Extensions</th>
                                    <?php foreach($lotgdVersions as $version): ?>
                                        <th scope="col"><?= $version ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                        </tr>
                        <?php foreach($extensionsFullList as $ext => $name): ?>
                            <tr>
                                <th scope="col" class="smaller"><?= $name ?></th>
                                <?php foreach($lotgdVersions as $version): ?>
                                    <?php if (isset($results[$version]['details']['extensions']['details'][$ext])): ?>
                                        <?php $data = $results[$version]['details']['extensions']['details'][$ext]  ?>
                                        <td class="<?= $data['isPassed'] ? 'success' : 'fail' ?>"><?= $data['isPassed'] ? 'Success' : 'Fail' ?></td>
                                    <?php else: ?>
                                        <td>---</td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
            <hr>
            <section>
                <table>
                    <caption><h2>MySQL Requirements</h2></caption>
                    <thead>
                        <tr>
                            <th scope="colgroup" colspan="<?= count($lotgdVersions) ?>">LoTGD Version</th>
                        </tr>
                        <tr>
                            <?php foreach($lotgdVersions as $version): ?>
                                <th scope="col"><?= $version ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <?php $hasError = false ?>
                            <?php foreach($lotgdVersions as $version): ?>
                                <?php $data = $results[$version]['details']['mysql_version'] ?>
                                <?php if ($data['isError']): ?>
                                    <?php $hasError = true ?>
                                    <td class="warning">Warning</td>
                                <?php else: ?>
                                    <td class="<?= $data ['isPassed'] ? 'success' : 'fail' ?>">
                                        <?php if ($data ['isPassed']): ?>
                                            Success
                                        <?php else: ?>
                                        Need <?= $data['need'] ?><br>
                                        Server <?= $data['current'] ?>
                                    <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
                <?php if ($hasError): ?>
                    <p class="advisory">MySQL connection could not be established to perform version check</p>
                <?php endif; ?>
            </section>
        </div>
    </body>
</html>
