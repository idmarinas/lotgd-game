<?php

require_once 'lib/pullurl.php';

$licenseview = implode('', pullurl('http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode'));
$licenseview = str_replace("\n", '', $licenseview);
$licenseview = str_replace("\r", '', $licenseview);
$shortlicense = [];
preg_match_all("'<body[^>]*>(.*)</body>'", $licenseview, $shortlicense);
$licenseview = $shortlicense[1][0];
output('`@`c`bLicense Agreement´b´c`0');
output('`2Before continuing, you must read and understand the following license agreement.`0`n`n');

if ('484d213db9a69e79321feafb85915ff1' == md5($licenseview))
{
    rawoutput("<div style='height: 350px; max-height: 350px; overflow: auto; color: #FFFFFF; background-color: #000000; padding: 10px;'>");
    rawoutput($licenseview);
    rawoutput('</div>');
}
else
{
    output('`^Warning, the Creative Commons license has changed, or could not be retrieved from the Creative Commons server.');
    output('You should check with the game authors to ensure that the below license agrees with the license under which it was released.');
    output('The license may be referenced at %s.`n', "<a target='_blank' rel='noopener noreferrer' href='http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode'>the Creative Commons site</a>", true);
}
$licenseview = implode('', file('public/LICENSE.txt'));
$licenseview = preg_replace("/[^\na-zA-Z0-9!?.,;:'\"\\/\\()@ -\\]\\[]/", '', $licenseview);
$licensemd5s = [
    'e281e13a86d4418a166d2ddfcd1e8032' => true
];

if (isset($licensemd5s[md5($licenseview)]))
{
    $licenseview = htmlentities($licenseview, ENT_COMPAT, getsetting('charset', 'UTF-8'));
    $licenseview = nl2br($licenseview);
    output('`n`n`b`@Plain Text:´b`n`7');
    rawoutput($licenseview);
}
else
{
    output("`n`^The license file (LICENSE.txt) has been modified.  Please obtain a new copy of the game's code, this file has been tampered with.");
    output('Expected MD5 in ('.implode(array_keys($licensemd5s), ',').'), but got '.md5($licenseview));
    $stage = -1;
    $session['installer']['stagecompleted'] = -1;
}
