<?php

// translator ready
// addnews ready
// mail ready

function do_forced_nav($anonymous, $overrideforced)
{
    global $session;

    $request = \LotgdLocator::get(\Lotgd\Core\Http::class);
    $requestUri = $request->getRequestUri();

    rawoutput("<!--\nAllowAnonymous: ".($anonymous ? 'True' : 'False')."\nOverride Forced Nav: ".($overrideforced ? 'True' : 'False')."\n-->");

    if ($session['loggedin'] ?? false && $session['user']['acctid'] ?? false)
    {
        //-- Using Doctrine repository to process login
        $repositoryAccounts = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);
        $account = $repositoryAccounts->getUserById($session['user']['acctid']);

        if (! $account)
        {
            $session = [];
            $session['message'] = translate_inline('`4Error, your login was incorrect`0', 'login');

            return redirect('index.php', 'Account Disappeared!');
        }

        $session['user'] = $account;
        $session['bufflist'] = $session['user']['bufflist'] ?? [];
        $session['user']['allowednavs'] = $session['user']['allowednavs'] ?? [];

        if (! $session['user']['loggedin'] || ((time() - $session['user']['laston']->getTimestamp()) > getsetting('LOGINTIMEOUT', 900)))
        {
            $session = [];

            return redirect('index.php?op=timeout', 'Account not logged in but session thinks they are.');
        }

        if ($session['user']['allowednavs'][$requestUri] ?? false && true !== $overrideforced)
        {
            $session['user']['allowednavs'] = [];
        }
        elseif (true !== $overrideforced)
        {
            return redirect('badnav.php', "Navigation not allowed to $requestUri");
        }
    }
    elseif (! $anonymous)
    {
        return redirect('index.php?op=timeout', "Not logged in: $requestUri");
    }
}
