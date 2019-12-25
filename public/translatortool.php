<?php

// addnews ready
// translator ready
// mail ready
define('OVERRIDE_FORCED_NAV', true);
require_once 'common.php';

/**
 * @deprecated This is a deprecated way to translated game
 *
 * This file will be deleted in a future version.
 *
 * Maybe in version 4.1.0
 */

check_su_access(SU_IS_TRANSLATOR);
$op = httpget('op');

if ('' == $op)
{
    popup_header('Translator Tool');
    $uri = rawurldecode(httpget('u'));
    $text = stripslashes(rawurldecode(httpget('t')));
    $translation = translate_loadnamespace($uri);

    if (isset($translation[$text]))
    {
        $trans = $translation[$text];
    }
    else
    {
        $trans = '';
    }
    $namespace = translate_inline('Namespace:');
    $texta = translate_inline('Text:');
    $translation = translate_inline('Translation:');
    $saveclose = htmlentities(translate_inline('Save & Close'), ENT_COMPAT, getsetting('charset', 'UTF-8'));
    $savenotclose = htmlentities(translate_inline('Save No Close'), ENT_COMPAT, getsetting('charset', 'UTF-8'));

    rawoutput("<form action='translatortool.php?op=save' method='POST' class='ui form'>");
    rawoutput("<div class='field'><label>$namespace</label> <input name='uri' value=\"".htmlentities(stripslashes($uri), ENT_COMPAT, getsetting('charset', 'UTF-8')).'" readonly></div>');
    rawoutput("<div class='field'><label>$texta</label><br>");
    rawoutput("<textarea name='text' cols='60' rows='5' readonly>".htmlentities($text, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</textarea></div>');
    rawoutput("<div class='field'><label>$translation</label><br>");
    rawoutput("<textarea name='trans' cols='60' rows='5'>".htmlentities(stripslashes($trans), ENT_COMPAT, getsetting('charset', 'UTF-8')).'</textarea></div><div class="field">');
    // rawoutput("<input type='submit' value=\"$saveclose\" class='ui button'>");
    rawoutput("<input type='submit' value=\"$savenotclose\" class='ui button' name='savenotclose'></div>");
    rawoutput('</form>');

    popup_footer();
}
elseif ('save' == $op)
{
    $uri = httppost('uri');
    $text = httppost('text');
    $trans = httppost('trans');

    $page = $uri;

    if (false !== strpos($page, '?'))
    {
        $page = substr($page, 0, strpos($page, '?'));
    }

    if ('' == $trans)
    {
        $sql = DB::delete('translations');
    }
    else
    {
        $sql = DB::select('translations');
    }
    $sql->where->equalTo('language', LANGUAGE)
        ->equalTo('intext', $text)
        ->nest()
        ->equalTo('uri', $page)
        ->or
        ->equalTo('uri', $uri)
        ->unnest()
    ;

    if ($trans > '')
    {
        $result = DB::execute($sql);
        invalidatedatacache('translations-'.$uri.'-'.$language);

        if (0 == $result->count())
        {
            $sql = DB::insert('translations');
            $sql->values([
                'language' => LANGUAGE,
                'uri' => $uri,
                'intext' => $text,
                'outtext' => $trans,
                'author' => $session['user']['login'],
                'version' => \Lotgd\Core\Application::VERSION
            ]);

            $delete = DB::delete('untranslated');
            $delete->where->equalTo('intext', $text)
                ->equalTo('language', LANGUAGE)
                ->equalTo('namespace', $url)
            ;
            DB::execute($delete);
        }
        elseif (1 == $result->count())
        {
            $row = $result->current();
            // MySQL is case insensitive so we need to do it here.
            if ($row['intext'] == $text)
            {
                $sql = DB::update('translations');
                $sql->set([
                    'author' => $session['user']['login'],
                    'version' => \Lotgd\Core\Application::VERSION,
                    'uri' => $uri,
                    'outtext' => $trans,
                ])
                    ->where->equalTo('tid', $row['tid'])
                ;
            }
            else
            {
                $sql = DB::insert('translations');
                $sql->values([
                    'language' => LANGUAGE,
                    'uri' => $uri,
                    'intext' => $text,
                    'outtext' => $trans,
                    'author' => $session['user']['login'],
                    'version' => \Lotgd\Core\Application::VERSION
                ]);

                $delete = DB::delete('untranslated');
                $delete->where->equalTo('intext', $text)
                    ->equalTo('language', LANGUAGE)
                    ->equalTo('namespace', $url)
                ;
                DB::execute($delete);
            }
        }
        elseif ($result->count() > 1)
        {
            $rows = [];

            while ($row = DB::fetch_assoc($result))
            {
                // MySQL is case insensitive so we need to do it here.
                if ($row['intext'] == $text)
                {
                    $rows['tid'] = $row['tid'];
                }
            }
            $sql = DB::update('translations');
            $sql->set([
                'author' => $session['user']['login'],
                'version' => \Lotgd\Core\Application::VERSION,
                'uri' => $page,
                'outtext' => $trans,
            ])
                ->where->int('tid', $rows)
            ;
        }
    }
    DB::execute($sql);

    if (httppost('savenotclose') > '')
    {
        header("Location: translatortool.php?op=list&u=$page");

        exit();
    }
    else
    {
        popup_header('Updated');
        rawoutput("<script language='javascript'>$('#modal-translator').modal('hide');</script>");
        popup_footer();
    }
}
elseif ('list' == $op)
{
    popup_header('Translation List');
    $sql = 'SELECT uri,count(*) AS c FROM '.DB::prefix('translations')." WHERE language='".LANGUAGE."' GROUP BY uri ORDER BY uri ASC";
    $result = DB::query($sql);
    rawoutput("<form action='translatortool.php' method='GET'>");
    rawoutput("<input type='hidden' name='op' value='list'>");
    output('Known Namespaces:');
    rawoutput("<select name='u'>");

    while ($row = DB::fetch_assoc($result))
    {
        rawoutput('<option value="'.rawurlencode(htmlentities($row['uri'], ENT_COMPAT, getsetting('charset', 'UTF-8'))).'">'.htmlentities($row['uri'], ENT_COMPAT, getsetting('charset', 'UTF-8'))." ({$row['c']})</option>", true);
    }
    rawoutput('</select>');
    $show = translate_inline('Show');
    rawoutput("<input type='submit' class='ui button' value=\"$show\">");
    rawoutput('</form>');
    $ops = translate_inline('Ops');
    $from = translate_inline('From');
    $to = translate_inline('To');
    $version = translate_inline('Version');
    $author = translate_inline('Author');
    $norows = translate_inline('No rows found');
    rawoutput("<table class='ui very compact striped selectable table'>");
    rawoutput("<tr class='trhead'><td>$ops</td><td>$from</td><td>$to</td><td>$version</td><td>$author</td></tr>");
    $sql = 'SELECT * FROM '.DB::prefix('translations')." WHERE language='".LANGUAGE."' AND uri='".httpget('u')."'";
    $result = DB::query($sql);

    if (DB::num_rows($result) > 0)
    {
        $i = 0;

        while ($row = DB::fetch_assoc($result))
        {
            $i++;
            rawoutput('<tr><td>');
            $edit = translate_inline('Edit');
            rawoutput("<a href='translatortool.php?u=".rawurlencode(htmlentities($row['uri'], ENT_COMPAT, getsetting('charset', 'UTF-8'))).'&t='.rawurlencode(htmlentities($row['intext']))."'>$edit</a>");
            rawoutput('</td><td>');
            rawoutput(htmlentities($row['intext'], ENT_COMPAT, getsetting('charset', 'UTF-8')));
            rawoutput('</td><td>');
            rawoutput(htmlentities($row['outtext'], ENT_COMPAT, getsetting('charset', 'UTF-8')));
            rawoutput('</td><td>');
            rawoutput($row['version']);
            rawoutput('</td><td>');
            rawoutput($row['author']);
            rawoutput('</td></tr>');
        }
    }
    else
    {
        rawoutput("<tr><td colspan='5'>$norows</td></tr>");
    }
    rawoutput('</table>');
    popup_footer();
}
