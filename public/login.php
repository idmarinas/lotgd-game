<?php

use Lotgd\Core\Controller\LoginController;
use Lotgd\Core\Entity\Bans;
// mail ready
// addnews ready
// translator ready

use Lotgd\Core\Entity\Faillog;
use Lotgd\Core\Event\Core;

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

$op    = (string) LotgdRequest::getQuery('op');
$name  = (string) LotgdRequest::getPost('name');
$iname = (string) LotgdSetting::getSetting('innname', LOCATION_INN);
$force = LotgdRequest::getPost('force');

if ('' != $name)
{
    if ($session['loggedin'])
    {
        redirect('badnav.php');
    }

    //-- If server is full, not need proces any data.
    if (LotgdKernel::get('lotgd_core.service.server_functions')->isTheServerFull() && 1 != $force)
    {
        //sanity check if the server is / got full --> back to home
        $session['user'] = [];
        LotgdFlashMessaged::addWarningMessage(LotgdTranslator::t('login.full', [], 'page_login'));

        redirect('home.php');
    }

    LotgdTool::checkBan(); //check if this computer is banned

    /** @var Symfony\Component\Security\Core\Encoder\UserPasswordEncoder $passwordEncoder */
    $passwordEncoder = LotgdKernel::get('security.password_encoder');
    $password        = LotgdRequest::getPost('password');

    //-- Using Doctrine repository to process login
    /** @var Lotgd\Core\Repository\UserRepository $repositoryAccounts */
    $repositoryAccounts = Doctrine::getRepository('LotgdCore:User');
    $account            = $repositoryAccounts->findOneByLogin($name);

    //-- Not found account
    if ( ! $account || ! $passwordEncoder->isPasswordValid($account, $password))
    {
        LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('login.incorrect', [], 'page_login'));

        //now we'll log the failed attempt and begin to issue bans if
        //there are too many, plus notify the admins.
        LotgdTool::checkBan();

        $result = $repositoryAccounts->createQueryBuilder('u')
            ->select('u.acctid')
            ->where('u.login = :login')
            ->setParameters(['login' => $name])
            ->getQuery()
            ->getResult()
        ;

        if (\count($result) > 0)
        {
            // just in case there manage to be multiple accounts on
            // this name.
            foreach ($result as $row)
            {
                $post = LotgdRequest::getPostAll();

                $failLog = new Faillog();
                $failLog->setEventid(0)
                    ->setDate(new DateTime())
                    ->setPost($post)
                    ->setIp(LotgdRequest::getServer('REMOTE_ADDR'))
                    ->setAcctid($row['acctid'])
                    ->setId(LotgdRequest::getCookie('lgi') ?: '')
                ;

                Doctrine::persist($failLog);
                Doctrine::flush(); //Persist objects

                $query = Doctrine::createQueryBuilder();
                $expr  = $query->expr();

                $query->select('u.ip', 'u.date', 'u.id', 'a.superuser', 'a.login')
                    ->from('LotgdCore:Faillog', 'u')

                    ->join('LotgdCore:User', 'a', 'with', $expr->eq('a.acctid', 'u.acctid'))

                    ->where('u.ip = :ip AND u.date > :date')

                    ->setParameters([
                        'ip'   => LotgdRequest::getServer('REMOTE_ADDR'),
                        'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    ])
                ;

                $c     = 0;
                $alert = '';
                $su    = false;

                $failResult = $query->getQuery()->getResult();

                foreach ($failResult as $row2)
                {
                    if ($row2['superuser'] > 0)
                    {
                        ++$c;
                        $su = true;
                    }
                    ++$c;
                    $alert .= sprintf(
                        '`7`3%s`0: Failed attempt from `&%s`0 [`3%s`0] to log on to `^%s`0`0`n',
                        $row2['date']->format('Y-m-d H:i:s'),
                        $row2['ip'],
                        $row2['id'],
                        $row2['login']
                    );
                }

                if ($c >= 10)
                {
                    // 5 failed attempts for superuser, 10 for regular user
                    $bans      = new Bans();
                    $banexpire = new DateTime('now');
                    $bans->setIpfilter(LotgdRequest::getServer('REMOTE_ADDR'))
                        ->setBanreason(LotgdTranslator::t('login.banMessage', [], 'page_login'))
                        ->setBanexpire($banexpire->add(new DateInterval('PT15M'))) //-- Added 15 minutes
                        ->setBanner('System')
                        ->setLasthit(new DateTime('0000-00-00 00:00:00'))
                    ;

                    Doctrine::persist($failLog);
                    Doctrine::flush(); //Persist objects

                    if ($su)
                    {
                        // send a system message to admins regarding
                        // this failed attempt if it includes superusers.
                        $result2 = $repositoryAccounts->getSuperuserWithPermit(SU_EDIT_USERS);

                        $subj = sprintf('%s failed to log in too many times!', LotgdRequest::getServer('REMOTE_ADDR'));

                        foreach ($result2 as $row2)
                        {
                            $msg = sprintf('This message is generated as a result of one or more of the accounts having been a superuser account.  Log Follows:`n`n%s', $alert);
                            LotgdKernel::get('lotgd_core.tool.system_mail')->send($row2['acctid'], $subj, $msg, 0);
                        }//end for
                    }//end if($su)
                }//end if($c>=10)
            }//end foreach
        }//end if (DB::num_rows)

        redirect('index.php');
    }

    //-- Rehash password if need
    if ($passwordEncoder->needsRehash($account))
    {
        $repositoryAccounts->upgradePassword($account, $passwordEncoder->encodePassword($account, $password));
    }

    unset($password, $passwordEncoder);

    $session['user'] = $repositoryAccounts->getUserById($account->getAcctid());

    LotgdTool::checkBan($session['user']['login']); //check if this account is banned

    // If the player isn't allowed on for some reason, anything on
    // this hook should automatically call page_footer and exit
    // itself.
    LotgdEventDispatcher::dispatch(new Core(), Core::LOGIN_CHECK);

    if ('' != $session['user']['emailvalidation'] && 'x' != substr($session['user']['emailvalidation'], 0, 1))
    {
        $session['user'] = [];
        LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('login.validate', [], 'page_login'));

        redirect('home.php');
    }

    $session['loggedin']       = true;
    $session['laston']         = date('Y-m-d H:i:s');
    $session['sentnotice']     = 0;
    $session['user']['laston'] = new DateTime('now');

    LotgdKernel::get('cache.app')->delete('char-list-home-page');

    // Let's throw a login module hook in here so that modules
    // like the stafflist which need to invalidate the cache
    // when someone logs in or off can do so.
    LotgdEventDispatcher::dispatch(new Core(), Core::LOGIN_PLAYER);

    //-- Check for valid restorepage
    if (empty($session['user']['restorepage']) || is_numeric($session['user']['restorepage']) || 'login.php' == $session['user']['restorepage'])
    {
        $session['user']['restorepage'] = 'news.php';
    }

    if ($session['user']['loggedin'])
    {
        $link = sprintf('<a href="%s">%s</a>', $session['user']['restorepage'], $session['user']['restorepage']);

        $str = LotgdTranslator::t('login.redirect', ['link' => $link], 'page_login');
        header("Location: {$session['user']['restorepage']}");
        LotgdTool::saveUser();
        echo $str;

        exit();
    }

    $session['user']['loggedin'] = true;

    if ($session['user']['location'] == $iname)
    {
        redirect('inn.php?op=strolldown');
    }
    elseif ($session['user']['restorepage'] > '')
    {
        redirect($session['user']['restorepage']);
    }

    redirect('news.php');
}
elseif ('logout' == $op)
{
    LotgdResponse::callController(LoginController::class, 'logout');
}

//- If you enter an empty username, don't just say oops.. do something useful.
LotgdSession::invalidate();
LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('fail.empty', [], 'page_login'));

redirect('index.php');
