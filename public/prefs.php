<?php

use Lotgd\Core\Http;

// addnews ready
// mail ready
// translator ready

if (isset($_POST['template']))
{
    $skin = $_POST['template'];

    if ($skin > '')
    {
        setcookie('template', $skin, strtotime('+45 days'));
        $_COOKIE['template'] = $skin;
    }
}

require_once 'common.php';
require_once 'lib/is_email.php';
require_once 'lib/showform.php';

$textDomain = 'page-prefs';

$params = [
    'textDomain' => $textDomain,
    'selfDelete' => (int) getsetting('selfdelete', 0)
];

page_header('title', [], $textDomain);

$op = (string) \LotgdHttp::getQuery('op');

\LotgdNavigation::addHeader('common.category.navigation');
\LotgdNavigation::addNav('common.nav.update', 'prefs.php');

if ('suicide' == $op && $params['selfDelete'])
{
    $userId = $session['user']['acctid'];

    require_once 'lib/charcleanup.php';

    if (char_cleanup($userId, CHAR_DELETE_SUICIDE))
    {
        addnews('delete.character', [ 'name' => $session['user']['name'] ], 'partial-news', true);

        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.delete.character', [], $textDomain));

        $session = [];
        $session['user'] = [];
        $session['loggedin'] = false;
        $session['user']['loggedin'] = false;

        LotgdCache::removeItem('charlisthomepage');
        LotgdCache::removeItem('list.php-warsonline');

        return redirect('home.php');
    }
}
elseif ('forcechangeemail' == $op)
{
    checkday();

    if ($session['user']['alive'])
    {
        \LotgdNavigation::villageNav();
    }
    else
    {
        \LotgdNavigation::addNav('common.nav.news', 'news.php');
    }

    \LotgdNavigation::addNav('common.nav.prefs', 'prefs.php');

    $replacearray = explode('|', $session['user']['replaceemail']);
    $email = $replacearray[0];

    $params['tpl'] = 'forcechangeemail';
    $params['email'] = $email;

    debuglog('Email Change Request from '.$session['user']['emailaddress'].' to '.$email.' has been forced after the wait period', $session['user']['acctid'], $session['user']['acctid'], 'Email');

    $session['user']['emailaddress'] = $replacearray[0];
    $session['user']['replaceemail'] = '';
    $session['user']['emailvalidation'] = '';
}
elseif ('cancelemail' == $op)
{
    checkday();

    if ($session['user']['alive'])
    {
        \LotgdNavigation::villageNav();
    }
    else
    {
        \LotgdNavigation::addNav('common.nav.news', 'news.php');
    }

    \LotgdNavigation::addNav('common.nav.prefs', 'prefs.php');

    $replacearray = explode('|', $session['user']['replaceemail']);
    $email = $replacearray[0];

    $params['tpl'] = 'cancelemail';
    $params['email'] = $email;

    debuglog('Email Change Request from '.$session['user']['emailaddress'].' to '.$email.' has been cancelled', $session['user']['acctid'], $session['user']['acctid'], 'Email');

    $session['user']['replaceemail'] = '';
    $session['user']['emailvalidation'] = '';
}
else
{
    checkday();

    \LotgdNavigation::addNav('common.nav.bio', 'bio.php?char='.$session['user']['acctid'].'&ret='.urlencode(\LotgdHttp::getServer('REQUEST_URI')));
    if ($session['user']['alive'])
    {
        \LotgdNavigation::villageNav();
    }
    else
    {
        \LotgdNavigation::addNav('common.nav.news', 'news.php');
    }

    $post = \LotgdHttp::getPostAll();
    unset($post['showFormTabIndex']);

    if (\LotgdHttp::isPost())
    {
        $pass1 = $post['pass1'];
        $pass2 = $post['pass2'];

        if ($pass1 != $pass2)
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.post.password.not.match', [], $textDomain));
        }
        elseif ('' != $pass1)
        {
            if (strlen($pass1) > 3)
            {
                if ('!md5!' != substr($pass1, 0, 5))
                {
                    $pass1 = md5(md5($pass1));
                }
                else
                {
                    $pass1 = md5(substr($pass1, 5));
                }
                $session['user']['password'] = $pass1;

                \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.post.password.changed', [], $textDomain));
            }
            else
            {
                \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.post.password.short', [], $textDomain));
            }
        }

        reset($post);
        $nonsettings = [
            'pass1' => 1,
            'pass2' => 1,
            'email' => 1,
            'template' => 1,
            'bio' => 1
        ];

        foreach ($post as $key => $val)
        {
            // If this is one we don't save, skip
            if ($nonsettings[$key] ?? false)
            {
                continue;
            }

            // If this is a module userpref handle and skip
            debug("Setting $key to $val");

            if (strstr($key, '___'))
            {
                if (false === strpos($key, 'user_') && false === strpos($key, 'check_'))
                {
                    continue;
                }
                $val = \LotgdHttp::getPost($key);
                $x = explode('___', $key);
                $module = $x[0];
                $key = $x[1];

                modulehook('notifyuserprefchange', ['name' => $key, 'new' => $val]);

                set_module_pref($key, $val, $module);

                continue;
            }

            $session['user']['prefs'][$key] = $val;
        }

        $bio = stripslashes($post['bio']);

        if ($bio != $session['user']['bio'])
        {
            if ($session['user']['biotime'] > '9000-01-01')
            {
                \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.post.bio.error', [], $textDomain));
            }
            else
            {
                $session['user']['bio'] = $bio;
                $session['user']['biotime'] = new \DateTime('now');
            }
        }

        $email = $post['email'];

        if ($email != $session['user']['emailaddress'])
        {
            if (getsetting('playerchangeemail', 0))
            {
                if (is_email($email))
                {
                    if (1 == getsetting('requirevalidemail', 0))
                    {
                        $emailverification = 'x'.md5(date('Y-m-d H:i:s').$email);
                        $emailverification = substr($emailverification, 0, strlen($emailverification) - 2);
                        //cut last char, won't be salved in the DB else!
                        $shortname = $session['user']['login'];

                        //-- Use "protocol-less" URLs
                        $serveraddress = sprintf('//%s?op=val&id=%s', \LotgdHttp::getServer('SERVER_NAME').'/create.php', $emailverification);
                        $serverurl = sprintf('//%s', \LotgdHttp::getServer('SERVER_NAME'));

                        $subj = \LotgdTranslator::t('mail.subject', [], $textDomain);

                        $msg = \LotgdTranslator::t('mail.message', [ 'name' => $shortname ], $textDomain);
                        $confirm = \LotgdTranslator::t('mail.confirm', [ 'serverAddress' => $serveraddress ], $textDomain);
                        $oldconfirm = \LotgdTranslator::t('mail.confirm.old', [], $textDomain);
                        $ownermsg = \LotgdTranslator::t('mail.owner', [ 'email' => $email, 'name' => $shortname ], $textDomain);
                        $newvalidationsent = \LotgdTranslator::t('mail.validation.new', [], $textDomain);
                        $oldvalidationsent = \LotgdTranslator::t('mail.validation.old', [], $textDomain);

                        $changetimeoutwarning = '';
                        if (getsetting('playerchangeemailauto', 0))
                        {
                            $changetimeoutwarning = \LotgdTranslator::t('mail.timeout', [ 'days' => getsetting('playerchangeemaildays', 3)], $textDomain);
                        }
                        $footer = $changetimeoutwarning.\LotgdTranslator::t('mail.footer', [ 'server' => $serverurl], $textDomain);

                        if (0 == getsetting('validationtarget', 0))
                        {
                            // old account
                            $msg .= $oldconfirm.$footer;
                            $ownermsg .= $oldvalidationsent.$confirm.$footer;
                        }
                        else
                        {
                            $msg .= $confirm.$footer;
                            $ownermsg .= $newvalidationsent.$footer;
                        }

                        mail($email, $subj, str_replace('`n', "\n", $msg), 'From: '.getsetting('gameadminemail', 'postmaster@localhost.com'));
                        mail($session['user']['emailaddress'], $subj, str_replace('`n', "\n", $ownermsg), 'From: '.getsetting('gameadminemail', 'postmaster@localhost.com'));

                        $session['user']['replaceemail'] = $email.'|'.date('Y-m-d H:i:s');
                        $session['user']['emailvalidation'] = $emailverification;

                        debuglog('Email Change requested from '.$session['user']['emailaddress'].' to '.$email, $session['user']['acctid'], $session['user']['acctid'], 'Email');

                        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.validate', [ 'target' => getsetting('validationtarget', 0) ], $textDomain));

                        if (getsetting('playerchangeemailauto', 0))
                        {
                            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.auto', [ 'days' => getsetting('playerchangeemaildays', 3) ], $textDomain));

                            if (0 == getsetting('validationtarget', 0))
                            {
                                \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.trouble', [], $textDomain));
                            }
                        }
                        elseif (0 == getsetting('validationtarget', 0))
                        {
                            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.account', [], $textDomain));
                        }
                    }
                    else
                    {
                        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.form.email.changed', [], $textDomain));

                        debuglog('Email changed from '.$session['user']['emailaddress'].' to '.$email, $session['user']['acctid'], $session['user']['acctid'], 'Email');

                        $session['user']['emailaddress'] = $email;
                    }
                }
                else
                {
                    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.form.email.invalid', [], $textDomain));
                }
            }
            else
            {
                \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.form.email.prohibit', [], $textDomain));
            }
        }

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.settings.saved', [], $textDomain));
    }

    $form = [
        'Account Preferences,title',
        'pass1' => 'Password,password',
        'pass2' => 'Retype,password',
        'email' => 'Email Address',

        'Character Preferences,title',
        'sexuality' => 'Which sex are you attracted to?,enum,0,male,1,female',
        'Note: if you find both attractive then choose one to be your primary. You may change it at any time.,note',

        'Display Preferences,title',
        'template' => 'Skin,theme',
        'language' => 'Language,enum,'.getsetting('serverlanguages', 'en,English,de,Deutsch,fr,Français,dk,Danish,es,Español,it,Italian'),
        'tabconfig' => 'Show config sections in tabs,bool',
        'forestcreaturebar' => 'Forest Creatures show health ...,enum,0,Only Text,1,Only Healthbar,2,Healthbar AND Text',

        'Game Behavior Preferences,title',
        'emailonmail' => 'Send email when you get new Ye Olde Mail?,bool',
        'systemmail' => 'Send email for system generated messages?,bool',
        'dirtyemail' => 'Allow profanity in received Ye Olde Poste messages?,bool',
        'timestamp' => 'Show timestamps in commentary?,enum,0,None,1,Real Time [12/25 1:27pm],2,Relative Time (1h35m)',
        'timeformat' => ['Timestamp format (currently displaying time as %s whereas default format is "[m/d h:ia]"),string,20',
            date($session['user']['prefs']['timeformat'],
                strtotime('now') + ($session['user']['prefs']['timeoffset'] * 60 * 60))],
        'timeoffset' => ['Hours to offset time displays (%s currently displays as %s)?,int',
            date($session['user']['prefs']['timeformat']),
            date($session['user']['prefs']['timeformat'],
                strtotime('now') + ($session['user']['prefs']['timeoffset'] * 60 * 60))],
        'ihavenocheer' => '`0Always disable all holiday related text replacements (such as a`1`0l`1`0e => e`1`0g`1`0g n`1`0o`1`0g for December),bool',
        'bio' => 'Short Character Biography (255 chars max),string,255',
    ];

    $warn = translate_inline('Your password is too short.  It must be at least 4 characters long.');

    $prefs = &$session['user']['prefs'];
    $prefs['bio'] = $session['user']['bio'];
    $prefs['template'] = \LotgdHttp::getCookie('template') ?: getsetting('defaultskin', 'jade.htm');
    $prefs['sexuality'] = $prefs['sexuality'] ?? ! $session['user']['sex'] ?: ! $session['user']['sex'];
    $prefs['email'] = $session['user']['emailaddress'];
    $prefs['timeformat'] = $prefs['timeformat'] ?? '[m/d h:ia]';
    // Default tabbed config to true
    $prefs['tabconfig'] = $prefs['tabconfig'] ?? 1;

    // Okay, allow modules to add prefs one at a time.
    // We are going to do it this way to *ensure* that modules don't conflict
    // in namespace.
    $moduleRepository = \Doctrine::getRepository('LotgdCore:Modules');
    $result = $moduleRepository->findInfoKeyLike('prefs');

    $everfound = 0;
    $foundmodules = [];
    $msettings = [];
    $mdata = [];

    foreach ($result as $row)
    {
        $module = $row['modulename'];
        $info = get_module_info($module);

        if (count($info['prefs']) <= 0)
        {
            continue;
        }
        $tempsettings = [];
        $tempdata = [];
        $found = 0;

        foreach ($info['prefs'] as $key => $val)
        {
            $isuser = preg_match('/^user_/', $key);
            $ischeck = preg_match('/^check_/', $key);

            if (is_array($val))
            {
                $v = $val[0];
                $x = explode('|', $v);
                $val[0] = $x[0];
                $x[0] = $val;
            }
            else
            {
                $x = explode('|', $val);
            }

            if (is_array($x[0]))
            {
                $x[0] = call_user_func_array('sprintf', $x[0]);
            }

            $type = explode(',', $x[0]);
            $type = trim($type[1] ?? 'string');

            // Okay, if we have a title section, let's copy over the last
            // title section
            if (strstr($type, 'title'))
            {
                if ($found)
                {
                    $everfound = 1;
                    $found = 0;
                    $msettings = array_merge($msettings, $tempsettings);
                    $mdata = array_merge($mdata, $tempdata);
                }
                $tempsettings = [];
                $tempdata = [];
            }

            if (! $isuser && ! $ischeck && ! strstr($type, 'title') && ! strstr($type, 'note'))
            {
                continue;
            }

            if ($isuser)
            {
                $found = 1;
            }
            // If this is a check preference, we need to call the modulehook
            // checkuserpref  (requested by cortalUX)
            if ($ischeck)
            {
                $args = modulehook('checkuserpref', ['name' => $key, 'pref' => $x[0], 'default' => $x[1]], false, $module);

                if (isset($args['allow']) && ! $args['allow'])
                {
                    continue;
                }
                $x[0] = $args['pref'];
                $x[1] = $args['default'];
                $found = 1;
            }

            $tempsettings[$module.'___'.$key] = $x[0];

            if (array_key_exists(1, $x))
            {
                $tempdata[$module.'___'.$key] = $x[1];
            }
        }

        if ($found)
        {
            $msettings = array_merge($msettings, $tempsettings);
            $mdata = array_merge($mdata, $tempdata);
            $everfound = 1;
        }

        // If we found a user editable one
        if ($everfound)
        {
            // Collect the values
            $foundmodules[] = $module;
        }
    }

    if (count($foundmodules))
    {
        $modulePrefsRepository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');
        $result = $modulePrefsRepository->findModulesPrefs($foundmodules, $session['user']['acctid']);

        foreach ($result as $row)
        {
            $mdata[$row['modulename'].'___'.$row['setting']] = $row['value'];
        }
    }

    $form = array_merge($form, $msettings);
    $prefs = array_merge($prefs, $mdata);

    if ('' != $session['user']['replaceemail'])
    {
        //we have an email change request here
        $replacearray = explode('|', $session['user']['replaceemail']);
        output('`$There is an email change request pending to the email address `q"%s`$" that was given at the timestamp %s (Server Time Zone).`n', $replacearray[0], $replacearray[1]);
        $expirationdate = strtotime('+ '.getsetting('playerchangeemaildays', 3).' days', strtotime($replacearray[1]));
        $left = $expirationdate - strtotime('now');
        $hoursleft = round($left / (60 * 60), 1);
        $autoaccept = getsetting('playerchangeemailauto', 0);

        if ($autoaccept)
        {
            if ($hoursleft > 0)
            {
                output('`n`qIf not cancelled, the option to automatically accept the new email address without verification will be due in approximately %s hours and can be done on this page.`n`n', $hoursleft);
            }
            else
            {
                // display the direct link to change it.
                $changeemail = translate_inline('Force your email address NOW');
                output('`n`qTime is up, you can now accept the change via this button:`n`n');
                rawoutput("<form action='prefs.php?op=forcechangeemail' method='POST'><input type='submit' class='ui button' value='$changeemail'></form><br>");
                \LotgdNavigation::addNavAllow('prefs.php?op=forcechangeemail');
            }
        }
        else
        {
            output('`$If you have trouble with this, please petition.`n`n');
        }
        $cancelemail = translate_inline('Cancel email change request');
        output('`$Cancel the request with the following button:`n`n');
        rawoutput("<form action='prefs.php?op=cancelemail' method='POST'><input type='submit' class='ui button' value='$cancelemail'></form><br>");
        \LotgdNavigation::addNavAllow('prefs.php?op=cancelemail');
    }

    $params['form'] = lotgd_showform($form, $prefs, false, false, false);
}

$params = modulehook('page-prefs-tpl-params', $params);
rawoutput(LotgdTheme::renderThemeTemplate('page/prefs.twig', $params));

page_footer();
