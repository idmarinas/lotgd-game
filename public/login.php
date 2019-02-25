<?php

// mail ready
// addnews ready
// translator ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/systemmail.php';
require_once 'lib/checkban.php';
require_once 'lib/serverfunctions.class.php';

tlschema('login');

translator_setup();

$op = (string) httpget('op');
$name = (string) httppost('name');
$iname = (string) getsetting('innname', LOCATION_INN);
$vname = (string) getsetting('villagename', LOCATION_FIELDS);
$force = httppost('force');

if ('' != $name)
{
    if ($session['loggedin'])
    {
        return redirect('badnav.php');
    }

    //-- If server is full, not need proces any data.
    if (ServerFunctions::isTheServerFull() && 1 != $force)
    {
        //sanity check if the server is / got full --> back to home
        $session['user'] = [];
        \LotgdFlashMessaged::addWarningMessage(\LotgdTranslator::t('login.full', [], 'page-login'));

        return redirect('home.php');
    }

    $password = stripslashes((string) httppost('password'));

    if ('!md5!' == substr($password, 0, 5))
    {
        $password = md5(substr($password, 5));
    }
    elseif ('!md52!' == substr($password, 0, 6) && 38 == strlen($password) && $force)
    {
        $password = substr($password, 6);
        $password = preg_replace('/[^a-f0-9]/', '', $password);
    }
    else
    {
        $password = md5(md5($password));
    }

    checkban(); //check if this computer is banned

    //-- Using Doctrine repository to process login
    $repositoryAccounts = Doctrine::getRepository(Lotgd\Core\Entity\Accounts::class);
    $account = $repositoryAccounts->processLoginGetAcctData($name, $password);

    //-- Not found account
    if (! $account)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('login.incorrect', [], 'page-login'));

        //now we'll log the failed attempt and begin to issue bans if
        //there are too many, plus notify the admins.
        checkban();

        $result = $repositoryAccounts->createQueryBuilder('u')
            ->select('u.acctid')
            ->where('u.login = :login')
            ->setParameters(['login' => $name])
            ->getQuery()
            ->getResult()
        ;

        if (count($result))
        {
            // just in case there manage to be multiple accounts on
            // this name.
            foreach ($result as $key => $row)
            {
                $post = httpallpost();

                $failLog = new \Lotgd\Core\Entity\Faillog();
                $failLog->setEventid(0)
                    ->setDate(new DateTime())
                    ->setPost($post)
                    ->setIp(\LotgdHttp::getServer('REMOTE_ADDR'))
                    ->setAcctid($row['acctid'])
                    ->setId(\LotgdHttp::getCookie('lgi') ?: '')
                ;

                \Doctrine::persist($failLog);
                \Doctrine::flush(); //Persist objects

                $sql = 'SELECT '.DB::prefix('faillog').'.*, '.DB::prefix('accounts').'.superuser,name,login FROM '.DB::prefix('faillog').' INNER JOIN '.DB::prefix('accounts').' ON '.DB::prefix('accounts').'.acctid='.DB::prefix('faillog').".acctid WHERE ip='{$_SERVER['REMOTE_ADDR']}' AND date>'".date('Y-m-d H:i:s', strtotime('-1 day'))."'";
                $result2 = DB::query($sql);

                $c = 0;
                $alert = '';
                $su = false;

                while ($row2 = DB::fetch_assoc($result2))
                {
                    if ($row2['superuser'] > 0)
                    {
                        $c++;
                        $su = true;
                    }
                    $c++;
                    $alert .= "`3{$row2['date']}`7: Failed attempt from `&{$row2['ip']}`7 [`3{$row2['id']}`7] to log on to `^{$row2['login']}`7 ({$row2['name']}`7)`n";
                }

                if ($c >= 10)
                {
                    // 5 failed attempts for superuser, 10 for regular user
                    $bans = new \Lotgd\Core\Entity\Bans();
                    $banexpire = new \DateTime('now');
                    $bans->setIpfilter(\LotgdHttp::getServer('REMOTE_ADDR'))
                        ->setBanreason(\LotgdTranslator::t('login.banMessage', [], 'page-login'))
                        ->setBanexpire($banexpire->add(new \DateInterval('PT15M'))) //-- Added 15 minutes
                        ->setBanner('System')
                        ->setLasthit(new \DateTime('0000-00-00 00:00:00'))
                    ;

                    \Doctrine::persist($failLog);
                    \Doctrine::flush(); //Persist objects

                    if ($su)
                    {
                        // send a system message to admins regarding
                        // this failed attempt if it includes superusers.
                        $sql = 'SELECT acctid FROM '.DB::prefix('accounts').' WHERE (superuser&'.SU_EDIT_USERS.')';
                        $result2 = DB::query($sql);
                        $subj = translate_mail(['`#%s failed to log in too many times!', \LotgdHttp::getServer('REMOTE_ADDR')], 0);

                        while ($row2 = DB::fetch_assoc($result2))
                        {
                            //delete old messages that
                            $sql = 'DELETE FROM '.DB::prefix('mail')." WHERE msgto={$row2['acctid']} AND msgfrom=0 AND subject = '".serialize($subj)."' AND seen=0";
                            DB::query($sql);

                            $noemail = false;

                            if (DB::affected_rows() > 0)
                            {
                                $noemail = true;
                            }
                            $msg = translate_mail(['This message is generated as a result of one or more of the accounts having been a superuser account.  Log Follows:`n`n%s', $alert], 0);
                            systemmail($row2['acctid'], $subj, $msg, 0, $noemail);
                        }//end for
                    }//end if($su)
                }//end if($c>=10)
            }//end foreach
        }//end if (DB::num_rows)

        \Doctrine::clear(); //-- Detaches all objects from Doctrine!

        return redirect('index.php');
    }

    $session['user'] = $account;

    checkban($session['user']['login']); //check if this account is banned

    // If the player isn't allowed on for some reason, anything on
    // this hook should automatically call page_footer and exit
    // itself.
    modulehook('check-login');

    if ('' != $session['user']['emailvalidation'] && 'x' != substr($session['user']['emailvalidation'], 0, 1))
    {
        $session['user'] = [];
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('login.validate', [], 'page-login'));

        return redirect('home.php');
    }

    $session['loggedin'] = true;
    $session['laston'] = date('Y-m-d H:i:s');
    $session['sentnotice'] = 0;
    $session['user']['laston'] = new \DateTime('now');

    invalidatedatacache('charlisthomepage');
    invalidatedatacache('list.php-warsonline');

    // Handle the change in number of users online
    translator_check_collect_texts();

    // Let's throw a login module hook in here so that modules
    // like the stafflist which need to invalidate the cache
    // when someone logs in or off can do so.
    modulehook('player-login');

    //-- Check for valid restorepage
    if (empty($session['user']['restorepage']) || is_numeric($session['user']['restorepage']) || 'login.php' == $session['user']['restorepage'])
    {
        $session['user']['restorepage'] = 'news.php';
    }

    if ($session['user']['loggedin'])
    {
        $link = sprintf('<a href="%s">%s</a>', $session['user']['restorepage'], $session['user']['restorepage']);

        $str = \LotgdTranslator::t('login.redirect', ['link' => $link], 'page-login');
        header("Location: {$session['user']['restorepage']}");
        saveuser();
        echo $str;

        exit();
    }

    $session['user']['loggedin'] = true;

    //-- Save user
    saveuser();

    if ($session['user']['location'] == $iname)
    {
        return redirect('inn.php?op=strolldown');
    }
    elseif ($session['user']['restorepage'] > '')
    {
        return redirect($session['user']['restorepage']);
    }

    return redirect('news.php');
}
elseif ('logout' == $op)
{
    if ($session['user']['loggedin'])
    {
        $session['user']['restorepage'] = 'news.php';
        if ($session['user']['superuser'] & (0xFFFFFFFF & ~SU_DOESNT_GIVE_GROTTO))
        {
            $session['user']['restorepage'] = 'superuser.php';
        }
        elseif ($session['user']['location'] == $iname)
        {
            $session['user']['restorepage'] = 'inn.php?op=strolldown';
        }

        invalidatedatacache('charlisthomepage');
        invalidatedatacache('list.php-warsonline');

        // Let's throw a logout module hook in here so that modules
        // like the stafflist which need to invalidate the cache
        // when someone logs in or off can do so.
        modulehook('player-logout');
        saveuser();

        \LotgdSession::sessionLogOut();

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('logout.success', [], 'page-login'));
    }
    $session = [];

    return redirect('index.php');
}

//- If you enter an empty username, don't just say oops.. do something useful.
\LotgdSession::sessionLogOut();
\LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('fail.empty', [], 'page-login'));

return redirect('index.php');
