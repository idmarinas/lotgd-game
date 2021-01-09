<?php

// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/checkban.php';
require_once 'lib/serverfunctions.class.php';

checkban();

$textDomain = 'page_create'; //-- Namespace, textDomain for page

$trash = (int) getsetting('expiretrashacct', 1);
$new = (int) getsetting('expirenewacct', 10);
$old = (int) getsetting('expireoldacct', 45);
$params = [
    'allowCreation' => (bool) getsetting('allowcreation', 1),
    'serverFull' => ServerFunctions::isTheServerFull(),
    'requireEmail' => (int) getsetting('requireemail', 0),
    'requireValidEmail' => (int) getsetting('requirevalidemail', 0),
    'acctTrash' => $trash,
    'acctNew' => $new,
    'acctOld' => $old
];

$op = (string) \LotgdRequest::getQuery('op');

//-- Init page
\LotgdResponse::pageStart('title.create', [], $textDomain);

if ('val' == $op || 'forgotval' == $op)
{
    \LotgdResponse::pageTitle('title.validate', [], $textDomain);
}

\LotgdNavigation::addHeader('common.category.login');
\LotgdNavigation::addNav('common.nav.login', 'index.php');

$accountRepo = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);

//-- Check server are in maintenance
if (getsetting('fullmaintenance', 0) || getsetting('maintenance', 0))
{
    $params['maintenanceMode'] = true;
}

