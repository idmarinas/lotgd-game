<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/datetime.php';
require_once 'lib/commentary.php';
require_once 'lib/sanitize.php';

function commentmoderate($intro, $section, $message, $limit = 10, $talkline = 'says', $schema = false, $viewall = false)
{
    //function to display comments easily to moderate only, not in the normal gameflow but in the grotto i.e. ... or your might want to write your own modules that do the trick
    if ($intro)
    {
        output($intro);
    }
    viewmoderatedcommentary($section, $message, $limit, $talkline, $schema, $viewall);
}

function viewmoderatedcommentary($section, $message = 'Interject your own commentary?', $limit = 10, $talkline = 'says', $schema = false, $viewall = false)
{
    // this one here is for moderation purposes only. though some redundances, it is better to make this into an extra file
    //if you choose the message to be "X", it won't display a talkform
    global $session,$REQUEST_URI,$doublepost, $translation_namespace;
    global $emptypost;

    $request = $this->getContainer(\Lotgd\Core\Http::class);

    $sectselect = '';
    if (false === $viewall)
    {
        rawoutput("<a name='$section'></a>");
        // Let's add a hook for modules to block commentary sections
        $args = modulehook('blockcommentarea', ['section' => $section]);

        if (isset($args['block']) && ('yes' == $args['block']))
        {
            return;
        }
        $sectselect = "section='$section' AND ";
    }

    $excludes = getsetting('moderateexcludes', '');
    //works here with %, so we need a LIKE and explode it if not empty
    if ('' != $excludes)
    {
        $array = explode(',', $excludes);

        foreach ($array as $entry)
        {
            $sectselect .= "section NOT LIKE '$entry' AND ";
        }
    }
    debug('Select: '.$sectselect);

    if (false === $schema)
    {
        $schema = $translation_namespace;
    }
    tlschema('commentary');

    $nobios = ['motd.php' => true];

    if (! array_key_exists(basename($request->getServer('SCRIPT_NAME')), $nobios))
    {
        $nobios[basename($request->getServer('SCRIPT_NAME'))] = false;
    }

    $linkbios = true;
    if ($nobios[basename($request->getServer('SCRIPT_NAME'))])
    {
        $linkbios = false;
    }

    if ('X' == $message)
    {
        $linkbios = true;
    }

    if ($doublepost)
    {
        output('`$`bDouble post?´b`0`n');
    }

    if ($emptypost)
    {
        output('`$`bWell, they say silence is a virtue.´b`0`n');
    }

    $clanrankcolors = ['`!', '`#', '`^', '`&', '`$'];

    // Needs to be here because scrolling through the commentary pages, entering a bio, then scrolling again forward
    // then re-entering another bio will lead to $com being smaller than 0 and this will lead to an SQL error later on.
    $com = (int) httpget('comscroll');

    if ($com < 0)
    {
        $com = 0;
    }
    $cc = false;

    $cid = 0;
    if (false !== httpget('comscroll') && (int) $session['lastcom'] == $com + 1)
    {
        $cid = (int) $session['lastcommentid'];
    }

    $session['lastcom'] = $com;

    $newadded = 0;
    if ($com > 0 || $cid > 0)
    {
        // Find newly added comments.
        $sql = 'SELECT COUNT(commentid) AS newadded FROM '.
            \DB::prefix('commentary').' LEFT JOIN '.
            \DB::prefix('accounts').' ON '.
            \DB::prefix('accounts').'.acctid = '.
            \DB::prefix('commentary').".author WHERE $sectselect ".
            '('.DB::prefix('accounts').'.locked=0 or '.DB::prefix('accounts').".locked is null) AND commentid > '$cid'";
        $result = \DB::query($sql);
        $row = \DB::fetch_assoc($result);
        $newadded = $row['newadded'];
    }

    $commentbuffer = [];

    if (0 == $cid)
    {
        $sql = 'SELECT '.DB::prefix('commentary').'.*, '.
            \DB::prefix('accounts').'.name, '.
            \DB::prefix('accounts').'.acctid, '.
            \DB::prefix('accounts').'.clanrank, '.
            \DB::prefix('clans').'.clanshort FROM '.
            \DB::prefix('commentary').' LEFT JOIN '.
            \DB::prefix('accounts').' ON '.
            \DB::prefix('accounts').'.acctid = '.
            \DB::prefix('commentary').'.author LEFT JOIN '.
            \DB::prefix('clans').' ON '.
            \DB::prefix('clans').'.clanid='.
            \DB::prefix('accounts').
            ".clanid WHERE $sectselect ".
            '( '.DB::prefix('accounts').'.locked=0 OR '.DB::prefix('accounts').'.locked is null ) '.
            'ORDER BY commentid DESC LIMIT '.
            ($com * $limit).",$limit";

        if (0 == $com && strstr($request->getServer('REQUEST_URI'), '/moderate.php') !== $request->getServer('REQUEST_URI'))
        {
            $result = \DB::query($sql);
        }
        else
        {
            $result = \DB::query($sql);
        }

        while ($row = \DB::fetch_assoc($result))
        {
            $commentbuffer[] = $row;
        }
    }
    else
    {
        $sql = 'SELECT '.DB::prefix('commentary').'.*, '.
            \DB::prefix('accounts').'.name, '.
            \DB::prefix('accounts').'.acctid, '.
            \DB::prefix('accounts').'.clanrank, '.
            \DB::prefix('clans').'.clanshort FROM '.
            \DB::prefix('commentary').' LEFT JOIN '.
            \DB::prefix('accounts').' ON '.
            \DB::prefix('accounts').'.acctid = '.
            \DB::prefix('commentary').'.author LEFT JOIN '.
            \DB::prefix('clans').' ON '.DB::prefix('clans').'.clanid='.
            \DB::prefix('accounts').
            ".clanid WHERE $sectselect ".
            '( '.DB::prefix('accounts').'.locked=0 OR '.DB::prefix('accounts').'.locked is null ) '.
            "AND commentid > '$cid' ".
            "ORDER BY commentid ASC LIMIT $limit";
        $result = \DB::query($sql);

        while ($row = \DB::fetch_assoc($result))
        {
            $commentbuffer[] = $row;
        }
        $commentbuffer = array_reverse($commentbuffer);
    }

    $rowcount = count($commentbuffer);

    if ($rowcount > 0)
    {
        $session['lastcommentid'] = $commentbuffer[0]['commentid'];
    }

    $counttoday = 0;

    for ($i = 0; $i < $rowcount; $i++)
    {
        $row = $commentbuffer[$i];
        $row['comment'] = comment_sanitize($row['comment']);
        $commentids[$i] = $row['commentid'];

        if (date('Y-m-d', strtotime($row['postdate'])) == date('Y-m-d'))
        {
            if ($row['name'] == $session['user']['name'])
            {
                $counttoday++;
            }
        }
        $x = 0;
        $ft = '';

        for ($x = 0; strlen($ft) < 5 && $x < strlen($row['comment']); $x++)
        {
            if ('`' == substr($row['comment'], $x, 1) && 0 == strlen($ft))
            {
                $x++;
            }
            else
            {
                $ft .= substr($row['comment'], $x, 1);
            }
        }

        $link = 'bio.php?char='.$row['acctid'].
            '&ret='.urlencode($request->getServer('REQUEST_URI'));

        if ('::' == substr($ft, 0, 2))
        {
            $ft = substr($ft, 0, 2);
        }
        elseif (':' == substr($ft, 0, 1))
        {
            $ft = substr($ft, 0, 1);
        }
        elseif ('/me' == substr($ft, 0, 3))
        {
            $ft = substr($ft, 0, 3);
        }

        $row['comment'] = holidayize($row['comment'], 'comment');
        $row['name'] = holidayize($row['name'], 'comment');

        if ($row['clanrank'])
        {
            $row['name'] = ($row['clanshort'] > '' ? "{$clanrankcolors[ceil($row['clanrank'] / 10)]}&lt;`2{$row['clanshort']}{$clanrankcolors[ceil($row['clanrank'] / 10)]}&gt; `&" : '').$row['name'];
        }

        if ('::' == $ft || '/me' == $ft || ':' == $ft)
        {
            $x = strpos($row['comment'], $ft);

            if (false !== $x)
            {
                if ($linkbios)
                {
                    $op[$i] = str_replace('&amp;', '&', htmlentities(substr($row['comment'], 0, $x), ENT_COMPAT, getsetting('charset', 'UTF-8')))."`0<a href='$link' style='text-decoration: none'>\n`&{$row['name']}`0</a>\n`& ".str_replace('&amp;', '&', htmlentities(substr($row['comment'], $x + strlen($ft)), ENT_COMPAT, getsetting('charset', 'UTF-8'))).'`0`n';
                }
                else
                {
                    $op[$i] = str_replace('&amp;', '&', htmlentities(substr($row['comment'], 0, $x), ENT_COMPAT, getsetting('charset', 'UTF-8')))."`0`&{$row['name']}`0`& ".str_replace('&amp;', '&', htmlentities(substr($row['comment'], $x + strlen($ft)), ENT_COMPAT, getsetting('charset', 'UTF-8'))).'`0`n';
                }
                $rawc[$i] = str_replace('&amp;', '&', htmlentities(substr($row['comment'], 0, $x), ENT_COMPAT, getsetting('charset', 'UTF-8')))."`0`&{$row['name']}`0`& ".str_replace('&amp;', '&', htmlentities(substr($row['comment'], $x + strlen($ft)), ENT_COMPAT, getsetting('charset', 'UTF-8'))).'`0`n';
            }
        }

        if ('/game' == $ft && ! $row['name'])
        {
            $x = strpos($row['comment'], $ft);

            if (false !== $x)
            {
                $op[$i] = str_replace('&amp;', '&', htmlentities(substr($row['comment'], 0, $x), ENT_COMPAT, getsetting('charset', 'UTF-8'))).'`0`&'.str_replace('&amp;', '&', htmlentities(substr($row['comment'], $x + strlen($ft)), ENT_COMPAT, getsetting('charset', 'UTF-8'))).'`0`n';
            }
        }

        if (! isset($op) || ! is_array($op))
        {
            $op = [];
        }

        if (! array_key_exists($i, $op) || '' == $op[$i])
        {
            if ($linkbios)
            {
                $op[$i] = "`0<a href='$link' style='text-decoration: none'>`&{$row['name']}`0</a>`3 says, \"`#".str_replace('&amp;', '&', htmlentities($row['comment'], ENT_COMPAT, getsetting('charset', 'UTF-8'))).'`3"`0`n';
            }
            elseif ('/game' == substr($ft, 0, 5) && ! $row['name'])
            {
                $op[$i] = str_replace('&amp;', '&', htmlentities($row['comment'], ENT_COMPAT, getsetting('charset', 'UTF-8')));
            }
            else
            {
                $op[$i] = "`&{$row['name']}`3 says, \"`#".str_replace('&amp;', '&', htmlentities($row['comment'], ENT_COMPAT, getsetting('charset', 'UTF-8'))).'`3"`0`n';
            }
            $rawc[$i] = "`&{$row['name']}`3 says, \"`#".str_replace('&amp;', '&', htmlentities($row['comment'], ENT_COMPAT, getsetting('charset', 'UTF-8'))).'`3"`0`n';
        }
        $session['user']['prefs']['timeoffset'] = round($session['user']['prefs']['timeoffset'], 1);

        if (! array_key_exists('timestamp', $session['user']['prefs']))
        {
            $session['user']['prefs']['timestamp'] = 0;
        }

        if (1 == $session['user']['prefs']['timestamp'])
        {
            if (! isset($session['user']['prefs']['timeformat']))
            {
                $session['user']['prefs']['timeformat'] = '[m/d h:ia]';
            }
            $time = strtotime($row['postdate']) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
            $s = date('`7'.$session['user']['prefs']['timeformat'].'`0 ', $time);
            $op[$i] = $s.$op[$i];
        }
        elseif (2 == $session['user']['prefs']['timestamp'])
        {
            $s = reltime(strtotime($row['postdate']));
            $op[$i] = "`7($s)`0 ".$op[$i];
        }

        if ('X' == $message)
        {
            $op[$i] = "`0({$row['section']}) ".$op[$i];
        }

        if ($row['postdate'] >= $session['user']['recentcomments'])
        {
            $op[$i] = "<img src='images/new.gif' alt='&gt;' width='3' height='5' align='absmiddle'> ".$op[$i];
        }
        addnav('', $link);
        $auth[$i] = $row['author'];

        if (isset($rawc[$i]))
        {
            $rawc[$i] = full_sanitize($rawc[$i]);
            $rawc[$i] = htmlentities($rawc[$i], ENT_QUOTES, getsetting('charset', 'UTF-8'));
        }
    }
    $i--;
    $outputcomments = [];
    $sect = 'x';

    $moderating = false;

    if (($session['user']['superuser'] & SU_EDIT_COMMENTS) && 'X' == $message)
    {
        $moderating = true;
    }

    $del = translate_inline('Del');
    $scriptname = substr($request->getServer('SCRIPT_NAME'), strrpos($request->getServer('SCRIPT_NAME'), '/') + 1);
    $pos = strpos($request->getServer('REQUEST_URI'), '?');
    $return = $scriptname.(false == $pos ? '' : substr($request->getServer('REQUEST_URI'), $pos));
    $one = (false == strstr($return, '?') ? '?' : '&');

    for (; $i >= 0; $i--)
    {
        $out = '';

        if ($moderating)
        {
            if ($session['user']['superuser'] & SU_EDIT_USERS)
            {
                $out .= "`0[ <input type='checkbox' name='comment[{$commentids[$i]}]'> | <a href='user.php?op=setupban&userid=".$auth[$i].'&reason='.rawurlencode($rawc[$i])."'>Ban</a> ]&nbsp;";
                addnav('', "user.php?op=setupban&userid=$auth[$i]&reason=".rawurlencode($rawc[$i]));
            }
            else
            {
                $out .= "`0[ <input type='checkbox' name='comment[{$commentids[$i]}]'> ]&nbsp;";
            }
            $matches = [];
            preg_match('/[(]([^)]*)[)]/', $op[$i], $matches);
            $sect = trim($matches[1]);

            if ('clan-' != substr($sect, 0, 5) || $sect == $section)
            {
                if ('pet-' != substr($sect, 0, 4))
                {
                    $out .= $op[$i];

                    if (! isset($outputcomments[$sect]) ||
                            ! is_array($outputcomments[$sect]))
                    {
                        $outputcomments[$sect] = [];
                    }
                    array_push($outputcomments[$sect], $out);
                }
            }
        }
        else
        {
            if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
            {
                $out .= "`2[<a href='".$return.$one."removecomment={$commentids[$i]}&section=$section&returnpath=/".urlencode($return)."'>$del</a>`2]`0&nbsp;";
                addnav('', $return.$one."removecomment={$commentids[$i]}&section=$section&returnpath=/".urlencode($return).'');
            }
            $out .= $op[$i];

            if (! array_key_exists($sect, $outputcomments) || ! is_array($outputcomments[$sect]))
            {
                $outputcomments[$sect] = [];
            }
            array_push($outputcomments[$sect], $out);
        }
    }

    if ($moderating)
    {
        $scriptname = substr($request->getServer('SCRIPT_NAME'), strrpos($request->getServer('SCRIPT_NAME'), '/') + 1);
        addnav('', "$scriptname?op=commentdelete&return=".urlencode($request->getServer('REQUEST_URI')));
        $mod_Del1 = htmlentities(translate_inline('Delete Checked Comments'), ENT_COMPAT, getsetting('charset', 'UTF-8'));
        $mod_Del2 = htmlentities(translate_inline('Delete Checked & Ban (3 days)'), ENT_COMPAT, getsetting('charset', 'UTF-8'));
        $mod_Del_confirm = addslashes(htmlentities(translate_inline('Are you sure you wish to ban this user and have you specified the exact reason for the ban, i.e. cut/pasted their offensive comments?'), ENT_COMPAT, getsetting('charset', 'UTF-8')));
        $mod_reason = translate_inline('Reason:');
        $mod_reason_desc = htmlentities(translate_inline('Banned for comments you posted.'), ENT_COMPAT, getsetting('charset', 'UTF-8'));

        output_notl("<br><form action='$scriptname?op=commentdelete&return=".urlencode($request->getServer('REQUEST_URI'))."' method='POST'>", true);
        output_notl("<input type='submit' class='ui button' value=\"$mod_Del1\">", true);
        output_notl("<input type='submit' class='ui button' name='delnban' value=\"$mod_Del2\" onClick=\"return confirm('$mod_Del_confirm');\">", true);
        output_notl("`n`n$mod_reason <div class='ui input'><input name='reason0' size='40' value=\"$mod_reason_desc\" onChange=\"document.getElementById('reason').value=this.value;\"></div>", true);
    }

    //output the comments
    ksort($outputcomments);
    reset($outputcomments);
    $sections = commentarylocs();
    $needclose = 0;

    while (list($sec, $v) = each($outputcomments))
    {
        if ('x' != $sec)
        {
            if ($needclose)
            {
                modulehook('}collapse');
            }
            output_notl("`n<hr><a href='moderate.php?area=%s'>`b`^%s`0´b</a>`n",
                $sec, isset($sections[$sec]) ? $sections[$sec] : "($sec)", true);
            addnav('', "moderate.php?area=$sec");
            modulehook('collapse{', ['name' => 'com-'.$sec]);
            $needclose = 1;
        }
        else
        {
            modulehook('collapse{', ['name' => 'com-'.$section]);
            $needclose = 1;
        }
        reset($v);

        while (list($key, $val) = each($v))
        {
            $args = ['commentline' => $val];
            $args = modulehook('viewcommentary', $args);
            $val = $args['commentline'];
            output_notl($val, true);
        }
    }

    if ($moderating && $needclose)
    {
        modulehook('}collapse');
        $needclose = 0;
    }

    if ($moderating)
    {
        output_notl('`n');
        rawoutput("<input type='submit' class='ui button' value=\"$mod_Del1\">");
        rawoutput("<input type='submit' class='ui button' name='delnban' value=\"$mod_Del2\" onClick=\"return confirm('$mod_Del_confirm');\">");
        output_notl('`n`n%s ', $mod_reason);
        rawoutput("<div class='ui input'><input type='text' name='reason' size='40' id='reason' value=\"$mod_reason_desc\"></div>");
        rawoutput('</form>');
        output_notl('`n');
    }

    if ($session['user']['loggedin'])
    {
        $args = modulehook('insertcomment', ['section' => $section]);

        if (array_key_exists('mute', $args) && $args['mute'] &&
                ! ($session['user']['superuser'] & SU_EDIT_COMMENTS))
        {
            output_notl('%s', $args['mutemsg']);
        }
        elseif ($counttoday < ($limit / 2) ||
                ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO)
                || ! getsetting('postinglimit', 1))
        {
            if ('X' != $message)
            {
                $message = "`n`@$message`n";
                output($message);
                talkform($section, $talkline, $limit, $schema);
            }
        }
        else
        {
            $message = "`n`@$message`n";
            output($message);
            output("Sorry, you've exhausted your posts in this section for now.`0`n");
        }
    }

    $jump = false;

    if (! isset($session['user']['prefs']['nojump']) || false == $session['user']['prefs']['nojump'])
    {
        $jump = true;
    }

    $firstu = translate_inline('&lt;&lt; First Unseen');
    $prev = translate_inline('&lt; Previous');
    $ref = translate_inline('Refresh');
    $next = translate_inline('Next &gt;');
    $lastu = translate_inline('Last Page &gt;&gt;');

    if ($rowcount >= $limit || $cid > 0)
    {
        $sql = 'SELECT count(commentid) AS c FROM '.DB::prefix('commentary')." WHERE section='$section' AND postdate > '{$session['user']['recentcomments']}'";
        $r = \DB::query($sql);
        $val = \DB::fetch_assoc($r);
        $val = round($val['c'] / $limit + 0.5, 0) - 1;

        if ($val > 0)
        {
            $first = comscroll_sanitize($REQUEST_URI).'&comscroll='.($val);
            $first = str_replace('?&', '?', $first);

            if (! strpos($first, '?'))
            {
                $first = str_replace('&', '?', $first);
            }
            $first .= '&refresh=1';

            if ($jump)
            {
                $first .= "#$section";
            }
            output_notl("<a href=\"$first\">$firstu</a>", true);
            addnav('', $first);
        }
        else
        {
            output_notl($firstu, true);
        }
        $req = comscroll_sanitize($REQUEST_URI).'&comscroll='.($com + 1);
        $req = str_replace('?&', '?', $req);

        if (! strpos($req, '?'))
        {
            $req = str_replace('&', '?', $req);
        }
        $req .= '&refresh=1';

        if ($jump)
        {
            $req .= "#$section";
        }
        output_notl("<a href=\"$req\">$prev</a>", true);
        addnav('', $req);
    }
    else
    {
        output_notl("$firstu $prev", true);
    }
    $last = appendlink(comscroll_sanitize($REQUEST_URI), 'refresh=1');

    // Okay.. we have some smart-ass (or stupidass, you guess) players
    // who think that the auto-reload firefox plugin is a good way to
    // avoid our timeouts.  Won't they be surprised when I take that little
    // hack away.
    $last = appendcount($last);

    $last = str_replace('?&', '?', $last);

    if ($jump)
    {
        $last .= "#$section";
    }
    //if (!strpos($last,"?")) $last = str_replace("&","?",$last);
    //debug($last);
    output_notl("&nbsp;<a href=\"$last\">$ref</a>&nbsp;", true);
    addnav('', $last);

    if ($com > 0 || ($cid > 0 && $newadded > $limit))
    {
        $req = comscroll_sanitize($REQUEST_URI).'&comscroll='.($com - 1);
        $req = str_replace('?&', '?', $req);

        if (! strpos($req, '?'))
        {
            $req = str_replace('&', '?', $req);
        }
        $req .= '&refresh=1';

        if ($jump)
        {
            $req .= "#$section";
        }
        output_notl(" <a href=\"$req\">$next</a>", true);
        addnav('', $req);
        output_notl(" <a href=\"$last\">$lastu</a>", true);
    }
    else
    {
        output_notl("$next $lastu", true);
    }

    if (! $cc)
    {
        \DB::free_result($result);
    }
    tlschema();

    if ($needclose)
    {
        modulehook('}collapse');
    }
}
