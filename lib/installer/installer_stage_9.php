<?php

$sql_upgrade_statements = include 'lib/installer/installer_sqlstatements.php';
require_once 'lib/installer/installer_functions.php';
require_once 'lib/tabledescriptor.php';

//-- To avoid conflicts if you have a code debugger installed such as xDebug
if (0 != ini_get('max_execution_time'))
{
    set_time_limit(660);
} //-- Temporary increased limit execution time

output('`@`c`bBuilding the Tables`b`c');
output("`2I'm now going to build the tables.");
output('If this is an upgrade, your current tables will be brought in line with the current version.');
output("If it's an install, the necessary tables will be placed in your database.`n");
output('`n`@Table Synchronization Logs:`n');
rawoutput("<div style='width: 100%; height: 150px; max-height: 150px; overflow: auto;'>");
$descriptors = descriptors($DB_PREFIX);
reset($descriptors);

foreach ($descriptors as $tablename => $descriptor)
{
    output("`3Synchronizing table `#$tablename`3..`n");
    synctable($tablename, $descriptor, true);

    if (false == $session['dbinfo']['upgrade'])
    {
        //on a clean install, destroy all old data.
        DB::query("TRUNCATE TABLE $tablename");
    }
}
rawoutput('</div>');
output("`n`2The tables now have new fields and columns added, I'm going to begin importing data now.`n");
rawoutput("<div style='width: 100%; height: 150px; max-height: 150px; overflow: auto;'>");
$dosql = false;
reset($sql_upgrade_statements);

foreach ($sql_upgrade_statements as $key => $val)
{
    if ($dosql)
    {
        output('`3Version `#%s`3: %s SQL statements...`n', $key, count($val));

        if (count($val) > 0)
        {
            output('`^Doing: `6');
            reset($val);
            $count = 0;

            while (list($id, $sql) = each($val))
            {
                $onlyupgrade = 0;

                if ('1|' == substr($sql, 0, 2))
                {
                    $sql = substr($sql, 2);
                    $onlyupgrade = 1;
                }
                // Skip any statements that should only be run during
                // upgrades from previous versions.
                if (! $session['dbinfo']['upgrade'] && $onlyupgrade)
                {
                    continue;
                }
                $count++;

                if (0 == $count % 10 && $count != count($val))
                {
                    output_notl("`6$count...");
                }

                if (! DB::query($sql))
                {
                    output("`n`\$Error: `^'%s'`7 executing `#'%s'`7.`n",
                    DB::error(), $sql);
                }
            }
            output("$count.`n");
        }
    }

    if ($key == $session['fromversion'] || false == $session['dbinfo']['upgrade'])
    {
        $dosql = true;
    }
}
rawoutput('</div>');
    /*
output("`n`2Now I'll install the recommended modules.");
output("Please note that these modules will be installed, but not activated.");
output("Once installation is complete, you should use the Module Manager found in the superuser grotto to activate those modules you wish to use.");
reset($recommended_modules);
rawoutput("<div style='width: 100%; height: 150px; max-height: 150px; overflow: auto;'>");
while (list($key,$modulename)=each($recommended_modules)){
output("`3Installing `#$modulename`\$`n");
install_module($modulename, false);
}
rawoutput("</div>");
*/
if (! isset($session['skipmodules']) || ! $session['skipmodules'])
{
    output("`n`2Now I'll install and configure your modules.");
    reset($session['moduleoperations']);
    rawoutput("<div style='width: 100%; height: 150px; max-height: 150px; overflow: auto;'>");

    while (list($modulename, $val) = each($session['moduleoperations']))
    {
        $ops = explode(',', $val);
        reset($ops);

        while (list($trash, $op) = each($ops))
        {
            switch ($op)
            {
                case 'uninstall':
                    output("`3Uninstalling `#$modulename`3: ");

                    if (uninstall_module($modulename))
                    {
                        output('`@OK!`0`n');
                    }
                    else
                    {
                        output('`$Failed!`0`n');
                    }
                break;
                case 'install':
                    output("`3Installing `#$modulename`3: ");

                    if (install_module($modulename))
                    {
                        output('`@OK!`0`n');
                    }
                    else
                    {
                        output('`$Failed!`0`n');
                    }
                    install_module($modulename);
                break;
                case 'activate':
                    output("`3Activating `#$modulename`3: ");

                    if (activate_module($modulename))
                    {
                        output('`@OK!`0`n');
                    }
                    else
                    {
                        output('`$Failed!`0`n');
                    }
                break;
                case 'deactivate':
                    output("`3Deactivating `#$modulename`3: ");

                    if (deactivate_module($modulename))
                    {
                        output('`@OK!`0`n');
                    }
                    else
                    {
                        output('`$Failed!`0`n');
                    }
                break;
                case 'donothing':
                break;
            }
        }
        $session['moduleoperations'][$modulename] = 'donothing';
    }
    rawoutput('</div>');
}
output("`n`2Finally, I'll clean up old data.`n");
rawoutput("<div style='width: 100%; height: 150px; max-height: 150px; overflow: auto;'>");
reset($descriptors);

while (list($tablename, $descriptor) = each($descriptors))
{
    output("`3Cleaning up `#$tablename`3...`n");
    synctable($tablename, $descriptor);
}
rawoutput('</div>');
output("`n`n`^You're ready for the next step.");
