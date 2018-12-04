<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/datetime.php';
require_once 'lib/sanitize.php';

tlschema('commentary');

/**
 * All comentary sections.
 *
 * @return array
 */
function commentarylocs()
{
    global $session;

    tlschema('commentary');

    $comsecs = datacache('commentary-comsecs', 600, true);

    if (is_array($comsecs) && count($comsecs))
    {
        return $comsecs;
    }

    $vname = getsetting('villagename', LOCATION_FIELDS);
    $iname = getsetting('innname', LOCATION_INN);
    $comsecs['village'] = sprintf_translate('%s Square', $vname);

    if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO)
    {
        $comsecs['superuser'] = translate_inline('Grotto');
    }
    $comsecs['shade'] = translate_inline('Land of the Shades');
    $comsecs['grassyfield'] = translate_inline('Grassy Field');
    $comsecs['inn'] = $iname;
    $comsecs['motd'] = translate_inline('MotD');
    $comsecs['veterans'] = translate_inline('Veterans Club');
    $comsecs['hunterlodge'] = translate_inline("Hunter's Lodge");
    $comsecs['gardens'] = translate_inline('Gardens');
    $comsecs['waiting'] = translate_inline('Clan Hall Waiting Area');

    if (1 == getsetting('betaperplayer', 1) && file_exists('pavilion.php'))
    {
        $comsecs['beta'] = translate_inline('Pavilion');
    }

    // All of the ones after this will be translated in the modules.
    $comsecs = modulehook('moderate', $comsecs);

    updatedatacache('commentary-comsecs', $comsecs, true);

    rawoutput(tlbutton_clear());

    tlschema();

    return $comsecs;
}

/**
 * Hidde a commentary.
 *
 * @param int    $cid
 * @param string $reason
 * @param int    $mod
 */
function removecommentary($cid, $reason, $mod)
{
    $select = DB::select('commentary');
    $select->columns(['*'])
        ->where->equalTo('commentid', $cid)
    ;
    $result = DB::execute($select);
    $row = DB::fetch_assoc($result);

    $row['info'] = unserialize($row['info']);
    $row['info']['hidecomment'] = 1;
    $row['info']['hidereason'] = $reason;
    $row['info']['hiddenby'] = $mod;

    if (isset($row['info']['restored']))
    {
        unset($row['info']['restored']);
    }

    $update = DB::update('commentary');
    $update->set(['info' => serialize($row['info'])])
        ->where->equalTo('commentid', $cid)
    ;
    DB::execute($update);

    invalidatedatacache("commentary-latestcommentary_{$row['section']}");
    invalidatedatacache("commentary-commentarycount_{$row['section']}");
    invalidatedatacache("commentary-whosonline_{$row['section']}");
}

/**
 * Restore a hidden commentary.
 *
 * @param [type] $cid
 * @param [type] $reason
 * @param [type] $mod
 */
function restorecommentary($cid, $reason, $mod)
{
    $select = DB::select('commentary');
    $select->columns(['*'])
        ->where->equalTo('commentid', $cid)
    ;
    $result = DB::execute($select);
    $row = DB::fetch_assoc($result);

    $row['info'] = unserialize($row['info']);
    unset($row['info']['hidecomment']);

    $time = time();
    $row['info']['restored'][$time]['reason'] = $reason;
    $row['info']['restored'][$time]['mod'] = $mod;

    $update = DB::update('commentary');
    $update->set(['info' => serialize($row['info'])])
        ->where->equalTo('commentid', $cid)
    ;
    DB::execute($update);

    invalidatedatacache("commentary/latestcommentary_{$row['section']}");
    invalidatedatacache("commentary/commentarycount_{$row['section']}");
    invalidatedatacache("commentary/whosonline_{$row['section']}");
}

/**
 * Clean commentary for insert in DB.
 *
 * @param string $comment
 *
 * @return string
 */
function commentcleanup($comment)
{
    $filterChain = new Zend\Filter\FilterChain();
    $filterChain
        ->attach(new Zend\Filter\StringTrim())
        ->attach(new Zend\Filter\StripTags())
        ->attach(new Zend\Filter\StripNewlines())
        ->attach(new Zend\Filter\PregReplace(['pattern' => '/`n/', 'replacement' => '']))
        ->attach(new Zend\Filter\PregReplace(['pattern' => "'([^[:space:]]{45,45})([^[:space:]])'", 'replacement' => '\\1 \\2']))
        ->attach(new Zend\Filter\Callback([new HTMLPurifier(), 'purify']))
    ;

    $comment = $filterChain->filter($comment);

    //Try to make it so that italics are always closed properly
    $italics = substr_count($comment, '`i');

    if ($italics && $italics % 2)
    {
        $comment .= '`i';
    }

    if ('`0' != substr($comment, -2))
    {
        $comment.'`0';
    } //-- For close color tag

    //-- Process comment
    $comment = modulehook('commentary-comment', ['comment' => $comment]);
    $comment = $comment['comment'];

    return $comment;
}

/**
 * Add a comment.
 *
 * @return void|false
 */
