<?php

require_once 'lib/settings.class.php';
include_once 'lib/gamelog.php';

if ('savecronjob' == $op)
{
    $cronid = (string) httpget('cronid');
    $post = httpallpost();
    $post = array_filter($post);

    //-- NAME - only accept alphabetic characters and digits in the unicode "letter" and "number" categories, respectively
    $filter = new Zend\I18n\Filter\Alnum();
    $post['name'] = $filter->filter($post['name']);
    //-- SCHELUDE - modifies to remove whitespaces from the beginning and end.
    $filter = new Zend\Filter\StringTrim();
    $post['schedule'] = $filter->filter($post['schedule']);

    if ($cronid)
    {
        $update = DB::update('cronjob');
        $update->set($post)
            ->where->equalTo('name', $cronid);
        DB::execute($update);

        output('`@CronJob update successful.`0');

        gamelog("`qUpdate CronJob `^{$post['name']}`$ by admin {$session['user']['playername']}", 'cronjob');
    }
    else
    {
        $insert = DB::insert('cronjob');
        $insert->values($post);
        DB::execute($insert);

        output('`@CronJob created successful.`0');

        gamelog("`@Create CronJob `^{$post['name']}`$ by admin {$session['user']['playername']}", 'cronjob');
    }

	$op = '';
	httpset($op, '');

    invalidatedatacache('cronjobstable', true);
}
elseif ('delcronjob' == $op)
{
    $cronid = (string) httpget('cronid');

    if ($cronid)
    {
        $delete = DB::delete('cronjob');
        $delete->where(['name' => $cronid]);
        DB::execute($delete);

        output('`$CronJob deleted successful.`0');
        gamelog("`4Delete CronJob `^$cronid`4 by admin {$session['user']['playername']}", 'cronjob');

        invalidatedatacache('cronjobstable', true);
    }
}

