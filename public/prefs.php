<?php

// addnews ready
// mail ready
// translator ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

if (isset($_POST['template']))
{
    $skin = $_POST['template'];

    if ($skin > '')
    {
        setcookie('template', $skin, ['expires' => strtotime('+45 days')]);
        $_COOKIE['template'] = $skin;
    }
}

require_once 'common.php';

$textDomain = 'page_prefs';

$params = [
    'textDomain' => $textDomain,
    'selfDelete' => (int) LotgdSetting::getSetting('selfdelete', 0),
];

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$op = (string) LotgdRequest::getQuery('op');

LotgdNavigation::addHeader('common.category.navigation');
LotgdNavigation::addNav('common.nav.update', 'prefs.php');

if ('suicide' == $op && $params['selfDelete'])
{
    $userId = (int) $session['user']['acctid'];

    if (LotgdKernel::get('lotgd.core.backup')->characterCleanUp($userId, CHAR_DELETE_SUICIDE))
    {
        LotgdTool::addNews('delete.character', ['name' => $session['user']['name']], 'partial_news', true);

        LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.delete.character', [], $textDomain));

        $session                     = [];
        $session['user']             = [];
        $session['loggedin']         = false;
        $session['user']['loggedin'] = false;

        LotgdKernel::get('cache.app')->delete('char-list-home-page');

        redirect('home.php');
    }
}
elseif ('forcechangeemail' == $op)
{
    LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    if ($session['user']['alive'])
    {
        LotgdNavigation::villageNav();
    }
    else
    {
        LotgdNavigation::addNav('common.nav.news', 'news.php');
    }

    LotgdNavigation::addNav('common.nav.prefs', 'prefs.php');

    $replacearray = explode('|', $session['user']['replaceemail']);
    $email        = $replacearray[0];

    $params['tpl']   = 'forcechangeemail';
    $params['email'] = $email;

    LotgdLog::debug('Email Change Request from '.$session['user']['emailaddress'].' to '.$email.' has been forced after the wait period', $session['user']['acctid'], $session['user']['acctid'], 'Email');

    $session['user']['emailaddress']    = $replacearray[0];
    $session['user']['replaceemail']    = '';
    $session['user']['emailvalidation'] = '';
}
elseif ('cancelemail' == $op)
{
    LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    if ($session['user']['alive'])
    {
        LotgdNavigation::villageNav();
    }
    else
    {
        LotgdNavigation::addNav('common.nav.news', 'news.php');
    }

    LotgdNavigation::addNav('common.nav.prefs', 'prefs.php');

    $replacearray = explode('|', $session['user']['replaceemail']);
    $email        = $replacearray[0];

    $params['tpl']   = 'cancelemail';
    $params['email'] = $email;

    LotgdLog::debug('Email Change Request from '.$session['user']['emailaddress'].' to '.$email.' has been cancelled', $session['user']['acctid'], $session['user']['acctid'], 'Email');

    $session['user']['replaceemail']    = '';
    $session['user']['emailvalidation'] = '';
}
else
{
    LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    LotgdNavigation::addNav('common.nav.bio', 'bio.php?char='.$session['user']['acctid'].'&ret='.urlencode(LotgdRequest::getServer('REQUEST_URI')));
    if ($session['user']['alive'])
    {
        LotgdNavigation::villageNav();
    }
    else
    {
        LotgdNavigation::addNav('common.nav.news', 'news.php');
    }

    $post = LotgdRequest::getPostAll();
    unset($post['showFormTabIndex']);

    if (LotgdRequest::isPost())
    {
        $pass1 = $post['pass1'];
        $pass2 = $post['pass2'];

        if ($pass1 != $pass2)
        {
            LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.post.password.not.match', [], $textDomain));
        }
        elseif ('' != $pass1)
        {
            if (\strlen($pass1) > 3)
            {
                /** @var Symfony\Component\Security\Core\Encoder\UserPasswordEncoder $passwordEncoder */
                $passwordEncoder = LotgdKernel::get('security.password_encoder');
                $account         = Doctrine::getRepository('LotgdCore:User')->find($session['user']['acctid']);

                $session['user']['password'] = $passwordEncoder->encodePassword($account, $pass1);

                LotgdFlashMessages::addSuccessMessage(LotgdTranslator::t('flash.message.form.password.changed', [], $textDomain));
            }
            else
            {
                LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.post.password.short', [], $textDomain));
            }
        }

        reset($post);
        $nonsettings = [
            'pass1'    => 1,
            'pass2'    => 1,
            'email'    => 1,
            'template' => 1,
            'bio'      => 1,
        ];

        foreach ($post as $key => $val)
        {
            // If this is one we don't save, skip
            if ($nonsettings[$key] ?? false)
            {
                continue;
            }

            // If this is a module userpref handle and skip
            LotgdResponse::pageDebug("Setting {$key} to {$val}");

            if (strstr($key, '___'))
            {
                if (false === strpos($key, 'user_') && false === strpos($key, 'check_'))
                {
                    continue;
                }
                $val    = LotgdRequest::getPost($key);
                $x      = explode('___', $key);
                $module = $x[0];
                $key    = $x[1];

                $args = new GenericEvent(null, ['name' => $key, 'new' => $val]);
                LotgdEventDispatcher::dispatch($args, Events::PAGE_PREFS_CHANGE);
                modulehook('notifyuserprefchange', $args->getArguments());

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
                LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.post.bio.error', [], $textDomain));
            }
            else
            {
                $session['user']['bio']     = $bio;
                $session['user']['biotime'] = new DateTime('now');
            }
        }

        $email = $post['email'];

        if ($email != $session['user']['emailaddress'])
        {
            if (LotgdSetting::getSetting('playerchangeemail', 0))
            {
                if (LotgdKernel::get("lotgd_core.tool.validator")->isMail($email))
                {
                    if (1 == LotgdSetting::getSetting('requirevalidemail', 0))
                    {
                        $emailverification = 'x'.md5(date('Y-m-d H:i:s').$email);
                        $emailverification = substr($emailverification, 0, \strlen($emailverification) - 2);
                        //cut last char, won't be salved in the DB else!
                        $shortname = $session['user']['login'];

                        //-- Use "protocol-less" URLs
                        $serveraddress = sprintf('//%s?op=val&id=%s', LotgdRequest::getServer('SERVER_NAME').'/create.php', $emailverification);
                        $serverurl     = sprintf('//%s', LotgdRequest::getServer('SERVER_NAME'));

                        $subj = LotgdTranslator::t('mail.subject', [], $textDomain);

                        $msg               = LotgdTranslator::t('mail.message', ['name' => $shortname], $textDomain);
                        $confirm           = LotgdTranslator::t('mail.confirm', ['serverAddress' => $serveraddress], $textDomain);
                        $oldconfirm        = LotgdTranslator::t('mail.confirm.old', [], $textDomain);
                        $ownermsg          = LotgdTranslator::t('mail.owner', ['email' => $email, 'name' => $shortname], $textDomain);
                        $newvalidationsent = LotgdTranslator::t('mail.validation.new', [], $textDomain);
                        $oldvalidationsent = LotgdTranslator::t('mail.validation.old', [], $textDomain);

                        $changetimeoutwarning = '';
                        if (LotgdSetting::getSetting('playerchangeemailauto', 0))
                        {
                            $changetimeoutwarning = LotgdTranslator::t('mail.timeout', ['days' => LotgdSetting::getSetting('playerchangeemaildays', 3)], $textDomain);
                        }
                        $footer = $changetimeoutwarning.LotgdTranslator::t('mail.footer', ['server' => $serverurl], $textDomain);

                        if (0 == LotgdSetting::getSetting('validationtarget', 0))
                        {
                            // old account
                            $msg      .= $oldconfirm.$footer;
                            $ownermsg .= $oldvalidationsent.$confirm.$footer;
                        }
                        else
                        {
                            $msg      .= $confirm.$footer;
                            $ownermsg .= $newvalidationsent.$footer;
                        }

                        mail($email, $subj, str_replace('`n', "\n", $msg), 'From: '.LotgdSetting::getSetting('gameadminemail', 'postmaster@localhost.com'));
                        mail($session['user']['emailaddress'], $subj, str_replace('`n', "\n", $ownermsg), 'From: '.LotgdSetting::getSetting('gameadminemail', 'postmaster@localhost.com'));

                        $session['user']['replaceemail']    = $email.'|'.date('Y-m-d H:i:s');
                        $session['user']['emailvalidation'] = $emailverification;

                        LotgdLog::debug('Email Change requested from '.$session['user']['emailaddress'].' to '.$email, $session['user']['acctid'], $session['user']['acctid'], 'Email');

                        LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.validate', ['target' => LotgdSetting::getSetting('validationtarget', 0)], $textDomain));

                        if (LotgdSetting::getSetting('playerchangeemailauto', 0))
                        {
                            LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.auto', ['days' => LotgdSetting::getSetting('playerchangeemaildays', 3)], $textDomain));

                            if (0 == LotgdSetting::getSetting('validationtarget', 0))
                            {
                                LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.trouble', [], $textDomain));
                            }
                        }
                        elseif (0 == LotgdSetting::getSetting('validationtarget', 0))
                        {
                            LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.account', [], $textDomain));
                        }
                    }
                    else
                    {
                        LotgdFlashMessages::addSuccessMessage(LotgdTranslator::t('flash.message.form.email.changed', [], $textDomain));

                        LotgdLog::debug('Email changed from '.$session['user']['emailaddress'].' to '.$email, $session['user']['acctid'], $session['user']['acctid'], 'Email');

                        $session['user']['emailaddress'] = $email;
                    }
                }
                else
                {
                    LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.form.email.invalid', [], $textDomain));
                }
            }
            else
            {
                LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.form.email.prohibit', [], $textDomain));
            }
        }

        LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.settings.saved', [], $textDomain));
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
        'template'          => 'Skin,theme',
        'language'          => 'Language,enum,'.LotgdSetting::getSetting('serverlanguages', 'en,English,de,Deutsch,fr,Français,dk,Danish,es,Español,it,Italian'),
        'tabconfig'         => 'Show config sections in tabs,bool',
        'forestcreaturebar' => 'Forest Creatures show health ...,enum,0,Only Text,1,Only Healthbar,2,Healthbar AND Text',

        'Game Behavior Preferences,title',
        'emailonmail' => 'Send email when you get new Ye Olde Mail?,bool',
        'systemmail'  => 'Send email for system generated messages?,bool',
        'dirtyemail'  => 'Allow profanity in received Ye Olde Poste messages?,bool',
        'timestamp'   => 'Show timestamps in commentary?,enum,0,None,1,Real Time [12/25 1:27pm],2,Relative Time (1h35m)',
        'timeformat'  => ['Timestamp format (currently displaying time as %s whereas default format is "[m/d h:ia]"),string,20',
            date(
                $session['user']['prefs']['timeformat'],
                strtotime('now') + ($session['user']['prefs']['timeoffset'] * 60 * 60)
            ), ],
        'timeoffset' => ['Hours to offset time displays (%s currently displays as %s)?,int',
            date($session['user']['prefs']['timeformat']),
            date(
                $session['user']['prefs']['timeformat'],
                strtotime('now') + ($session['user']['prefs']['timeoffset'] * 60 * 60)
            ), ],
        'ihavenocheer' => '`0Always disable all holiday related text replacements (such as a`1`0l`1`0e => e`1`0g`1`0g n`1`0o`1`0g for December),bool',
        'bio'          => 'Short Character Biography (255 chars max),string,255',
    ];

    $warn = 'Your password is too short.  It must be at least 4 characters long.';

    $prefs               = &$session['user']['prefs'];
    $prefs['bio']        = $session['user']['bio'];
    $prefs['template']   = LotgdRequest::getCookie('template') ?: LotgdSetting::getSetting('defaultskin', 'jade.htm');
    $prefs['sexuality']  = $prefs['sexuality'] ?? ! $session['user']['sex'] ?: ! $session['user']['sex'];
    $prefs['email']      = $session['user']['emailaddress'];
    $prefs['timeformat'] ??= '[m/d h:ia]';
    // Default tabbed config to true
    $prefs['tabconfig'] ??= 1;

    // Okay, allow modules to add prefs one at a time.
    // We are going to do it this way to *ensure* that modules don't conflict
    // in namespace.
    $moduleRepository = Doctrine::getRepository('LotgdCore:Modules');
    $result           = $moduleRepository->findInfoKeyLike('prefs');

    $everfound    = 0;
    $foundmodules = [];
    $msettings    = [];
    $mdata        = [];

    foreach ($result as $row)
    {
        $module = $row['modulename'];
        $info   = get_module_info($module);

        if (\count($info['prefs']) <= 0)
        {
            continue;
        }
        $tempsettings = [];
        $tempdata     = [];
        $found        = 0;

        foreach ($info['prefs'] as $key => $val)
        {
            $isuser  = preg_match('/^user_/', $key);
            $ischeck = preg_match('/^check_/', $key);

            if (\is_array($val))
            {
                $v      = $val[0];
                $x      = explode('|', $v);
                $val[0] = $x[0];
                $x[0]   = $val;
            }
            else
            {
                $x = explode('|', $val);
            }

            if (\is_array($x[0]))
            {
                $x[0] = \call_user_func_array('sprintf', $x[0]);
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
                    $found     = 0;
                    $msettings = array_merge($msettings, $tempsettings);
                    $mdata     = array_merge($mdata, $tempdata);
                }
                $tempsettings = [];
                $tempdata     = [];
            }

            if ( ! $isuser && ! $ischeck && ! strstr($type, 'title') && ! strstr($type, 'note'))
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
                $args = new GenericEvent(null, ['name' => $key, 'pref' => $x[0], 'default' => $x[1]]);
                LotgdEventDispatcher::dispatch($args, Events::PAGE_PREFS_CHECK);
                $args = modulehook('checkuserpref', $args->getArguments(), false, $module);

                if (isset($args['allow']) && ! $args['allow'])
                {
                    continue;
                }
                $x[0]  = $args['pref'];
                $x[1]  = $args['default'];
                $found = 1;
            }

            $tempsettings[$module.'___'.$key] = $x[0];

            if (\array_key_exists(1, $x))
            {
                $tempdata[$module.'___'.$key] = $x[1];
            }
        }

        if ($found)
        {
            $msettings = array_merge($msettings, $tempsettings);
            $mdata     = array_merge($mdata, $tempdata);
            $everfound = 1;
        }

        // If we found a user editable one
        if (0 !== $everfound)
        {
            // Collect the values
            $foundmodules[] = $module;
        }
    }

    if ( ! empty($foundmodules))
    {
        $modulePrefsRepository = Doctrine::getRepository('LotgdCore:ModuleUserprefs');
        $result                = $modulePrefsRepository->findModulesPrefs($foundmodules, $session['user']['acctid']);

        foreach ($result as $row)
        {
            $mdata[$row['modulename'].'___'.$row['setting']] = $row['value'];
        }
    }

    $form  = array_merge($form, $msettings);
    $prefs = array_merge($prefs, $mdata);

    if ('' != $session['user']['replaceemail'])
    {
        //we have an email change request here
        $replacearray = explode('|', $session['user']['replaceemail']);
        LotgdResponse::pageAddContent(LotgdFormat::colorize(LotgdTranslator::t('replace.email.pending', ['email' => $replacearray[0], 'time' => $replacearray[1]], $textDomain)));
        $expirationdate = strtotime('+ '.LotgdSetting::getSetting('playerchangeemaildays', 3).' days', strtotime($replacearray[1]));
        $left           = $expirationdate - strtotime('now');
        $hoursleft      = round($left / (60 * 60), 1);
        $autoaccept     = LotgdSetting::getSetting('playerchangeemailauto', 0);

        if ($autoaccept)
        {
            if ($hoursleft > 0)
            {
                LotgdResponse::pageAddContent(LotgdFormat::colorize(LotgdTranslator::t('replace.email.hours.left', ['hours' => $hoursleft], $textDomain)));
            }
            else
            {
                // display the direct link to change it.
                $changeemail = LotgdTranslator::t('replace.email.button.force', [], $textDomain);
                LotgdResponse::pageAddContent(LotgdFormat::colorize(LotgdTranslator::t('replace.email.time.out', [], $textDomain)));
                LotgdResponse::pageAddContent("<form action='prefs.php?op=forcechangeemail' method='POST'><input type='submit' class='ui button' value='{$changeemail}'></form><br>");
                LotgdNavigation::addNavAllow('prefs.php?op=forcechangeemail');
            }
        }
        else
        {
            LotgdResponse::pageAddContent(LotgdFormat::colorize(LotgdTranslator::t('replace.email.trouble', [], $textDomain)));
        }
        $cancelemail = LotgdTranslator::t('replace.email.button.cancel', [], $textDomain);
        LotgdResponse::pageAddContent(LotgdFormat::colorize(LotgdTranslator::t('replace.email.cancel', [], $textDomain)));
        LotgdResponse::pageAddContent("<form action='prefs.php?op=cancelemail' method='POST'><input type='submit' class='ui button' value='{$cancelemail}'></form><br>");
        LotgdNavigation::addNavAllow('prefs.php?op=cancelemail');
    }

    $params['form'] = lotgd_showform($form, $prefs, false, false, false);
}

$args = new GenericEvent(null, $params);
LotgdEventDispatcher::dispatch($args, Events::PAGE_PREFS_POST);
LotgdResponse::pageAddContent(LotgdTheme::render('page/prefs.html.twig', $params->getArguments()));

//-- Finalize page
LotgdResponse::pageEnd();