function addcommentary()
{
    global $session, $emptypost, $afk, $dni, $output;

    $section = httppost('section');
    $talkline = httppost('talkline');
    $schema = httppost('schema');
    $comment = commentcleanup(httppost('insertcommentary'));
    $counter = httppost('counter');
    $remove = (int) httpget('removecomment');
    $restore = (int) httpget('restorecomment');

    if (httpget('bulkdelete'))
    {
        $everything = httpallpost();

        foreach ($everything as $key => $val)
        {
            if ('deletecomment_' == substr($key, 0, 14))
            {
                $del = str_replace('deletecomment_', '', $key);
                removecommentary($del, "Mass deletion by {$session['user']['name']}", $session['user']['acctid']);
            }
        }
    }

    if ($remove > 0)
    {
        removecommentary($remove, "Moderated by {$session['user']['name']}", $session['user']['acctid']);
    }

    if ($restore > 0)
    {
        restorecommentary($restore, "Restored by {$session['user']['name']}", $session['user']['acctid']);
    }

    if (! $comment)
    {
        return false;
    }

    if (isset($session['user']['chatloc']) && 'DNI' == $session['user']['chatloc'])
    {
        $dni = true;
    }

    //-- Process commands
    //--------------------
    if ($comment == strtoupper($comment))
    {
        //this is an all-uppercase entry.  Do not add this comment to the database; instead, check it for built-in stuff like AFK and GREM, then run it through the commentarycommand hook
        if ('AFK' == $comment || 'BRB' == $comment)
        {
            $session['user']['chatloc'] = 'AFK';
            $afk = true;
            output('`0`n`c`bYou are Away From the Keyboard until you load another page.`b´c`n');

            return false;
        }
        elseif ('DNI' == $comment)
        {
            if ('DNI' == $session['user']['chatloc'])
            {
                $session['user']['chatloc'] = $section;
                $dni = false;
                output('`0`n`c`bYou are no longer in Do Not Interrupt status.`b´c`n');
            }
            else
            {
                $session['user']['chatloc'] = 'DNI';
                $dni = true;
                output("`0`n`c`bYou are in Do Not Interrupt status.  Type DNI again to leave.`b`nDNI status is used for whenever you're doing or saying something that means other players shouldn't try to interact with you.  For example, when two or more characters are chatting just outside of the main group of characters, and other characters shouldn't be able to hear them.´c`n");
            }

            return false;
        }
        //-- Delete last comment
        elseif ('GREM' == $comment)
        {
            //handle deleting the player's last comment
            $sql = 'SELECT * FROM '.DB::prefix('commentary')." WHERE author='".$session['user']['acctid']."' ORDER BY commentid DESC LIMIT 1";
            $select = DB::select('commentary');
            $select->columns(['*'])
                ->limit(1)
                ->order('commentid DESC')
                ->where->equalTo('author', $session['user']['acctid'])
            ;
            $row = DB::execute($select)->current();

            if ($row)
            {
                $then = strtotime($row['postdate']);
                $ago = time() - $then;

                if ($ago < 120)
                {
                    removecommentary($row['commentid'], 'Typo Gremlin', $session['user']['acctid']);
                    output('`0`n`c`bA nearby Typo Gremlin notices the peculiar tastiness of your previous comment.  Within moments, a small horde of them have descended upon your words, and consumed them.`b´c`n');
                }
                else
                {
                    output("`0`n`c`bThe Typo Gremlins turn up their nose at your latest comment - it's just too old.  They have no taste for stale words.`b´c`n");
                }
            }

            return false;
        }

        //-- Process additional commands
        $returnedhook = modulehook('commentarycommand', ['command' => $comment, 'section' => $section]);

        if (isset($returnedhook['skipcommand']) && ! $returnedhook['skipcommand'])
        {
            //if for some reason you're going to involve a command that can be a mix of upper and lower case, set $args['skipcommand'] and $args['ignore'] to true and handle it in postcomment instead.
            if (isset($returnedhook['processed']) && ! $returnedhook['processed'])
            {
                output("`c`b`JCommand Not Recognized`b`0`nWhen you type in ALL CAPS, the game doesn't think you're talking to other players; it thinks you're trying to perform an action within the game.  For example, typing `#GREM`0 will remove the last comment you posted, as long as you posted it less than two minutes ago.  Typing `#AFK`0 or `#BRB`0 will turn your online status bar grey, so that people know you're `#A`0way `#F`0rom the `#K`0eyboard (or, if you prefer, that you'll `#B`0e `#R`0ight `#B`0ack).  Typing `#DNI`0 will let other players know that you're busy talking to one particular player - maybe somewhere off-camera - and that you don't want to be interrupted right now.`nSome areas have special hidden commands or other easter eggs that you can hunt for.  This time around, you didn't trigger anything special.´c`0`n");
            }

            return false;
        }
    }

    if ($section || $talkline || $comment)
    {
        $tcom = color_sanitize($comment);

        if ('' != $tcom && ':' != $tcom && '::' != $tcom && '/me' != $tcom)
        {
            $comment = comment_sanitize($comment);
            injectcommentary($section, $talkline, $comment);
        }
    }
}

/**
 * Add a new comment.
 *
 * @param string $section
 * @param string $talkline
 * @param string $comment
 */
