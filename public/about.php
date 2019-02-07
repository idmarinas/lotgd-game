<?php

// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);
require_once 'common.php';
require_once 'lib/showform.php';

tlschema('about');

page_header(LotgdTranslator::t('title', [], 'page-about'));

checkday();
$op = LotgdHttp::getQuery('op');

if ($session['user']['loggedin'])
{
    LotgdNavigation::addNav('common.nav.news', 'news.php');
}
else
{
    LotgdNavigation::addHeader('common.category.login');
    LotgdNavigation::addNav('common.nav.login', 'index.php');
}

LotgdNavigation::addHeader('about.category.about');
LotgdNavigation::addNav('about.nav.about', 'about.php');
LotgdNavigation::addNav('about.nav.setup', 'about.php?op=setup');
LotgdNavigation::addNav('about.nav.module', 'about.php?op=listmodules');
LotgdNavigation::addNav('about.nav.license', 'about.php?op=license');

$params = [];
if ('listmodules' == $op)
{
    LotgdNavigation::blockLink('about.php?op=listmodules');

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Modules::class);
    $params['result'] = $repository->findBy(['active' => 1], ['category' => 'ASC', 'formalname' => 'ASC']);

    $params = modulehook('page-about-modules-tpl-params', $params);
    rawoutput(LotgdTheme::renderThemeTemplate('pages/about/modules.twig', $params));
}
elseif ('setup' == $op)
{
    LotgdNavigation::blockLink('about.php?op=setup');

    $details = gametimedetails();
    $setup = [
        'Game Setup,title',
        'pvp' => 'Enable Slay Other Players,viewonly',
        'pvpday' => 'Player Fights per day,viewonly',
        'pvpimmunity' => 'Days that new players are safe from PvP,viewonly',
        'pvpminexp' => 'Amount of experience when players become killable in PvP,viewonly',
        'soap' => 'Clean user posts (filters bad language and splits words over 45 chars long),viewonly',
        'newplayerstartgold' => 'Amount of gold to start a new character with,viewonly',
        'New Days,title',
        'fightsforinterest' => 'Player must have fewer than how many forest fights to earn interest?,viewonly',
        'maxinterest' => 'Max Interest Rate (%),viewonly',
        'mininterest' => 'Min Interest Rate (%),viewonly',
        'daysperday' => 'Game days per calendar day,viewonly',
        'specialtybonus' => 'Extra daily uses in specialty area,viewonly',

        'Bank settings,title',
        'borrowperlevel' => 'Max amount player can borrow per level,viewonly',
        'allowgoldtransfer' => 'Are players allowed to transfer gold,viewonly',
        'transferperlevel' => 'Max amount player can transfer per level of recipient (if transfers are enabled),viewonly',
        'mintransferlev' => 'Minimum level a player has to be before they can transfer gold (if transfers are enabled),viewonly',
        'transferreceive' => 'Total transfers a player can receive in one play day (if transfers are enabled),viewonly',
        'maxtransferout' => 'Max amount total a player can transfer to others per level (if transfers are enabled),viewonly',

        'Forest,title',
        'turns' => 'Forest Fights per day,viewonly',
        'dropmingold' => 'Forest Creatures always drop at least 1/4 of possible gold,viewonly',

        'Mail Settings,title',
        'mailsizelimit' => 'Message size limit per message,viewonly',
        'inboxlimit' => 'Limit # of messages in inbox,viewonly',
        'oldmail' => 'Automatically delete old messages after (days),viewonly',

        'Content Expiration,title',
        'expirecontent' => 'Days to keep comments and news?  (0 for infinite),viewonly',
        'expiretrashacct' => 'Days to keep accounts that were never logged in to? (0 for infinite),viewonly',
        'expirenewacct' => 'Days to keep level 1 accounts with no dragon kills? (0 for infinite),viewonly',
        'expireoldacct' => 'Days to keep all other accounts? (0 for infinite),viewonly',
        'LOGINTIMEOUT' => 'Seconds of inactivity before auto-logoff,viewonly'
    ];

    $useful = [
        'Useful Information,title',
        'dayduration' => 'Day Duration,viewonly',
        'curgametime' => 'Current game time,viewonly',
        'curservertime' => 'Current Server Time,viewonly',
        'lastnewday' => 'Last new day,viewonly',
        'nextnewday' => 'Next new day,viewonly'
    ];

    $secstonextday = secondstonextgameday($details);
    $useful_vals = [
        'dayduration' => round(($details['dayduration'] / 60 / 60), 0).' hours',
        'curgametime' => getgametime(),
        'curservertime' => date('Y-m-d h:i:s a'),
        'lastnewday' => date('h:i:s a', strtotime("-{$details['realsecssofartoday']} seconds")),
        'nextnewday' => date('h:i:s a', strtotime("+{$details['realsecstotomorrow']} seconds")).' ('.date('H\\h i\\m s\\s', $secstonextday).')'
    ];

    $localsettings = $settings->getArray();

    $args = ['settings' => [], 'values' => []];
    $args = modulehook('showsettings', $args);
    $form = array_merge($setup, $args['settings'], $useful);
    $vals = array_merge($localsettings, $args['values'], $useful_vals);

    $params['form'] = lotgd_showform($form, $vals, true, false, false);

    $params = modulehook('page-about-setup-tpl-params', $params);
    rawoutput(LotgdTheme::renderThemeTemplate('pages/about/setup.twig', $params));
}
elseif ('license' == $op)
{
    LotgdNavigation::blockLink('about.php?op=license');

    $params = modulehook('page-about-license-tpl-params', $params);
    rawoutput(LotgdTheme::renderThemeTemplate('pages/about/license.twig', $params));
}
else
{
    LotgdNavigation::blockLink('about.php');

    $results = modulehook('about', ['messages' => []]);
    if(count($results['messages']))
    {
        $params['hookHomeMiddle'] = $results['messages'];
    }

    $params = modulehook('page-about-tpl-params', $params);
    rawoutput(LotgdTheme::renderThemeTemplate('pages/about.twig', $params));
}

page_footer();
