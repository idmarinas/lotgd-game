<?php

$subop = httpget('subop');
$none = translate_inline('NONE');

if ('xml' == $subop)
{
    header('Content-Type: text/xml');
    $sql = 'SELECT DISTINCT '.DB::prefix('accounts').'.name FROM '.DB::prefix('bans').', '.DB::prefix('accounts')." WHERE (ipfilter='".addslashes(httpget('ip'))."' AND ".
        DB::prefix('bans').".uniqueid='".
        addslashes(httpget('id'))."') AND ((substring(".
        DB::prefix('accounts').'.lastip,1,length(ipfilter))=ipfilter '.
        "AND ipfilter<>'') OR (".DB::prefix('bans').'.uniqueid='.
        DB::prefix('accounts').'.uniqueid AND '.
        DB::prefix('bans').".uniqueid<>''))";
    $r = DB::query($sql);
    echo '<xml>';
    $number = DB::num_rows($r);

    for ($x = 0; $x < $number; $x++)
    {
        $ro = DB::fetch_assoc($r);
        echo '<name name="';
        echo urlencode(appoencode("`0{$ro['name']}"));
        echo '"/>';
    }

    if (0 == DB::num_rows($r))
    {
        echo "<name name=\"$none\"/>";
    }
    echo '</xml>';

    exit();
}
    DB::query('DELETE FROM '.DB::prefix('bans').' WHERE banexpire < "'.date('Y-m-d')."\" AND banexpire>'0000-00-00'");
$duration = httpget('duration');

if (httpget('notbefore'))
{
    $operator = '>=';
}
else
{
    $operator = '<=';
}

if ('' == $duration)
{
    $since = " WHERE banexpire $operator '".date('Y-m-d H:i:s', strtotime('+2 weeks'))."' AND banexpire > '0000-00-00 00:00:00'";
    output('`bShowing bans that will expire within 2 weeks.`b`n`n');
}
else
{
    if ('forever' == $duration)
    {
        $since = " WHERE banexpire='0000-00-00 00:00:00'";
        output('`bShowing all permanent bans`b`n`n');
    }
    elseif ('all' == $duration)
    {
        $since = '';
        output('`bShowing all bans`b`n`n');
    }
    else
    {
        $since = " WHERE banexpire $operator '".date('Y-m-d H:i:s', strtotime('+'.$duration))."' AND banexpire > '0000-00-00 00:00:00'";
        output('`bShowing bans that will expire within %s.`b`n`n', $duration);
    }
}
addnav('Perma-Bans');
addnav('Show', 'user.php?op=removeban&duration=forever');
addnav('Will Expire Within');
addnav('1 week', 'user.php?op=removeban&duration=1+week');
addnav('2 weeks', 'user.php?op=removeban&duration=2+weeks');
addnav('3 weeks', 'user.php?op=removeban&duration=3+weeks');
addnav('4 weeks', 'user.php?op=removeban&duration=4+weeks');
addnav('2 months', 'user.php?op=removeban&duration=2+months');
addnav('3 months', 'user.php?op=removeban&duration=3+months');
addnav('4 months', 'user.php?op=removeban&duration=4+months');
addnav('5 months', 'user.php?op=removeban&duration=5+months');
addnav('6 months', 'user.php?op=removeban&duration=6+months');
addnav('1 year', 'user.php?op=removeban&duration=1+year');
addnav('2 years', 'user.php?op=removeban&duration=2+years');
addnav('4 years', 'user.php?op=removeban&duration=4+years');
addnav('Show all', 'user.php?op=removeban&duration=all');
addnav('Will Expire not before');
addnav('1 week', 'user.php?op=removeban&duration=1+week&notbefore=1');
addnav('2 weeks', 'user.php?op=removeban&duration=2+weeks&notbefore=1');
addnav('3 weeks', 'user.php?op=removeban&duration=3+weeks&notbefore=1');
addnav('4 weeks', 'user.php?op=removeban&duration=4+weeks&notbefore=1');
addnav('2 months', 'user.php?op=removeban&duration=2+months&notbefore=1');
addnav('3 months', 'user.php?op=removeban&duration=3+months&notbefore=1');
addnav('4 months', 'user.php?op=removeban&duration=4+months&notbefore=1');
addnav('5 months', 'user.php?op=removeban&duration=5+months&notbefore=1');
addnav('6 months', 'user.php?op=removeban&duration=6+months&notbefore=1');
addnav('1 year', 'user.php?op=removeban&duration=1+year&notbefore=1');
addnav('2 years', 'user.php?op=removeban&duration=2+years&notbefore=1');
addnav('4 years', 'user.php?op=removeban&duration=4+years&notbefore=1');