function injectcommentary($section, $talkline, $comment)
{
    global $session, $doublepost;

    // Make the comment pristine so that we match on it correctly.
    $comment = commentcleanup($comment);
    $doublepost = false;
    $emptypost = 0;
    $colorcount = 0;

    if ('' == $comment)
    {
        return false;
    }

    $commentary = soap($comment);

    $info = [];
    $info['rawcomment'] = $comment;

    if ($session['user']['clanid'] && $session['user']['clanrank'])
    {
        $clanrow = datacache("commentary-claninfo-{$session['user']['clanid']}-{$session['user']['acctid']}", 86400, true);

        if (is_array($clanrow) && ! empty($clanrow))
        {
            $info = array_merge($info, $clanrow);
        }
        else
        {
            $select = DB::select('clans');
            $select->columns(['clanname', 'clanshort', 'clanid'])
                ->limit(1)
                ->where->equalTo('clanid', $session['user']['clanid'])
            ;

            $clanrow = DB::execute($select)->current();

            $clanrow['clanrank'] = $session['user']['clanrank'];

            $info = array_merge($info, $clanrow);

            updatedatacache("commentary-claninfo-{$session['user']['clanid']}-{$session['user']['acctid']}", $clanrow, true);
        }
    }

    if (! isset($session['user']['prefs']['ucol']))
    {
        $session['user']['prefs']['ucol'] = false;
    }
    else
    {
        $info['talkcolour'] = $session['user']['prefs']['ucol'];
    }

    $args = ['commentline' => $commentary, 'commenttalk' => $talkline, 'info' => $info, 'name' => $session['user']['name'], 'section' => $section];
    $args = modulehook('postcomment', $args);
    //debug($args);

    //A module tells us to ignore this comment, so we will
    if (isset($args['ignore']) && 1 == $args['ignore'])
    {
        return false;
    }

    $commentary = $args['commentline'];
    $talkline = $args['commenttalk'];
    $info = $args['info'];
    $name = $args['name'];

    // Sort out /game switches
    if ('/game' == substr($commentary, 0, 5) && SU_IS_GAMEMASTER == ($session['user']['superuser'] & SU_IS_GAMEMASTER))
    {
        //handle game master inserts now, allow double posts
        injectsystemcomment($section, $commentary);
    }
    else
    {
        //check for double posts
        $commentbuffer = datacache("commentary-latestcommentary_$section", 60, true);

        if (is_array($commentbuffer))
        {
            if ($commentbuffer[0]['comment'] == $commentary)
            {
                $doublepost = true;
            }
        }
        else
        {
            $select = DB::select('commentary');
            $select->columns(['*'])
                ->limit(1)
                ->order('commentid DESC')
                ->where->equalTo('section', $section)
                    ->equalTo('author', $session['user']['acctid'])
            ;
            $row = DB::execute($select)->current();

            if (is_array($row))
            {
                if ($row['comment'] == $commentary)
                {
                    $doublepost = true;
                }

                $commentbuffer = [
                    'postdate' => $row['postdate'],
                    'section' => $row['section'],
                    'author' => $row['author'],
                    'comment' => $row['comment'],
                    'name' => $row['name'],
                    'info' => $row['info']
                ];

                updatedatacache("commentary-latestcommentary_$section", [$commentbuffer], true);
            }
        }

        if (! $doublepost)
        {
            //Not a double post, inject the comment
            injectrawcomment($section, $session['user']['acctid'], $commentary, $session['user']['name'], $info);
            $session['user']['laston'] = date('Y-m-d H:i:s');
        }
    }
}

/**
 * Lets system put comments without a user association...be careful, it is not trackable who posted it.
 *
 * @param string $section
 * @param string $comment
 *
 * @return injectrawcomment
 */
function injectsystemcomment($section, $comment)
{
    if (0 !== strncmp($comment, '/game', 5))
    {
        $comment = '/game'.$comment;
    }

    return injectrawcomment($section, 0, $comment);
}

/**
 * Lets gamemasters put raw comments.
 *
 * @param string $section
 * @param int    $author
 * @param string $comment
 * @param string $name
 * @param array  $info
 */
function injectrawcomment($section, $author, $comment, $name = false, $info = false)
{
    if (is_array($info))
    {
        $sqlinfo = serialize($info);
    }
    else
    {
        $sqlinfo = serialize([$info]);
    }

    $newcomment = [
        'postdate' => date('Y-m-d H:i:s'),
        'section' => $section,
        'author' => $author,
        'comment' => $comment,
        'name' => $name,
        'info' => $sqlinfo
    ];

    $insert = DB::insert('commentary');
    $insert->values($newcomment);

    DB::execute($insert);

    //update datacache for latest commentary - no need to invalidate this and then rebuild it again, just pop one off the start and put our new one on the end
    $commentbuffer = datacache("commentary-latestcommentary_$section", 60, true);

    if (is_array($commentbuffer))
    {
        array_unshift($commentbuffer, $newcomment);
        updatedatacache("commentary-latestcommentary_$section", $commentbuffer, true);
    }

    //commentary count - no need to invalidate this and then rebuild it again, just increment it by one
    $count = (int) datacache("commentary-commentarycount_$section", 60, true);

    if ($count)
    {
        updatedatacache("commentary-commentarycount_$section", $count++, true);
    }

    invalidatedatacache("commentary-whosonline_$section");
}

/**
 * Display comentary block.
 *
 * @param string $intro
 * @param string $section
 * @param string $message
 * @param int    $limit
 * @param string $talkline
 * @param bool   $schema
 */
function commentdisplay($intro, $section, $message = 'Interject your own commentary?', $limit = 10, $talkline = 'says', $schema = false)
{
    // Let's add a hook for modules to block commentary sections
    $args = modulehook('blockcommentarea', ['section' => $section]);

    if (isset($args['block']) && ('yes' == $args['block']))
    {
        return;
    }

    if ($intro)
    {
        output($intro);
    }

    viewcommentary($section, $message, $limit, $talkline, $schema);
}

/**
 * View all comments.
 *
 * @param string       $section
 * @param string       $message
 * @param int          $limit
 * @param string       $talkline
 * @param bool         $schema
 * @param bool         $skipfooter
 * @param false|string $customsql
 * @param bool         $skiprecentupdate
 * @param bool         $overridemod
 */
