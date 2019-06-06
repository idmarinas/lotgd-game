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
    elseif (0 == strcmp($bver, 'unknown') && 0 != strcmp($aver, 'unknown'))
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

$op = \LotgdHttp::getQuery('op');

if ('' == $op)
{
    $censor = \LotgdLocator::get(\Lotgd\Core\Output\Censor::class);

    $addy = (string) \LotgdHttp::getQuery('addy');
    $desc = (string) \LotgdHttp::getQuery('desc');
    $vers = (string) \LotgdHttp::getQuery('version');
    $admin = (string) \LotgdHttp::getQuery('admin');
    $count = (int) \LotgdHttp::getQuery('c') * 1;
    $lang = (string) \LotgdHttp::getQuery('l');

    $vers = $vers ?: 'Unknown';

    if ('' == $admin || 'postmaster@localhost.com' == $admin)
    {
        $admin = 'unknown';
    }

    // Clean up the desc
    $desc = \LotgdSanitize::logdnetSanitize($desc ?: '');
    $desc = $censor->filter($desc);

    $data = [
        'address' => $addy,
        'lang' => $lang,
        'count' => $count,
        'recentips' => \LotgdHttp::getServer('REMOTE_ADDR'),
        'description' => $desc,
        'version' => $vers,
        'admin' => $admin,
        'lastupdate' => new \DateTime('now'),
        'lastping' => new \DateTime('now')
    ];

    $repository = \Doctrine::getRepository('LotgdCore:Logdnet');
    $entity = $repository->findOneByAddress($addy);
    $newRow = (! $entity);
    $entity = $repository->hydrateEntity($data, $entity);

    $dateUpdate = new \DateTime('now');
    $dateUpdate->sub(new \DateInterval('PT1M'));

    // Only one update per minute allowed.
    if (! $newRow && $entity->getLastping() < $dateUpdate)
    {
        $entity->setPriority($entity->getPriority() + 1);
    }

    \Doctrine::persist($entity);
    \Doctrine::flush();

    //-- Deleted older server
    $repository->deletedOlderServer();

    //-- Degrade the popularity of any server which hasn't been updated in the past 5 minutes by 1%.
    $repository->degradePopularity();

    //Now, if we're using version 2 of LoGDnet, we'll return the appropriate code.
    $v = \LotgdHttp::getQuery('v');

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
    $repository = \Doctrine::getRepository('LotgdCore:Logdnet');
    $entities = $repository->getNetServerList();

    $entities = apply_logdnet_bans($entities);
    usort($entities, 'lotgdsort');

    bdump($entities);
    // Okay, they are now sorted, so output them
    foreach ($entities as $value)
    {
        $entity = serialize($value);
        echo $entity."\n";
    }
}
else
{
    page_header('title', [], 'page-logdnet');

    \LotgdNavigation::addHeader('common.category.login');
    \LotgdNavigation::addNav('common.nav.login', 'index.php');

    $params = ['servers' => []];

    $u = getsetting('logdnetserver', 'http://logdnet.logd.com/');

    if (! preg_match('/\\/$/', $u))
    {
        $u = $u.'/';
        savesetting('logdnetserver', $u);
    }
    $servers = file("${u}logdnet.php?op=net") ?: [];

    $filterChain = new Filter\FilterChain();
    $filterChain
        ->attach(new Filter\StringTrim())
        ->attach(new Filter\StripTags())
        ->attach(new Filter\StripNewlines())
    ;

    foreach ($servers as $key => $val)
    {
        $row = unserialize($val);

        // If we aren't given an address, continue on.
        if ('http://' != substr($row['address'], 0, 7) && 'https://' != substr($row['address'], 0, 8))
        {
            unset($servers[$key]);

            continue;
        }

        $row['description'] = iconv(mb_detect_encoding($row['description'], 'auto'), 'UTF-8//IGNORE', $row['description']);

        //-- Filter description
        $row['description'] = $filterChain->filter(stripslashes($row['description']));

        // Clean up the desc
        $row['description'] = \LotgdSanitize::logdnetSanitize($row['description'] ?: '');

        $servers[$key] = $row;
    }

    bdump($servers, 'Server list found');
    $params['servers'] = $servers;

    rawoutput(\LotgdTheme::renderThemeTemplate('page/logdnet.twig', $params));

    page_footer();
}

function apply_logdnet_bans($logdnet)
{
    $repository = \Doctrine::getRepository('LotgdCore:Logdnetbans');
    $entities = $repository->findAll();
    $entities = $repository->extractEntity($entities);

    foreach ($entities as $value)
    {
        foreach($logdnet as $key => $net)
        {
            if (preg_match("/{$value['banvalue']}/i", $net[$value['bantype']]))
            {
                unset($logdnet[$key]);
            }
        }
    }

    return $logdnet;
}
