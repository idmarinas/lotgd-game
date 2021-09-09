<?php

\define('ALLOW_ANONYMOUS', false);
\define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

$search = (string) \LotgdRequest::getQuery('search');

$repository = \Doctrine::getRepository('LotgdCore:Avatar');

$result = $repository->findLikeName($search, 15);

$characters = [];
foreach ($result as $char)
{
    $superuser = ($char['superuser'] & SU_GIVES_YOM_WARNING) && ! ($char['superuser'] & SU_OVERRIDE_YOM_WARNING);

    $characters[] = [
        'value'     => $char['acctid'],
        'icon'      => ($char['loggedin'] ? 'green' : 'red').' '.($superuser ? 'user secret' : 'user'),
        'name'      => \LotgdSanitize::fullSanitize($char['name']),
        'superuser' => $superuser,
    ];
}

echo json_encode([
    'success' => (bool) \count($characters),
    'results' => $characters,
]);

// fields: {
//     remoteValues : 'results',    // grouping for api results
//     values       : 'values',     // grouping for all dropdown values
//     disabled     : 'disabled',   // whether value should be disabled
//     name         : 'name',       // displayed dropdown text
//     value        : 'value',      // actual dropdown value
//     text         : 'text',       // displayed text when selected
//     type         : 'type',       // type of dropdown element
//     image        : 'image',      // optional image path
//     imageClass   : 'imageClass', // optional individual class for image
//     icon         : 'icon',       // optional icon name
//     iconClass    : 'iconClass',  // optional individual class for icon (for example to use flag instead)
//     class        : 'class',      // optional individual class for item/header
//     divider      : 'divider'     // optional divider append for group headers
//   }