function viewcommentary($section, $message = 'Interject your own commentary?', $limit = 25, $talkline = 'says', $schema = false, $skipfooter = false, $customsql = false, $skiprecentupdate = false, $overridemod = false)
{
    global $session, $REQUEST_URI, $doublepost, $translation_namespace, $emptypost, $chatloc, $afk, $dni, $moderating, $fiveminuteload;

    tlschema('commentary');

    if (! array_key_exists('commentary_auto_update', $session['user']['prefs']))
    {
        $session['user']['prefs']['commentary_auto_update'] = 1;
    }

    if (httpget('disable_auto_update'))
    {
        $session['user']['prefs']['commentary_auto_update'] = 0;
    }

    if (httpget('enable_auto_update'))
    {
        $session['user']['prefs']['commentary_auto_update'] = 1;
    }

    if (! isset($returnlink) || ! $returnlink)
    {
        $returnlink = urlencode($REQUEST_URI);
    }

    if (($session['user']['superuser'] & SU_EDIT_COMMENTS) || $overridemod)
    {
        $showmodlink = true;
    }
    else
    {
        $showmodlink = false;
    }

    if (! $skiprecentupdate)
    {
        $session['recentcomments'] = date('Y-m-d H:i:s');
    }

    output_notl('`n');
    rawoutput('<!--start of commentary area--><a name="commentarystart"></a>');

    if ($session['user']['prefs']['commentary_auto_update'])
    {
        $timeout = translate_inline('Auto-update has timed out. Click any link to restart the clock.');
        rawoutput("<script>setInterval(function () { Lotgd.loadnewchat(\"ajaxcommentarydiv$section\", \"$section\", \"$message\", $limit, \"$talkline\", \"$returnlink\", \"$timeout\") }, 3000)</script>");
        rawoutput("<div id='ajaxcommentarydiv$section'>");
    }

    $out = preparecommentaryblock($section, $message, $limit, $talkline, $schema, $skipfooter, $customsql, $skiprecentupdate, $overridemod, $returnlink);
    $commentary = appoencode($out, true);
    rawoutput($commentary);

    if ($session['user']['prefs']['commentary_auto_update'])
    {
        rawoutput('</div>');
    }

    if (! $skipfooter)
    {
        commentaryfooter($section, $message, $limit, $talkline, $schema);
    }

    rawoutput('<!--end of commentary area-->');

    tlschema();
}

/**
 * Prepare block of commentaries.
 *
 * @param [type] $section
 * @param string $message
 * @param int    $limit
 * @param string $talkline
 * @param bool   $schema
 * @param bool   $skipfooter
 * @param bool   $customsql
 * @param bool   $skiprecentupdate
 * @param bool   $overridemod
 * @param bool   $returnlink
 */
function preparecommentaryblock($section, $message = 'Interject your own commentary?', $limit = 25, $talkline = 'says', $schema = false, $skipfooter = false, $customsql = false, $skiprecentupdate = false, $overridemod = false, $returnlink = false)
{
    global $session, $REQUEST_URI, $doublepost, $translation_namespace, $emptypost, $chatloc, $afk, $dni, $moderating;

    $com = max((int) httpget('comscroll'), 0);

    if (($session['user']['superuser'] & SU_EDIT_COMMENTS) || $overridemod)
    {
        $showmodlink = true;
    }
    else
    {
        $showmodlink = false;
    }

    $ret = '';

    //skip assigning chatloc if this chatloc's id ends with "_aux" - this way we can have dual chat areas
    if (! $afk && (! isset($session['user']['chatloc']) || 'DNI' != $session['user']['chatloc']))
    {
        if ('_aux' != substr($section, strlen($section) - 4, strlen($section)))
        {
            $chatloc = $section;
        }
        else
        {
            $chatloc = substr($section, 0, -4);
        }

        $session['user']['chatloc'] = $chatloc;
    }
    else
    {
        $chatloc = 'AFK';
    }

    if ($section)
    {
        $ret .= "<a name='$section'></a>";
        // Let's add a hook for modules to block commentary sections
        $args = modulehook('blockcommentarea', ['section' => $section]);

        if (isset($args['block']) && ('yes' == $args['block']))
        {
            return;
        }
    }

    $commentbuffer = getcommentary($section, $limit, $talkline, $customsql, $showmodlink, $returnlink);
    $rowcount = count($commentbuffer);

    if ($doublepost)
    {
        $ret .= '`$`bDouble post?`b`0`n';
    }

    if ($emptypost)
    {
        $ret .= '`$`bWell, they say silence is a virtue.`b`0`n';
    }

    //output the comments!

    $start = microtime(true);

    if (($moderating && $rowcount) || ! $moderating)
    {
        if ($showmodlink)
        {
            $link = buildcommentarylink("&bulkdelete=true&comscroll=$com");
            $ret .= "<form action='$link' id='bulkdelete' method='post'>";
            $ret .= '<input type="submit" class="ui button" value="Mass Delete">';
        }

        if (! isset($session['user']['prefs']['commentary_recentline']))
        {
            $session['user']['prefs']['commentary_recentline'] = 1;
        }

        $ret .= '<div class="ui commentary list">';

        if (! $session['user']['prefs']['commentary_reverse'])
        {
            $commentbuffer = array_reverse($commentbuffer);
        }

        if (! isset($session['recentcomments']))
        {
            $session['recentcomments'] = 0;
        }

        foreach ($commentbuffer as $i => $comment)
        {
            if ($comment['postdate'] > $session['recentcomments'] && $session['user']['prefs']['commentary_recentline'])
            {
                $new = 1;
            }
            else
            {
                $new = 0;
            }

            $ret .= preparecommentaryline($comment);
        }
        $ret .= '</div>';

        if ($showmodlink)
        {
            $ret .= '</form>';
        }
    }

    $end = microtime(true);

    return $ret;
}

