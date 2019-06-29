<?php

// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

tlschema('source');

$textDomain = 'page-source';

$params = [
    'textDomain' => $textDomain
];

popup_header('title', [], $textDomain);

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/source.twig', $params));

tlschema();

popup_footer();
