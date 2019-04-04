<?php
// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/datetime.php';

tlschema('account');

$textDomain = 'page-account';

page_header('title', [], $textDomain);

checkday();

\LotgdNavigation::addHeader('account.category.navigation');
\LotgdNavigation::villageNav();

\LotgdNavigation::addHeader('account.category.actions');
\LotgdNavigation::addNav('account.nav.refresh', 'account.php');

$user = $session['user'];

$dragonpointssummary = [];

if ($user['dragonkills'] > 0)
{
    $dragonpointssummary = array_count_values($user['dragonpoints']);
}

//-- Add more statistics using templates
$tpl = modulehook('accountstats', ['templates' => []]);

$params = [
    'dragonpoints' => $dragonpointssummary,
    'templates' => $tpl['templates']
];

$params = modulehook('page-account-tpl-params', $params);
rawoutput(LotgdTheme::renderThemeTemplate('page/account.twig', $params));

tlschema();

page_footer();