/**
 * Get comments.
 *
 * @param string       $section
 * @param int          $limit
 * @param string       $talkline
 * @param false|string $customsql
 * @param bool         $showmodlink
 * @param bool         $returnlink
 */
function getcommentary($section, $limit = 25, $talkline, $customsql = false, $showmodlink = false, $returnlink = false)
{
    global $session, $REQUEST_URI, $translation_namespace, $chatloc, $bottomcid;

    tlschema('commentary');

    $com = max((int) httpget('comscroll'), 0);

    if (! $returnlink)
    {
        $returnlink = urlencode($REQUEST_URI);
    }

    //stops people from clicking on Bio links in the MoTD
    $nobios = ['motd.php' => true, 'runmodule.php?module=global_banter' => true];

    if (! array_key_exists(basename($_SERVER['SCRIPT_NAME']), $nobios))
    {
        $nobios[basename($_SERVER['SCRIPT_NAME'])] = false;
    }

    if ($nobios[basename($_SERVER['SCRIPT_NAME'])])
    {
        $linkbios = false;
    }
    else
    {
        $linkbios = true;
    }

    // Needs to be here because scrolling through the commentary pages, entering a bio, then scrolling again forward
    // then re-entering another bio will lead to $com being smaller than 0 and this will lead to an SQL error later on.
    $session['lastcom'] = $session['lastcom'] ?? 0;
    if (false !== httpget('comscroll') && (int) $session['lastcom'] == $com + 1)
    {
        $cid = (int) $session['lastcommentid'];
    }
    else
    {
        $cid = 1;
    }

    $session['lastcom'] = $com;

    $start = microtime(true);
    $commentbuffer = datacache("commentary-latestcommentary_$section$com", 30);

    if (! is_array($commentbuffer) || empty($commentbuffer))
    {
        if ($customsql)
        {
            $result = DB::query($customsql);
        }
        else
        {
            $select = DB::select(['c' => 'commentary']);
            $select->order('c.commentid DESC')
                ->join(['a' => 'accounts'], 'a.acctid = c.author', ['acctid', 'laston', 'loggedin', 'chatloc'], 'LEFT')
                ->where->equalTo('c.section', $section)
            ;

            $result = DB::paginator($select, $com, $limit);
        }

        $commentbuffer = [];

        foreach ($result as $row)
        {
            $row['info'] = @unserialize($row['info']);

            if (! is_array($row['info']))
            {
                $row['info'] = [];
            }
            $row['info']['link'] = buildcommentarylink("&commentid={$row['commentid']}");
            $row['skiptalkline'] = false;
            $row['gamecomment'] = false;
            $row['info']['icons'] = [];
            $commentbuffer[] = $row;
        }

        updatedatacache("commentary-latestcommentary_$section$com", $commentbuffer);
    }

    $end = microtime(true);
    $tot = $end - $start;

    //pre-formatting
    $commentbuffer = modulehook('commentbuffer-preformat', $commentbuffer);

    $rowcount = count($commentbuffer);

    if ($rowcount > 0)
    {
        $session['lastcommentid'] = $commentbuffer[0]['commentid'];
    }

    //figure out whether to handle absolute or relative time
    if (! array_key_exists('timestamp', $session['user']['prefs']))
    {
        $session['user']['prefs']['timestamp'] = 0;
    }

    if (isset($session['user']['prefs']['timeoffset']))
    {
        $session['user']['prefs']['timeoffset'] = round($session['user']['prefs']['timeoffset'], 1);
    }
    else
    {
        $session['user']['prefs']['timeoffset'] = 0;
    }

    if (! array_key_exists('commentary_reverse', $session['user']['prefs']))
    {
        $session['user']['prefs']['commentary_reverse'] = 0;
    }

    //this array of userids means that with a single query we can figure out who's online and nearby
    $acctidstoquery = [];

    //prepare the actual comment line part of the comment - is it hidden, is it an action, is it a game comment, should we show a moderation link, clan rank colours, posting date abs/rel
    $loop1start = microtime(true);
    $bioretlink = urlencode(buildcommentarylink('&frombio=true', $returnlink));
    $del = 'Del';
    $undel = 'UNDel';

    $clanrankcolors = [CLAN_APPLICANT => '`!', CLAN_MEMBER => '`3', CLAN_OFFICER => '`^', CLAN_LEADER => '`&', CLAN_FOUNDER => '`4'];
    $offline = date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds'));

    foreach ($commentbuffer as $i => $comment)
    {
        if ((! isset($commentbuffer[$i]['info']['hidecomment']) || ! $commentbuffer[$i]['info']['hidecomment']) || $showmodlink)
        {
            $thiscomment = '';
            $row = &$commentbuffer[$i];
            $row['acctid'] = $row['author'];

            if (':' == substr($row['comment'], 0, 1) || '/me' == substr($row['comment'], 0, 3))
            {
                $row['skiptalkline'] = true;

                if ('/me' == substr($row['comment'], 0, 3))
                {
                    $row['comment'] = substr($row['comment'], 3);
                }
                elseif ('::' == substr($row['comment'], 0, 2))
                {
                    $row['comment'] = substr($row['comment'], 2);
                }
                elseif (':' == substr($row['comment'], 0, 1))
                {
                    $row['comment'] = substr($row['comment'], 1);
                }
            }

            if (isset($row['gamecomment']) && $row['gamecomment'] || ('/game' == substr($row['comment'], 0, 5) && ! $row['name']))
            {
                $row['gamecomment'] = true;
                $row['skiptalkline'] = true;
                $row['info']['icons'] = [];
                $row['comment'] = str_replace('/game', '', $row['comment']);
            }

            if ($linkbios && ! isset($row['biolink']))
            {
                $row['biolink'] = true;
            }

            if ($showmodlink)
            {
                if (isset($row['info']['hidecomment']) && $row['info']['hidecomment'])
                {
                    $restorelink = buildcommentarylink("&restorecomment={$row['commentid']}&comscroll=$com");
                    $thiscomment = sprintf('[<a href="%s">`@%s`0</a>] <del>', $restorelink, $undel);
                }
                else
                {
                    $removelink = buildcommentarylink("&removecomment={$row['commentid']}&comscroll=$com");
                    $thiscomment = sprintf('[<a href="%s">`$%s`0</a>] <input type="checkbox" name="deletecomment_%s">', $removelink, $del, $row['commentid']);
                }
            }

            if (! $row['gamecomment'] && isset($row['info']['clanid']) && ($row['info']['clanid'] || 0 === $row['info']['clanid']) && $row['info']['clanrank'])
            {
                $thiscomment = sprintf('%s <a class="ui tooltip" title="%s">`$&lt;%s%s`$&gt;`0</a>',
                    $thiscomment,
                    $row['info']['clanname'],
                    $clanrankcolors[$row['info']['clanrank']],
                    $row['info']['clanshort']
                );
            }

            if (! $row['gamecomment'])
            {
                if ($row['biolink'])
                {
                    $bio = buildcommentarylink("?char={$row['acctid']}&ret=$bioretlink", 'bio.php');
                    $thiscomment = sprintf('%s <a href="%s">`&%s`0</a>', $thiscomment, $bio, $row['name']);
                }
                else
                {
                    $thiscomment = sprintf('%s `&%s`0', $thiscomment, $row['name']);
                }
            }

            if (! $row['skiptalkline'])
            {
                $thiscomment = sprintf('%s `3%s, "%s%s`3"`0 %s',
                    $thiscomment,
                    $talkline,
                    (! isset($row['info']['talkcolour']) || false === $row['info']['talkcolour']) ? '`#' : '`'.$row['info']['talkcolour'],
                    str_replace('&amp;', '&', htmlentities($row['comment'], ENT_COMPAT, getsetting('charset', 'UTF-8'))),
                    (isset($row['info']['hidecomment']) && $row['info']['hidecomment']) ? '</del>' : ''
                );
            }
            else
            {
                $thiscomment = sprintf('%s %s%s%s %s',
                    $thiscomment,
                    false == $row['gamecomment'] ? '`i`)' : '`i`b`7',
                    str_replace('&amp;', '&', htmlentities($row['comment'], ENT_COMPAT, getsetting('charset', 'UTF-8'))),
                    false == $row['gamecomment'] ? '`i`0' : '`b`i`0',
                    (isset($row['info']['hidecomment']) && $row['info']['hidecomment']) ? '</del>' : ''
                );
            }

            $commentbuffer[$i]['comment'] = $thiscomment;
            $commentbuffer[$i]['icons'] = $row['info']['icons'];
            $commentbuffer[$i]['time'] = strtotime($row['postdate']);

            if (1 == $session['user']['prefs']['timestamp'])
            {
                if (! isset($session['user']['prefs']['timeformat']))
                {
                    $session['user']['prefs']['timeformat'] = '[m/d h:ia]';
                }
                $time = strtotime($row['postdate']) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
                $s = date('`7'.$session['user']['prefs']['timeformat'].'`0 ', $time);
                $commentbuffer[$i]['displaytime'] = $s;
            }
            elseif (2 == $session['user']['prefs']['timestamp'])
            {
                $s = reltime(strtotime($row['postdate']));
                $commentbuffer[$i]['displaytime'] = "`7[$s]`0 ";
            }

            //-- Add basic status icons for online/offline/nearby/afk/dnd
            if ('AFK' == $row['chatloc'])
            {
                $commentbuffer[$i]['info']['online'] = -1;
                $icon = [
                    'icon' => 'images/icons/onlinestatus/afk.png',
                    'mouseover' => translate_inline('Away from Keyboard'),
                ];
                $commentbuffer[$i]['info']['icons']['online'] = $icon;
            }
            elseif ('DNI' == $row['chatloc'])
            {
                $commentbuffer[$i]['info']['online'] = -1;
                $icon = [
                    'icon' => 'images/icons/onlinestatus/dni.png',
                    'mouseover' => translate_inline("DNI (please don't try to talk to this player right now!)"),
                ];
                $commentbuffer[$i]['info']['icons']['online'] = $icon;
            }
            elseif ($row['laston'] < $offline || ! $row['loggedin'])
            {
                $commentbuffer[$i]['info']['online'] = 0;
                $commentbuffer[$i]['info']['icons']['online'] = [
                    'icon' => 'images/icons/onlinestatus/offline.png',
                    'mouseover' => translate_inline('Offline'),
                ];
            }
            elseif ($row['chatloc'] == $chatloc)
            {
                $commentbuffer[$i]['info']['online'] = 2;
                $commentbuffer[$i]['info']['icons']['online'] = [
                    'icon' => 'images/icons/onlinestatus/nearby.png',
                    'mouseover' => translate_inline('Nearby'),
                ];
            }
            else
            {
                $commentbuffer[$i]['info']['online'] = 1;
                $commentbuffer[$i]['info']['icons']['online'] = [
                    'icon' => 'images/icons/onlinestatus/online.png',
                    'mouseover' => translate_inline('Online'),
                ];
            }
        }
        else
        {
            unset($commentbuffer[$i]);
        }

        if (isset($commentbuffer[$i]))
        {
            $bottomcid = $commentbuffer[$i]['commentid'];
        }
    }

    //send through a modulehook for additional processing by modules
    $commentbuffer = modulehook('commentbuffer', $commentbuffer);

    tlschema();

    return $commentbuffer;
}

