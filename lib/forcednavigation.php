<?php

// translator ready
// addnews ready
// mail ready

function do_forced_nav($anonymous, $overrideforced)
{
    global $session;

    $requestUri = \LotgdRequest::getServer('REQUEST_URI');

    \LotgdResponse::pageAddContent("<!--\nAllowAnonymous: ".($anonymous ? 'True' : 'False')."\nOverride Forced Nav: ".($overrideforced ? 'True' : 'False')."\n-->");

    if ($session['user']['acctid'] ?? false)
    {
        //-- Using Doctrine repository to get data of account
        $repositoryAccounts = \Doctrine::getRepository('LotgdCore:User');
        $account            = $repositoryAccounts->getUserById($session['user']['acctid']);

        if ( ! $account)
        {
            $session            = [];
            $session['message'] = \LotgdTranslator::t('session.login.incorrect', [], 'app_default');

            redirect('home.php', \LotgdTranslator::t('session.login.account.disappeared', [], 'app_default'));
        }

        $session['user']                = $account;
        $session['bufflist']            = $session['user']['bufflist']    ?? [];
        $session['user']['allowednavs'] = $session['user']['allowednavs'] ?? [];

        if ( ! $session['user']['loggedin'] || ((\time() - $session['user']['laston']->getTimestamp()) > LotgdSetting::getSetting('LOGINTIMEOUT', 900)))
        {
            $session = [];

            \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('session.timeout', [], 'app_default'));

            redirect('home.php', \LotgdTranslator::t('session.login.account.notLogged', [], 'app_default'));
        }

        if (($session['user']['allowednavs'][$requestUri] ?? false) && true !== $overrideforced)
        {
            $session['user']['allowednavs'] = [];
        }
        elseif (true !== $overrideforced)
        {
            redirect('badnav.php', \LotgdTranslator::t('session.login.account.notAllowed', ['uri' => $requestUri], 'app_default'));
        }
    }
    elseif ( ! $anonymous)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('session.timeout', [], 'app_default'));

        redirect('home.php', \LotgdTranslator::t('session.login.anonymous.notLogged', ['uri' => $requestUri], 'app_default'));
    }
}