if ('savecron' == $op)
{
    $old = (int) getsetting('newdaycron', 0);
    $new = (int) httppost('newdaycron');

    if ($old != $new)
    {
		savesetting('newdaycron', $new);
		output("Setting %s to %s`n", 'newdaycron', $new);
		gamelog("`@Changed core setting `^newdaycron`@ from `#{$old}`@ to `&$new`0","settings");
		// Notify every module
		modulehook('changesetting', ['module' => 'core', 'setting' => 'newdaycron', 'old' => $old, 'new' => $new], true);
	}
	output("`^Settings saved.`0");
	$op = '';
	httpset($op, '');
}
elseif ('newcronjob' == $op)
{
    require_once 'lib/listfiles.php';

    $cronid = (string) httpget('cronid');

    $data = ['enabled' => 1];
    if ($cronid)
    {
        $select = DB::select('cronjob');
        $select->columns(['*'])
            ->limit(1)
            ->where->equalTo('name', $cronid)
        ;

        $result = DB::execute($select)->current();

        if ($result) $data = $result;
    }

    $sort = list_files('cronjob', []);
	sort($sort);
	$scriptenum = implode('', $sort);
	$scriptenum = ',,none'.$scriptenum;

    $form = [
        'Job requires these,title',
            'Note: This three options are mandatory,note',
            'name' => 'Name for CronJob. There can not be another with the same name',
            'schedule' => 'Crontab schedule format (man -s 5 crontab) or DateTime format (Y-m-d H:i:s) (https://crontab.guru)',
            'command' => 'The shell command to run,enum'.$scriptenum,
            'Note: if you want add new CronJob add your Cron PHP file in "cronjob/" folder,note',
        'Other,title',
            'runAs' => 'Run as this user, if crontab user has sudo privileges',
            'debug' => 'Send jobby internal messages to "debug.log",bool',
        'Filtering,title',
            'environment' => 'Development environment for this job,textarea',
            'runOnHost' => 'Run jobs only on this hostname',
            'maxRuntime' => 'Maximum execution time for this job (in seconds),number',
            'enabled' => 'Run this job at scheduled times (enable or disabled),bool',
            'haltDir' => 'A job will not run if this directory contains a file bearing the job\'s name',
        'Logging,title',
            'output' => 'Redirect stdout and stderr to this file',
            'dateFormat' => 'Format for dates on jobby log messages',
        'Mailing,title',
            'recipients' => 'Comma-separated string of email addresses',
            'mailer' => 'Email method: sendmail or smtp or mail',
            'smtpHost' => 'SMTP host; if mailer is smtp',
            'smtpPort' => 'SMTP port; if mailer is smtp,number',
            'smtpUsername' => 'SMTP user; if mailer is smtp',
            'smtpPassword' => 'SMTP password; if mailer is smtp',
            'smtpSecurity' => 'SMTP security option: ssl or tls, if mailer is smtp',
            'smtpSender' => 'The sender and from addresses used in SMTP notices',
            'smtpSenderName' => 'Jobby	The name used in the from field for SMTP messages',
    ];

    addnav('', "configuration.php?settings=cronjob&op=savecronjob&cronid=$cronid");
    rawoutput("<form action='configuration.php?settings=cronjob&op=savecronjob&cronid=$cronid' method='POST'>");
    lotgd_showform($form, $data);
    rawoutput("</form>");

	$op = '';
	httpset($op, '');
}
else
{
    $setup_cronjob = include_once 'lib/data/configuration_cronjob.php';

    rawoutput('<div class="ui info message">');
    output('Before activate this option, make sure you setup a cronjob on your machine confixx/plesk/cpanel or any other admin panel.`n');
    output('This is de unique cronjob need create. Copy and change `b"/path/to/project"`b to where is the game installed. This cronjob execute all CronJobs in the game.`n`n');
    output_notl('* * * * * cd /path/to/project && php cronjob.php 1>> /dev/null 2>&1');
    rawoutput('</div><div class="ui red message">');
    output('If you do not know what a Cronjob is... leave it turned off. If you want to know more... check out: %s', '<a class="ui red mini button" href="http://wiki.dragonprime.net/index.php?title=Cronjob">http://wiki.dragonprime.net/index.php?title=Cronjob</a>', true);
    rawoutput('</div>');
    output_notl('`n`n');
    rawoutput("<form action='configuration.php?settings=cronjob&op=savecron' method='POST'>");
    lotgd_showform($setup_cronjob, ['newdaycron' => getsetting('newdaycron', 0)]);
    rawoutput("</form>");
    output_notl('`n`n');

    $yes = translate_inline('`@Yes`0');
    $no = translate_inline('`$No`0');
    $newcronjob = translate_inline('New CronJob');
    $edit = translate_inline('Edit');
    $delete = translate_inline('Delete');
    $confirm = translate_inline('Are you sure you wish to delete this CronJob?');

    $page = max(1, (int) httpget('page'));

    $select = DB::select('cronjob');
    $select->columns(['*']);
    $result = DB::paginator($select, $page);
    DB::pagination($result, 'configuration.php?settings=cronjob');

    rawoutput('<a class="ui right floated button" href="configuration.php?settings=cronjob&op=newcronjob">'.$newcronjob.'</a>');
    output('`@`bCronJobs available in the game`b`0');
    addnav('', 'configuration.php?settings=cronjob&op=newcronjob');
    rawoutput('<br><br><table class="ui very compact striped selectable table">');
    rawoutput('<thead><tr><th>Name</th><th>Command</th><th>Schedule</th><th>Debug</th><th>Enabled</th><th>Opcs</th></tr></thead>');
    foreach($result as $key => $value)
    {
        rawoutput('<tr><td>');
        output_notl($value['name']);
        rawoutput('</td><td>');
        output_notl('php ' . $value['command'] . '.php');
        rawoutput('</td><td>');
        output_notl('<a href="https://crontab.guru/#%s" target="_blank"><i class="info icon"></i> `b%s`b</a>', str_replace(' ', '_', $value['schedule']),$value['schedule'], true);
        rawoutput('</td><td>');
        output_notl(($value['debug']?$yes:$no));
        rawoutput('</td><td>');
        output_notl(($value['enabled']?$yes:$no));
        rawoutput('</td><td>');
        $editlink = "configuration.php?settings=cronjob&op=newcronjob&cronid={$value['name']}";
        $deletelink = "configuration.php?settings=cronjob&op=delcronjob&cronid={$value['name']}";
        addnav('', $editlink);
        addnav('', $deletelink);
        output_notl("<a href='{$editlink}'>{$edit}</a> | <a href='{$deletelink}' onClick='return confirm(\"$confirm\");'>`4{$delete}`0</a>", true);
        rawoutput('</tr>');
    }
    rawoutput('</table>');

	$op = '';
	httpset($op, '');
}