/**
 * Create a line of comment.
 *
 * @param array $line
 */
function preparecommentaryline($line)
{
    $finaloutput = '<div class="item">';

    if (! isset($line['gamecomment']) || ! $line['gamecomment'])
    {
        $icons = $line['info']['icons'];

        //make it so that online icons always show up first
        $online = $icons['online'];
        unset($icons['online']);

        if (isset($online['icon']))
        {
            $finaloutput .= sprintf('<img src="%s" title="%s" alt="%s"> ', $online['icon'], $online['mouseover'], $online['mouseover']);
        }

        //show other icons
        if (count($icons))
        {
            foreach ($icons as $key => $vals)
            {
                if (file_exists($vals['icon']))
                {
                    $finaloutput .= sprintf('<img src="%s" title="%s" alt="%s"> ', $vals['icon'], $vals['mouseover'], $vals['mouseover']);
                }
                else
                {
                    $finaloutput .= $vals['mouseover'].' ';
                }
            }
        }
    }

    $finaloutput .= (isset($line['displaytime']) ? $line['displaytime'] : '');
    $finaloutput .= $line['comment'];

    $finaloutput .= '</div>';

    return $finaloutput;
}

/**
 * Footer of list of pages of comments.
 *
 * @param string $section
 * @param string $message
 * @param int    $limit
 * @param string $talkline
 * @param bool   $schema
 */
