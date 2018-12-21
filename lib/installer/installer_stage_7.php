<?php

if (httppost('type') > '')
{
    $session['fromversion'] = httppost('version');
    $session['dbinfo']['upgrade'] = true;
    if ('install' == httppost('type'))
    {
        $session['fromversion'] = '-1';
        $session['dbinfo']['upgrade'] = false;
    }
}

if (! isset($session['fromversion']) || '' == $session['fromversion'])
{
    output('`@`c`bConfirmation´b´c');
    output('`2Please confirm the following:`0`n');
    rawoutput("<form class='ui form' action='installer.php?stage=7' method='POST'>");
    rawoutput("<table class='ui very basic table'><tr><td>");
    output('`2I should:`0');
    rawoutput('</td><td>');

    $version = (string) getsetting('installer_version', '-1');

    $session['dbinfo']['upgrade'] = false;
    if ('-1' != $version)
    {
        $session['dbinfo']['upgrade'] = true;
    }
    rawoutput("<input type='radio' value='upgrade' name='type' ".($session['dbinfo']['upgrade'] ? 'checked' : '').'>');
    output(' `2Perform an upgrade from ');

    $version = ('-1' == $version) ? '0.9' : $version;

    $installer = new \Lotgd\Core\Installer\Install();
    $lotgd_versions = $installer->getAllVersions();

    $version = $installer->getIntVersion($version);
    unset($installer, $lotgd_versions[-1]);

    rawoutput("<select name='version' class='ui search dropdown'>");

    foreach ($lotgd_versions as $name => $key)
    {
        rawoutput("<option value='$key'".($version == $key ? ' selected' : '').">$name</option>");
    }
    rawoutput('</select>');
    rawoutput("<br><input type='radio' value='install' name='type' ".($session['dbinfo']['upgrade'] ? '' : 'checked').'>');
    output(' `2Perform a clean install.');
    rawoutput('</td></tr></table>');
    $submit = translate_inline('Submit');
    rawoutput("<input type='submit' value='$submit' class='ui button'>");
    rawoutput('</form>');

    $session['stagecompleted'] = $stage - 1;
}
else
{
    $session['stagecompleted'] = $stage;
    header('Location: installer.php?stage='.($stage + 1));

    exit();
}