if ('forgotval' == $op)
{
    $forgottenCode = \LotgdRequest::getQuery('id', 0);

    $account = $accountRepo->findOneBy(['forgottenpassword' => $forgottenCode]);

    if (! $account)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('validating.pass.paragraph', [], $textDomain));

        return redirect('home.php');
    }

    //-- Delete code of fogotten password
    $account->setForgottenpassword('');

    //-- Save
    \Doctrine::merge($account);

    //-- Rare case: we have somebody who deleted his first validation email and then requests a forgotten PW...
    if ('' != $account->getEmailvalidation() && 'x' != substr($account->getEmailvalidation(), 0, 1))
    {
        $account->getEmailvalidation('');
    }

    \Doctrine::flush(); //-- Persist objects

    $params['account'] = $account;

    $params = modulehook('page-create-forgotval-tpl-params', $params);
    \LotgdResponse::pageAddContent(LotgdTheme::renderBlock('create_forgot_val', '{theme}/pages/_blocks/_create.html.twig', $params));

    //-- Finalize page
    \LotgdResponse::pageEnd();
}
elseif ('val' == $op)
{
    $code = \LotgdRequest::getQuery('id', 0);

    $account = $accountRepo->findOneBy(['emailvalidation' => $code]);

    if (! $account)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('validating.email.paragraph.fail', [], $textDomain));

        return redirect('home.php');
    }

    $params['showLoginButton'] = (bool) ! ($account->getReplaceemail());

    if ($account->getReplaceemail())
    {
        $params['emailChanged'] = true;
        $replace_array = explode('|', $account->getReplaceemail());
        $replaceemail = $replace_array[0]; //1==date
        $oldEmail = $account->getEmailaddress();

        debuglog('Email change request validated by link from '.$oldEmail.' to '.$replaceemail, $account->getAcctid(), $account->getAcctid(), 'Email');

        //-- Note: remove any forgotten password request!
        $account->setReplaceemail('')
            ->setEmailaddress($replaceemail)
            ->setForgottenpassword('')
        ;

        //-- If a superuser changes email, we want to know about it... at least those who can ee it anyway, the user editors...
        if ($account->getSuperuser() > 0)
        {
            // 5 failed attempts for superuser, 10 for regular user
            // send a system message to admin
            require_once 'lib/systemmail.php';

            $acctSuper = $accountRepo->getSuperuserWithPermit(SU_EDIT_USERS);

            if (count($acctSuper))
            {
                $subj = \LotgdTranslator::t('yeoldemail.subject', ['name' => $account->getName()], $textDomain);
                $alert = \LotgdTranslator::t('yeoldemail.alert', [
                    'newEmail' => $replaceemail,
                    'oldMail' => $oldEmail,
                    'login' => $account->getLogin()
                ], $textDomain);
                $msg = \LotgdTranslator::t('yeoldemail.message', ['alert' => $alert], $textDomain);

                foreach ($acctSuper as $user)
                {
                    systemmail($user['acctid'], $subj, $msg, 0, $noemail);
                }
            }
        }
    }

    //-- Delete code of email validation
    $account->setEmailvalidation('');

    //-- Save
    \Doctrine::merge($account);

    \Doctrine::flush(); //-- Persist objects

    $params['account'] = $account;

    savesetting('newestplayer', $account->getAcctid());
    savesetting('newestplayername', $account->getCharacter()->getName());

    $params = modulehook('page-create-val-tpl-params', $params);
    \LotgdResponse::pageAddContent(LotgdTheme::renderBlock('create_email_val', '{theme}/pages/_blocks/_create.html.twig', $params));

    //-- Finalize page
    \LotgdResponse::pageEnd();
}
elseif ('forgot' == $op)
{
    $charname = (string) \LotgdRequest::getPost('charname', '');

    if ($charname)
    {
        $account = $accountRepo->findOneBy(['login' => $charname]);

        if (! $account)
        {
            \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('forgot.account.notFound', [], $textDomain));

            return redirect('create.php?op=forgot');
        }

        if (! trim($account->getEmailaddress()))
        {
            \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('forgot.account.noEmail', [], $textDomain));

            return redirect('create.php?op=forgot');
        }

        if ('' == $account->getForgottenpassword())
        {
            $account->setForgottenpassword(substr('x'.md5(date('Y-m-d H:i:s').$account->getPassword()), 0, 32));
        }

        $language = ($account->getPrefs()['language'] ?? '') ?: getsetting('defaultlanguage', 'en');

        $subj = \LotgdTranslator::t('forgotpassword.subject', [], 'app_mail', $language);
        $msg = \LotgdTranslator::t('forgotpassword.body', [
            'login' => $account->getLogin(),
            'acctid' => $account->getAcctid(),
            'emailaddress' => $account->getEmailaddress(),
            'requester_ip' => \LotgdRequest::getServer('REMOTE_ADDR'),
            'gameurl' => '//'.(\LotgdRequest::getServer('SERVER_NAME').'/'.\LotgdRequest::getServer('SCRIPT_NAME')),
            'forgottenid' => $account->getForgottenpassword(),
        ], 'app_mail', $language);

        lotgd_mail($account->getEmailaddress(), $subj, \LotgdFormat::colorize($msg, true));

        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('forgot.account.sent', [], $textDomain));

        return redirect('create.php?op=forgot');
    }

    $params = modulehook('page-create-forgot-tpl-params', $params);
    \LotgdResponse::pageAddContent(LotgdTheme::renderBlock('create_forgot', '{theme}/pages/_blocks/_create.html.twig', $params));

    //-- Finalize page
    \LotgdResponse::pageEnd();
}
elseif ('create' == $op)
{
    $emailverification = '';
    $shortname = trim((string) \LotgdRequest::getPost('name'));
    $shortname = \LotgdSanitize::nameSanitize(getsetting('spaceinname', 0), $shortname);
    $censor = \LotgdKernel::get(\Lotgd\Core\Output\Censor::class);

    if ($censor->filter($shortname) != $shortname)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.badLanguage', [], $textDomain));

        return redirect('create.php');
    }

    $blockaccount = false;
    $email = (string) \LotgdRequest::getPost('email');
    $pass1 = (string) \LotgdRequest::getPost('pass1');
    $pass2 = (string) \LotgdRequest::getPost('pass2');

    if (1 == getsetting('blockdupeemail', 0) && 1 == getsetting('requireemail', 0))
    {
        $result = $accountRepo->findBy(['emailaddress' => $email]);

        if ($result)
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.email.duplicate', [], $textDomain));

            return redirect('create.php');
        }
    }

    $passlen = (int) \LotgdRequest::getPost('passlen');

    if ('!md5!' != substr($pass1, 0, 5) && '!md52!' != substr($pass1, 0, 6))
    {
        $passlen = strlen($pass1);
    }

    if ($passlen <= 3)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.password.length', [], $textDomain));
        $blockaccount = true;
    }

    if ($pass1 != $pass2)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.password.notIdentical', [], $textDomain));
        $blockaccount = true;
    }

    if (strlen($shortname) < 3)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.name.minLength', [], $textDomain));
        $blockaccount = true;
    }

    if (strlen($shortname) > 25)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.name.maxLength', [], $textDomain));
        $blockaccount = true;
    }

    if (1 == (int) getsetting('requireemail', 0) && ! is_email($email))
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.email.incorrect', [], $textDomain));
        $blockaccount = true;
    }

    $args = modulehook('check-create', \LotgdRequest::getPostAll());

    if (isset($args['blockaccount']) && $args['blockaccount'])
    {
        $blockaccount = true;
    }

    if ($blockaccount)
    {
        return redirect('create.php');
    }

    $shortname = preg_replace("/\s+/", ' ', $shortname);
    $result = $accountRepo->findOneBy(['login' => $shortname]);

    if ($result)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.name.duplicate', [], $textDomain));

        return redirect('create.php');
    }

    $sex = (int) \LotgdRequest::getPost('sex');
    // Inserted the following line to prevent hacking
    // Reported by Eliwood
    if (SEX_MALE != $sex)
    {
        $sex = SEX_FEMALE;
    }

    require_once 'lib/titles.php';

    $title = get_dk_title(0, $sex);

    if (getsetting('requirevalidemail', 0))
    {
        $emailverification = md5(date('Y-m-d H:i:s').$email);
    }
    $refer = \LotgdRequest::getQuery('r');

    $referer = 0;

    if ($refer > '')
    {
        $result = $accountRepo->findOneBy(['login' => $refer]);
        $referer = $result->getAcctid();
    }

    $dbpass = '';

    if ('!md5!' == substr($pass1, 0, 5))
    {
        $dbpass = md5(substr($pass1, 5));
    }
    else
    {
        $dbpass = md5(md5($pass1));
    }

    try
    {
        //-- Configure account
        $accountEntity = new \Lotgd\Core\Entity\Accounts();
        $accountEntity->setLogin((string) $shortname)
            ->setPassword((string) $dbpass)
            ->setSuperuser((int) getsetting('defaultsuperuser', 0))
            ->setRegdate(new \DateTime())
            ->setUniqueid(\LotgdRequest::getCookie('lgi') ?: '')
            ->setLastip(\LotgdRequest::getServer('REMOTE_ADDR'))
            ->setEmailaddress($email)
            ->setEmailvalidation($emailverification)
            ->setReferer($referer)
        ;

        //-- Need for get a ID of new account
        \Doctrine::persist($accountEntity);
        \Doctrine::flush(); //Persist objects

        //-- Configure character
        $characterEntity = new \Lotgd\Core\Entity\Characters();
        $characterEntity->setPlayername((string) $shortname)
            ->setSex($sex)
            ->setName("{$title} {$shortname}")
            ->setTitle($title)
            ->setGold((int) getsetting('newplayerstartgold', 50))
            ->setLocation(getsetting('villagename', LOCATION_FIELDS))
            ->setAcct($accountEntity)

        ;

        //-- Need for get ID of new character
        \Doctrine::persist($characterEntity);
        \Doctrine::flush(); //-- Persist objects

        //-- Set ID of character and update Account
        $accountEntity->setCharacter($characterEntity);
        \Doctrine::persist($accountEntity);
        \Doctrine::flush(); //-- Persist objects

        $args = \LotgdRequest::getPostAll();
        $args['acctid'] = $accountEntity->getAcctid();
        modulehook('process-create', $args);

        if ('' != $emailverification)
        {
            $subj = \LotgdTranslator::t('verificationmail.subject', [], 'app_mail');
            $msg = \LotgdTranslator::t('verificationmail.body', [
                'login' => $shortname,
                'acctid' => $accountEntity->getAcctid(),
                'emailaddress' => $accountEntity->getEmailaddress(),
                'gameurl' => 'https://'.(\LotgdRequest::getServer('SERVER_NAME').'/'.\LotgdRequest::getServer('SCRIPT_NAME')),
                'validationid' => $emailverification,
            ], 'app_mail');

            lotgd_mail($email, $subj, \LotgdFormat::colorize($msg, true));

            \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('create.account.emailVerification', ['email' => $email], $textDomain));

            return redirect('index.php');
        }

        savesetting('newestplayer', $accountEntity->getAcctid());
        savesetting('newestplayername', $characterEntity->getName());

        $params['login'] = $shortname;
        $params['password'] = $pass1;

        \LotgdResponse::pageAddContent(\LotgdTheme::renderBlock('create_account_login', '{theme}/pages/_blocks/_create.html.twig', $params));

        //-- Finalize page
        \LotgdResponse::pageEnd();
    }
    catch (\Throwable $th)
    {
        \Tracy\Debugger::log($th);

        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('create.account.error', [], $textDomain));

        return redirect('create.php');
    }
}

$refer = (string) \LotgdRequest::getQuery('r');

$params['refer'] = '';

if ($refer)
{
    $params['refer'] = '&r='.htmlentities($refer, ENT_COMPAT, getsetting('charset', 'UTF-8'));
}

/**
 * Get all templates with params for form
 * Example: [
 *      // (string) tplName => array []
 *      'tpl-tame' => ['key' => 'value']
 * ].
 */
$result = modulehook('create-form', ['templates' => []]);
$params['templates'] = $result['templates'];

\Doctrine::flush(); //-- Persist objects

$params = modulehook('page-create-tpl-params', $params);
\LotgdResponse::pageAddContent(\LotgdTheme::render('{theme}/pages/create.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