function commentaryfooter($section, $message = 'Interject your own commentary?', $limit = 25, $talkline = 'says', $schema = false)
{
    global $session, $REQUEST_URI, $doublepost, $translation_namespace, $emptypost, $chatloc, $moderating, $bottomcid;

    tlschema('commentary');

    //Output page jumpers
    $com = max((int) httpget('comscroll'), 0);

    if ('all' == $section)
    {
        $sql = 'SELECT count(commentid) AS c FROM '.DB::prefix('commentary')." WHERE section NOT LIKE 'dwelling%' AND section NOT LIKE 'clan%' AND section NOT LIKE 'pet-%'";
    }
    else
    {
        $sql = 'SELECT count(commentid) AS c FROM '.DB::prefix('commentary')." WHERE section='$section'";
    }

    $r = DB::query($sql);
    $val = DB::fetch_assoc($r);
    $rowcount = $val['c'];
    $val = round($val['c'] / $limit + 0.5, 0) - 1;

    $returnlink = urlencode($REQUEST_URI);
    $returnlink = buildcommentarylink('&frombio=true', $returnlink);

    $hook = [
        'section' => $section,
        'message' => $message,
        'talkline' => $talkline,
        'returnlink' => $returnlink,
    ];

    $hook = modulehook('commentary_talkform', $hook);

    $section = $hook['section'];
    $message = $hook['message'];
    $talkline = $hook['talkline'];

    if ($session['user']['loggedin'])
    {
        if ('X' != $message)
        {
            $message = "`n`@$message`0`n";
            output($message, true);

            if (! isset($hook['blocktalkform']) || ! $hook['blocktalkform'])
            {
                talkform($section, $talkline, $limit, $schema);
            }
        }
    }

    $jump = false;

    if (! isset($session['user']['prefs']['nojump']) || false == $session['user']['prefs']['nojump'])
    {
        $jump = true;
    }

    //new-style commentary display with page numbers
    $nlink = buildcommentarylink('&refresh=1');

    //reinstating back and forward links
    output_notl('`n');
    $prev = '<i class="icon angle double left"></i>';
    $next = '<i class="icon angle double right"></i>';

    if ($rowcount >= $limit && $com != $val)
    {
        $req = buildcommentarylink('&comscroll='.($com + 1));
        output_notl("<a href=\"$req\">$prev</a> | ", true);
        addnav('', $req);
    }

    $cplink = buildcommentarylink("&comscroll=$com&refresh=1");
    addnav('', $cplink);

    output_notl("`0<a href=\"$cplink\">".translate_inline('Refresh')."</a> | <a href=\"$nlink\">".translate_inline('Latest').'</a>', true);

    if ($com > 0)
    {
        $req = buildcommentarylink('&comscroll='.($com - 1));
        output_notl(" | <a href=\"$req\">$next</a>", true);
        addnav('', $req);
    }

    output_notl('`n');

    if ($session['user']['prefs']['commentary_auto_update'])
    {
        $req = buildcommentarylink('&disable_auto_update=true');
        addnav('', $req);
        output_notl(" <a href=\"$req\">".translate_inline('Disable Auto-Update').'</a>', true);
        rawoutput("<div id ='ajaxcommentarynoticediv$section'></div>");
    }
    else
    {
        $req = buildcommentarylink('&enable_auto_update=true');
        output_notl(" <a href=\"$req\">".translate_inline('Enable Auto-Update').'</a>', true);
        addnav('', $req);
    }

    rawoutput('<div id="typedisplay'.$section.'"></div>');

    addnav('', $nlink);
    output('`n`n`0Jump to commentary page: ');
    $start = microtime(true);
    $nlink = buildcommentarylink('&refresh=1&comscroll=');

    for ($i = $val; $i >= 0; $i--)
    {
        // $nlink = buildcommentarylink("&comscroll=".$i."&refresh=1");
        $ndisp = 1 + $val - $i;

        if ($com != $i)
        {
            output_notl('<a href="'.$nlink.$i."\">$ndisp</a> ", true);
            addnav('', $nlink.$i);
        }
        else
        {
            output_notl("`@$ndisp`0 ", true);
        }
    }
    $end = microtime(true);
    // $tot = $end - $start;
    //debug("commentary footer page numbers loop: ".$tot);
    output_notl('`n');

    if ($moderating)
    {
        output('`bLast Comment ID shown on this page: %s`b`n', LotgdFormat::numeral($bottomcid));
    }
    else
    {
        modulehook('commentaryoptions');
    }

    tlschema();
}

