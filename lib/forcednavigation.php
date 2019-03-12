<?php

// translator ready
// addnews ready
// mail ready

function do_forced_nav($anonymous, $overrideforced)
{
    global $session;

    $requestUri = \LotgdHttp::getServer('REQUEST_URI');

    rawoutput("<!--\nAllowAnonymous: ".($anonymous ? 'True' : 'False')."\nOverride Forced Nav: ".($overrideforced ? 'True' : 'False')."\n-->");

    if ($session['user']['acctid'] ?? false)
    {
        //-- Using Doctrine repository to get data of account
        $repositoryAccounts = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);
        $account = $repositoryAccounts->getUserById($session['user']['acctid']);

        if (! $account)
        {
            $session = [];
            $session['message'] = \LotgdTranslator::t('session.login.incorrect', [], 'app-default');

            return redirect('home.php', \LotgdTranslator::t('session.login.account.disappeared', [], 'app-default'));
        }

        $session['user'] = $account;
        $session['bufflist'] = $session['user']['bufflist'] ?? [];
        $session['user']['allowednavs'] = $session['user']['allowednavs'] ?? [];

        if (! $session['user']['loggedin'] || ((time() - $session['user']['laston']->getTimestamp()) > getsetting('LOGINTIMEOUT', 900)))
        {
            $session = [];

            \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('session.timeout', [], 'app-default'));

            return redirect('home.php', \LotgdTranslator::t('session.login.account.notLogged', [], 'app-default'));
        }

        if (($session['user']['allowednavs'][$requestUri] ?? false) && true !== $overrideforced)
        {
            $session['user']['allowednavs'] = [];
        }
        elseif (true !== $overrideforced)
        {
            return redirect('badnav.php', \LotgdTranslator::t('session.login.account.notAllowed', ['uri' => $requestUri], 'app-default'));
        }
    }
    elseif (! $anonymous)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('session.timeout', [], 'app-default'));

        return redirect('home.php', \LotgdTranslator::t('session.login.anonymous.notLogged', ['uri' => $requestUri], 'app-default'));
    }
}
