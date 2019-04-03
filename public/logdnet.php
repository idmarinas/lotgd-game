<?php

// translator ready
// addnews ready
// mail ready

define('ALLOW_ANONYMOUS', true);

if (! isset($_GET['op']) || 'list' != $_GET['op'])
{
    //don't want people to be able to visit the list while logged in -- breaks their navs.
    define('OVERRIDE_FORCED_NAV', true);
}
require_once 'common.php';
require_once 'lib/sanitize.php';

use Zend\Filter;

function lotgdsort($a, $b)
{
    // $a and $b are table rows.

    $versions = new \Lotgd\Core\Installer\Install();
    $official_prefixes = $versions->getAllVersions();
    unset($official_prefixes['-1']);
    $official_prefixes = array_keys($official_prefixes);

    $aver = strtolower(str_replace(' ', '', $a['version']));
    $bver = strtolower(str_replace(' ', '', $b['version']));

    // Okay, if $a and $b are the same version, use the priority
    // This is true whether or not they are the official version or not.
    // We bubble the official version to the top below.
    if (0 == strcmp($aver, $bver))
    {
        if ($a['priority'] == $b['priority'])
        {
            return 0;
        }

        return ($a['priority'] < $b['priority']) ? 1 : -1;
    }

    // Unknown versions are always worse than non-unknown
    if (0 == strcmp($aver, 'unknown') && 0 != strcmp($bver, 'unknown'))
    {
        return 1;
    }

    if (0 == strcmp($bver, 'unknown') && 0 != strcmp($aver, 'unknown'))
    {
        return -1;
    }

    // Check if either of them are a prefix.
    $costa = 10000;
    $costb = 10000;

    foreach ($official_prefixes as $index => $value)
    {
        if (0 == strncmp($aver, $value, strlen($value)) && 10000 == $costa)
        {
            $costa = $index;
        }

        if (0 == strncmp($bver, $value, strlen($value)) && 10000 == $costb)
        {
            $costb = $index;
        }
    }

    // If both are the same prefix (or no prefix), just strcmp.
    if ($costa == $costb)
    {
        return strcmp($aver, $bver);
    }

    return ($costa < $costb) ? -1 : 1;
}

tlschema('logdnet');

$op = httpget('op');

