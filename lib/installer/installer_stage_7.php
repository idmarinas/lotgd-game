<?php

$type = (string) \LotgdHttp::getPost('type');

if ($type > '')
{
    $session['installer']['fromversion'] = \LotgdHttp::getPost('version');
    if ('install' == $type)
    {
        $session['installer']['fromversion'] = '-1';
        $session['installer']['dbinfo']['upgrade'] = false;
    }
}

if (! isset($session['installer']['fromversion']) || '' == $session['installer']['fromversion'])
{
    clearsettings();//-- To avoid possible problems with the cache
    $version = (string) getsetting('installer_version', '-1');
    $installer = new \Lotgd\Core\Installer\Install();
    $lotgd_versions = $installer->getAllVersions();

    $session['installer']['dbinfo']['upgrade'] = false;
    if ('-1' != $version)
    {
        $session['installer']['dbinfo']['upgrade'] = true;
    }

    $version = ('-1' == $version) ? '0.9' : $version;
    $version = $installer->getIntVersion($version);

    $session['installer']['stagecompleted'] = $stage - 1;

    $params = [
        'upgrade' => $session['installer']['dbinfo']['upgrade'],
        'lotgdVersions' => $lotgd_versions,
        'actualVersion' => $version
    ];

    rawoutput(LotgdTheme::renderLotgdTemplate('core/page/installer/stage-7.twig', $params));
}
else
{
    $session['installer']['stagecompleted'] = $stage;

    return redirect('installer.php?stage='.($stage + 1));
}
