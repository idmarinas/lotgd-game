<?php

session_start();

if (isset($_GET['op']) && 'register' == $_GET['op'])
{
    $info = true;

    if (! isset($_SESSION['logdnet']) || ! isset($_SESSION['logdnet']['']) || '' == $_SESSION['logdnet'][''])
    {
        //register with LoGDnet
        $a = $_GET['a'];
        $c = $_GET['c'];
        $l = $_GET['l'];
        $d = $_GET['d'];
        $e = $_GET['e'];
        $v = $_GET['v'];
        $u = $_GET['u'];
        $url = $u. //central server
            'logdnet.php?'. //logdnet script
            'addy='.rawurlencode($a). //server URL
            '&desc='.rawurlencode($d). //server description
            '&version='.rawurlencode($v). //game version
            '&admin='.rawurlencode($e). //admin email
            '&c='.$c. // player count (for my own records, this isn't used
                      // in the sorting mechanism)
            '&v=2'.   // LoGDnet version.
            '&l='.$l. // primary language of this server -- you may change
                      // this if it turns out to be inaccurate.
            '';

        $info = @file($url);

        if (false !== $info)
        {
            $info = base64_decode(join('', $info));
            $_SESSION['logdnet'] = unserialize($info);
            $_SESSION['logdnet']['when'] = date('Y-m-d H:i:s');
            $_SESSION['logdnet']['note'] = "\n<!-- registered with logdnet successfully -->";
            $_SESSION['logdnet']['note'] .= "\n<!-- {$url} -->";
        }
        else
        {
            $_SESSION['logdnet']['when'] = date('Y-m-d H:i:s');
            $_SESSION['logdnet']['note'] = "\n<!-- There was trouble registering on logdnet. -->";
            $_SESSION['logdnet']['note'] .= "\n<!-- {$url} -->";
        }
    }

    if (false !== $info)
    {
        $o = $_SESSION['logdnet'][''];
        $refer = '';

        if (isset($_SERVER['HTTP_REFERER']))
        {
            $refer = $_SERVER['HTTP_REFERER'];
        }

        if (isset($_SESSION['session']['user']))
        {
            echo $_SESSION['logdnet']['note']."\n";
            echo "<!-- At {$_SESSION['logdnet']['when']} -->\n";

            echo sprintf($o,\preg_replace('/[`´]./u', '', $_SESSION['session']['user']['login']), htmlentities($_SESSION['session']['user']['login']).':'.$_SERVER['HTTP_HOST'].$refer, ENT_COMPAT, 'ISO-8859-1');

            exit;
        }
    }
}
elseif (isset($_SESSION['logdnet']))
{
    var_export($_SESSION['logdnet']);
    header('Content-Type: '.$_SESSION['logdnet']['content-type']);
    header('Content-Length: '.strlen($_SESSION['logdnet']['image']));
    echo $_SESSION['logdnet']['image'];

    exit;
}

$image = join('', file('paypal1.gif'));
header('Content-Type: image/gif');
header('Content-Length: '.strlen($image));
echo $image;