if ('' == $op)
{
    $addy = httpget('addy');
    $desc = httpget('desc');
    $vers = httpget('version');
    $admin = httpget('admin');
    $count = httpget('c') * 1;
    $lang = httpget('l');

    $vers = $vers ?: 'Unknown';

    if ('' == $admin || 'postmaster@localhost.com' == $admin)
    {
        $admin = 'unknown';
    }

    // See if we know this server.
    $sql = 'SELECT lastupdate,serverid,lastping,recentips FROM '.DB::prefix('logdnet')." WHERE address='".DB::quoteValue($addy)."'";
    $result = DB::query($sql);
    $row = DB::fetch_assoc($result);

    // Clean up the desc
    $desc = logdnet_sanitize($desc);
    $desc = soap($desc);
    // Limit descs to 75 characters.
    if (strlen($desc) > 75)
    {
        $desc = substr($desc, 0, 75);
    }

    $date = date('Y-m-d H:i:s');

    if (DB::num_rows($result) > 0)
    {
        // This is an already known server.

        // TEMP hack for IPs
        $ips = $_SERVER['REMOTE_ADDR'];
        // Only one update per minute allowed.
        if (strtotime($row['lastping']) < strtotime('-1 minutes'))
        {
            // Increase the popularity of this server
            $sql = 'UPDATE '.DB::prefix('logdnet')." SET lang='$lang',count='$count',recentips='$ips',priority=priority+1,description='$desc',version='$vers',admin='$admin',lastupdate='$date',lastping='$date' WHERE serverid={$row['serverid']}";
            DB::query($sql);
        }
    }
    else
    {
        // This is a new server, so add it and give it a small priority boost.
        $sql = 'INSERT INTO '.DB::prefix('logdnet')." (address,description,version,admin,priority,lastupdate,lastping,count,recentips,lang) VALUES ('$addy','$desc','$vers','$admin',10,'$date','$date','$count','{$_SERVER['REMOTE_ADDR']}','$lang')";
        $result = DB::query($sql);
    }

    // Do these next two things whether we've added a new server or
    // updated an old one

    // Delete servers older than a week
    $sql = 'DELETE FROM '.DB::prefix('logdnet')." WHERE lastping < '".date('Y-m-d H:i:s', strtotime('-2 weeks'))."'";
    DB::query($sql);

    // Degrade the popularity of any server which hasn't been updated in the
    // past 5 minutes by 1%.  This means that unpopular servers will fall
    // toward the bottom of the list.
    $since = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $sql = 'UPDATE '.DB::prefix('logdnet')." SET priority=priority*0.99,lastupdate='".date('Y-m-d H:i:s')."' WHERE lastupdate < '$since'";
    DB::query($sql);

    //Now, if we're using version 2 of LoGDnet, we'll return the appropriate code.
    $v = httpget('v');

    if ((int) $v >= 2)
    {
        $currency = getsetting('paypalcurrency', 'USD');
        $info = [];
        $info[''] = '<!--data from '.$_SERVER['HTTP_HOST'].'-->
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="logd@mightye.org">
        <input type="hidden" name="item_name" value="Legend of the Green Dragon Author Donation from %s">
        <input type="hidden" name="item_number" value="%s">
        <input type="hidden" name="no_shipping" value="1">
        <input type="hidden" name="notify_url" value="http://lotgd.net/payment.php">
        <input type="hidden" name="cn" value="Your Character Name">
        <input type="hidden" name="cs" value="1">
        <input type="hidden" name="currency_code" value="'.$currency.'">
        <input type="hidden" name="tax" value="0">
        <input type="image" src="images/logdnet.php" border="0" width="62" height="57" name="submit" alt="Donate!">
        </form>';
        $info['image'] = join('', file('images/paypal1.gif'));
        $info['content-type'] = 'image/gif';

        echo base64_encode(serialize($info));
    }
}
elseif ('net' == $op)
{
    // Someone is requesting our list of servers, so give it to them.

    // I'm going to do a slightly niftier sort manually in a bit which always
    // pops the most recent 'official' versions to the top of the list.
    $sql = 'SELECT address,description,version,admin,priority FROM '.DB::prefix('logdnet')." WHERE lastping > '".date('Y-m-d H:i:s', strtotime('-7 days'))."'";
    $result = DB::query($sql);
    $rows = [];
    $number = DB::num_rows($result);

    for ($i = 0; $i < $number; $i++)
    {
        $rows[] = DB::fetch_assoc($result);
    }
    $rows = apply_logdnet_bans($rows);
    usort($rows, 'lotgdsort');

    // Okay, they are now sorted, so output them
    for ($i = 0; $i < count($rows); $i++)
    {
        $row = serialize($rows[$i]);
        echo $row."\n";
    }
}
else
{
    require_once 'lib/pullurl.php';

    page_header('title', [], 'page-logdnet');

    LotgdNavigation::addHeader('common.category.login');
    LotgdNavigation::addNav('common.nav.login', 'index.php');

    $params = ['servers' => []];

    $u = getsetting('logdnetserver', 'http://logdnet.logd.com/');

    if (! preg_match('/\\/$/', $u))
    {
        $u = $u.'/';
        savesetting('logdnetserver', $u);
    }
    $servers = pullurl("${u}logdnet.php?op=net") ?: [];

    bdump($servers);

    $filterChain = new Filter\FilterChain();
    $filterChain
        ->attach(new Filter\StringTrim())
        ->attach(new Filter\StripTags())
        ->attach(new Filter\StripNewlines())
        // ->attach(new Filter\HtmlEntities())
    ;

    while (list($key, $val) = each($servers))
    {
        $row = unserialize($val);

        // If we aren't given an address, continue on.
        if ('http://' != substr($row['address'], 0, 7) && 'https://' != substr($row['address'], 0, 8))
        {
            continue;
        }

        $row['address'] = htmlentities($row['address'], ENT_COMPAT, getsetting('charset', 'UTF-8'));

        // Give undescribed servers a boring descriptionn
        $row['description'] = $filterChain->filter(stripslashes($row['description']));

        // Clean up the desc
        $row['description'] = soap(logdnet_sanitize($row['description']));
        // Limit descs to 75 characters.
        if (strlen($row['description']) > 75)
        {
            $row['description'] = substr($row['description'], 0, 75);
        }

        $row['description'] = str_replace('`&amp;', '`&', $row['description']);

        // Correct for old logdnet servers
        $row['version'] = $row['version'] ?: 'Unknown';

        $params['servers'][] = $row;
    }

    rawoutput(LotgdTheme::renderThemeTemplate('page/logdnet.twig', $params));

    page_footer();
}

function apply_logdnet_bans($logdnet)
{
    $sql = 'SELECT * FROM '.DB::prefix('logdnetbans');
    $result = DB::query($sql, 'logdnetbans');

    while ($row = DB::fetch_assoc($result))
    {
        reset($logdnet);

        while (list($i, $net) = each($logdnet))
        {
            if (preg_match("/{$row['banvalue']}/i", $net[$row['bantype']]))
            {
                unset($logdnet[$i]);
            }
        }
    }

    return $logdnet;
}
