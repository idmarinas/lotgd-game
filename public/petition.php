<?php

// addnews ready
// translator ready
// mail ready
define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

$op = (string) \LotgdHttp::getQuery('op');

$textDomain = 'page-petition';
$repository = \Doctrine::getRepository('LotgdCore:Petitions');

$params = [
    'textDomain' => $textDomain,
    'daysPerDay' => getsetting('daysperday', 2)
];

popup_header('title.default', [], $textDomain);

if ('' == $op || 'submit' == $op)
{
    $params['tpl'] = 'default';

    $params['multimaster'] = (int) getsetting('multimaster', 1);

    require 'lib/petition/petition_default.php';
}
elseif ('faq' == $op)
{
    $params['tpl'] = 'faq';

    popup_header('title.faq', [], $textDomain);

    $params['faqList'] = modulehook('faq-toc', [
        [
            'href' => 'petition.php?op=primer',
            'link' => [
                'section.faq.toc.primer',
                [],
                $textDomain
            ]
        ],
        [
            'href' => 'petition.php?op=faq1',
            'link' => [
                'section.faq.toc.general',
                [],
                $textDomain
            ]
        ],
        [
            'href' => 'petition.php?op=faq2',
            'link' => [
                'section.faq.toc.spoiler',
                [],
                $textDomain
            ]
        ],
        [
            'href' => 'petition.php?op=faq3',
            'link' => [
                'section.faq.toc.technical',
                [],
                $textDomain
            ]
        ]
    ]);
}
elseif ('faq1' == $op)
{
    $params['tpl'] = 'faq1';

    popup_header('title.faq1', [], $textDomain);
}
elseif ('faq2' == $op)
{
    $params['tpl'] = 'faq2';

    popup_header('title.faq2', [], $textDomain);
}
elseif ('faq3' == $op)
{
    $params['tpl'] = 'faq3';

    popup_header('title.faq3', [], $textDomain);
}
elseif ('primer' == $op)
{
    $params['tpl'] = 'primer';

    popup_header('title.primer', [], $textDomain);

    $params['deathOverlord'] = getsetting('deathoverlord', '`$Ramius`0');
    $params['pvp'] = getsetting('pvp', 1);
    $params['pvpImmunity'] = getsetting('pvpimmunity', 5);
    $params['pvpMinExp'] = getsetting('pvpminexp', 1500);
    $params['pvpDeflose'] = getsetting('pvpdeflose', 5);
    $params['pvpAttGain'] = getsetting('pvpattgain', 10);
    $params['pvpAttLose'] = getsetting('pvpattlose', 15);
    $params['pvpDefGain'] = getsetting('pvpdefgain', 10);
}

$params = modulehook('page-petition-tpl-params', $params);
rawoutput(LotgdTheme::renderThemeTemplate('page/petition.twig', $params));

popup_footer();