/**
 * Create a link for comment.
 *
 * @param string $append
 * @param bool   $returnlink
 *
 * @return string
 */
function buildcommentarylink($append, $returnlink = false)
{
    //TODO: Check for removecomment and restorecomment flags, possibly via regexp
    global $session, $REQUEST_URI;

    $jump = false;

    if (isset($session['user']['prefs']['nojump']) && true == $session['user']['prefs']['nojump'])
    {
        $jump = true;
    }

    if (! $returnlink)
    {
        $nlink = comscroll_sanitize($REQUEST_URI);
    }
    else
    {
        $nlink = urldecode($returnlink);

        $nlink = comscroll_sanitize($nlink);
    }

    $nlink = preg_replace("'&r(emovecomment)?=([[:digit:]]|-)*'", '', $nlink);
    $nlink = preg_replace("'\\?r(emovecomment)?=([[:digit:]]|-)*'", '?', $nlink);
    $nlink = preg_replace("'&r(estorecomment)?=([[:digit:]]|-)*'", '', $nlink);
    $nlink = preg_replace("'\\?r(estorecomment)?=([[:digit:]]|-)*'", '?', $nlink);

    //solve world map re-fight exploit
    $nlink = preg_replace('/op=fight/i', 'op=continue', $nlink);

    //stop multiple addnav counters getting added to the end
    $nlink = preg_replace("'&c?=([[:digit:]]|-)*'", '', $nlink);
    $nlink = preg_replace("'\\?c?=([[:digit:]]|-)*'", '?', $nlink);

    $nlink = preg_replace('/[?|&]enable_auto_update=true/i', '', $nlink);
    $nlink = preg_replace('/[?|&]disable_auto_update=true/i', '', $nlink);
    $nlink = preg_replace('/[?|&]bulkdelete=true/i', '', $nlink);
    $nlink = str_replace('&frombio=true', '', $nlink);
    $nlink .= $append;
    $nlink = str_replace(['.php&', '.php?&'], '.php?', $nlink);
    $nlink = preg_replace('/[?|&]switchstack=[0-9]*/i', '', $nlink);
    $nlink = preg_replace('/[?|&]switchmultichat=[0-9]*/i', '', $nlink);

    if (! strpos($nlink, '?'))
    {
        $nlink = str_replace('&', '?', $nlink);

        if (! strpos($nlink, '?'))
        {
            $nlink .= '?';
        }
    }

    if ($jump && $section)
    {
        $nlink .= '#commentarystart';
    }

    addnav('', $nlink);

    return $nlink;
}

/**
 * Form for talk in section.
 *
 * @param string $section
 * @param string $talkline
 * @param int    $limit
 * @param bool   $schema
 */
function talkform($section, $talkline, $limit = 10, $schema = false)
{
    require_once 'lib/forms.php';

    tlschema('commentary');

    global $REQUEST_URI, $session, $translation_namespace, $chatsonpage, $fiveminuteload;

    if (false === $schema)
    {
        $schema = $translation_namespace;
    }

    $jump = true;

    if (isset($session['user']['prefs']['nojump']) && true == $session['user']['prefs']['nojump'])
    {
        $jump = false;
    }

    if ($jump && httpget('comment') && httppost('focus') == $section)
    {
        $focus = true;
    }
    else
    {
        $focus = false;
    }

    if (! isset($session['user']['prefs']['ucol']))
    {
        $session['user']['prefs']['ucol'] = false;
    }

    if ('says' != translate_inline($talkline, $schema))
    {
        $tll = strlen(translate_inline($talkline, $schema)) + 11;
    }
    else
    {
        $tll = 0;
    }

    $req = buildcommentarylink('&comment=1');

    if ($jump)
    {
        $req .= "#commentaryjump_$section";
    }
    addnav('', $req);

    // *** AJAX CHAT MOD START ***
    output_notl("<form action=\"$req\" id='commentaryform' method='POST' autocomplete='false'>", true);
    // *** AJAX CHAT MOD END ***

    $add = htmlentities(translate_inline('Add'), ENT_QUOTES, getsetting('charset', 'UTF-8'));

    if ($fiveminuteload >= 8)
    {
        output('Server load is currently too high for auto-update chat.  This will hopefully balance out in a few minutes.`n');
    }
    previewfield('insertcommentary', $session['user']['name'], $talkline, true, ['size' => 30, 'maxlength' => 255 - $tll]);

    rawoutput("<input type='hidden' name='talkline' value='$talkline'>");
    rawoutput("<input type='hidden' name='schema' value='$schema'>");
    rawoutput("<input type='hidden' name='focus' value='$section'>");
    rawoutput("<input type='hidden' name='counter' value='{$session['counter']}'>");
    $session['commentcounter'] = $session['counter'];

    if ('X' == $section)
    {
        $vname = getsetting('villagename', LOCATION_FIELDS);
        $iname = getsetting('innname', LOCATION_INN);
        $sections = commentarylocs();
        reset($sections);
        output_notl("<select class='ui dropdown' name='section'>", true);

        while (list($key, $val) = each($sections))
        {
            output_notl("<option value='$key'>$val</option>", true);
        }
        output_notl('</select>', true);
    }
    else
    {
        output_notl("<input type='hidden' name='section' value='$section'>", true);
    }

    rawoutput('</form>');

    tlschema();
}

tlschema();
