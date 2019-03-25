<?php

require_once 'lib/pullurl.php';

$licenseview = implode('', pullurl('http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode'));
$licenseview = str_replace("\n", '', $licenseview);
$licenseview = str_replace("\r", '', $licenseview);
$shortlicense = [];
preg_match_all("'<body[^>]*>(.*)</body>'", $licenseview, $shortlicense);
$licenseview = $shortlicense[1][0];


$params = ['cclicense' => false];

if ('484d213db9a69e79321feafb85915ff1' == md5($licenseview))
{
    $params['cclicense'] = $licenseview;
}

$licenseview = implode('', file('public/LICENSE.txt'));
$licenseview = preg_replace("/[^\na-zA-Z0-9!?.,;:'\"\\/\\()@ -\\]\\[]/", '', $licenseview);
$licensemd5s = [
    'e281e13a86d4418a166d2ddfcd1e8032' => true
];

$params['lotgdlicense'] = false;
if (isset($licensemd5s[md5($licenseview)]))
{
    $params['lotgdlicense'] = $licenseview;
}
else
{
    $params['lotgdlicenseExpected'] = implode(',', array_keys($licensemd5s));
    $params['lotgdlicenseGot'] = md5($licenseview);
    $stage = -1;
    $session['installer']['stagecompleted'] = -1;
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/pages/installer/stage-1.twig', $params));