$sql = 'SELECT * FROM '.DB::prefix('bans')." $since ORDER BY banexpire ASC";
$result = DB::query($sql);
rawoutput("<script language='JavaScript'>
function getUserInfo(ip,id,divid){
	var filename='user.php?op=removeban&subop=xml&ip='+ip+'&id='+id;
	//set up the DOM object
	var xmldom;
	if (document.implementation &&
			document.implementation.createDocument){
		//Mozilla style browsers
		xmldom = document.implementation.createDocument('', '', null);
	} else if (window.ActiveXObject) {
		//IE style browsers
		xmldom = new ActiveXObject('Microsoft.XMLDOM');
	}
		xmldom.async=false;
	xmldom.load(filename);
	var output='';
	for (var x=0; x<xmldom.documentElement.childNodes.length; x++){
		output = output + unescape(xmldom.documentElement.childNodes[x].getAttribute('name').replace(/\\+/g,' ')) +'<br>';
	}
	document.getElementById('user'+divid).innerHTML=output;
}
</script>
");
rawoutput("<table class='ui very compact striped selectable table'>");
$ops = translate_inline('Ops');
$bauth = translate_inline('Ban Author');
$ipd = translate_inline('IP/ID');
$dur = translate_inline('Duration');
$mssg = translate_inline('Message');
$aff = translate_inline('Affects');
$l = translate_inline('Last');
rawoutput("<thead><tr><th>$ops</th><th>$bauth</th><th>$ipd</th><th>$dur</th><th>$mssg</th><th>$aff</th><th>$l</th></tr></thead");
$i = 0;

while ($row = DB::fetch_assoc($result))
{
    $liftban = translate_inline('Lift&nbsp;ban');
    $showuser = translate_inline('Click&nbsp;to&nbsp;show&nbsp;users');
    rawoutput("<tr class='".($i % 2 ? 'trlight' : 'trdark')."'>");
    rawoutput("<td><a href='user.php?op=delban&ipfilter=".urlencode($row['ipfilter']).'&uniqueid='.urlencode($row['uniqueid'])."'>");
    output_notl('%s', $liftban, true);
    rawoutput('</a>');
    addnav('', 'user.php?op=delban&ipfilter='.urlencode($row['ipfilter']).'&uniqueid='.urlencode($row['uniqueid']));
    rawoutput('</td><td>');
    output_notl('`&%s`0', $row['banner']);
    rawoutput('</td><td>');
    output_notl('%s', $row['ipfilter']);
    output_notl('%s', $row['uniqueid']);
    rawoutput('</td><td>');
    // "43200" used so will basically round to nearest day rather than floor number of days

    $expire = sprintf_translate('%s days',
            round((strtotime($row['banexpire']) + 43200 - strtotime('now')) / 86400, 0));

    if ('1 ' == substr($expire, 0, 2))
    {
        $expire = translate_inline('1 day');
    }

    if (date('Y-m-d', strtotime($row['banexpire'])) == date('Y-m-d'))
    {
        $expire = translate_inline('Today');
    }

    if (date('Y-m-d', strtotime($row['banexpire'])) ==
            date('Y-m-d', strtotime('1 day')))
    {
        $expire = translate_inline('Tomorrow');
    }

    if ('0000-00-00 00:00:00' == $row['banexpire'])
    {
        $expire = translate_inline('Never');
    }
    output_notl('%s', $expire);
    rawoutput('</td><td>');
    output_notl('%s', $row['banreason']);
    rawoutput('</td><td>');
    $file = "user.php?op=removeban&subop=xml&ip={$row['ipfilter']}&id={$row['uniqueid']}";
    rawoutput("<div id='user$i'><a href='$file' target='_blank' onClick=\"getUserInfo('{$row['ipfilter']}','{$row['uniqueid']}',$i); return false;\">");
    output_notl('%s', $showuser, true);
    rawoutput('</a></div>');
    addnav('', $file);
    rawoutput('</td><td>');
    output_notl($lotgdFormat->relativedate($row['lasthit']));
    rawoutput('</td></tr>');
    $i++;
}
rawoutput('</table>');
