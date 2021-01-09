<?php

// translator ready
// addnews ready
// mail ready
function redirect($location, $reason = false)
{
    global $session;

    // This function is deliberately not localized.  It is meant as error handling.
    $session['debug'] = $session['debug'] ?? '';

    if (false === \strpos($location, 'badnav.php'))
    {
        //deliberately html in translations so admins can personalize this, also in one schema
        $session['user']['allowednavs'] = [];

        \LotgdNavigation::addNavAllow($location);

        $content = \LotgdTranslator::t('redirect.badnav.content', [
            'locationUrl' => $location,
            'petitionUrl' => 'petition.php',
        ], 'app_default');

        \LotgdResponse::pageTitle('redirect.badnav.title', [], 'app_default');
        \LotgdTheme::getTemplateParams()->set('content', \LotgdFormat::colorize($content, true));

        $session['output'] = \LotgdTheme::renderLayout(\LotgdTheme::getTemplateParams()->toArray());
    }

    restore_buff_fields();
    $session['debug'] = \LotgdTranslator::t('redirect.redirection', [
        'locationTo'   => $location,
        'locationFrom' => \LotgdRequest::getServer('REQUEST_URI'),
        'reason'       => $reason,
    ], 'app_default');
    saveuser();

    $host = \LotgdRequest::getServer('HTTP_HOST');
    $http = (443 == \LotgdRequest::getServer('SERVER_PORT')) ? 'https' : 'http';
    $uri  = \rtrim(\dirname(\LotgdRequest::getServer('PHP_SELF')), '/\\');

    \header(\sprintf(
        'Location: %s://%s%s/%s',
        $http,
        $host,
        $uri,
        $location
    ));

    // we should never hit this one here. in case we do, show the debug output along with some text
    // this might be the case if your php session handling is messed up or something.
    // echo \LotgdTranslator::t('redirect.whoops', [], 'app_default');
    // echo $session['debug'];

    exit();
}
