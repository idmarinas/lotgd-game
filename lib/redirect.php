<?php

// translator ready
// addnews ready
// mail ready
function redirect($location, $reason = false)
{
    global $session, $lotgdJaxon;

    // This function is deliberately not localized.  It is meant as error handling.
    $session['debug'] = $session['debug'] ?? '';

    if (false === strpos($location, 'badnav.php'))
    {
        //deliberately html in translations so admins can personalize this, also in one schema
        $session['user']['allowednavs'] = [];

        \LotgdNavigation::addNavAllow($location);

        $content = \LotgdTranslator::t('redirect.badnav.content', [
            'locationUrl' => $location,
            'petitionUrl' => 'petition.php'
        ], 'app-default');

        //-- Finalize output
        $lotgdJaxon->processRequest();

        $failOutput = [
            'title' => [
                'title' => 'redirect.badnav.title',
                'params' => [],
                'textDomain' => 'app-default'
            ],
            'content' => appoencode($content, true),
            'csshead' => $lotgdJaxon->getCss(),
            'scripthead' => $lotgdJaxon->getJs(),
            'scripthead' => $lotgdJaxon->getScript()
        ];
        $session['output'] =  \LotgdTheme::renderThemeTemplate('popup.twig', $failOutput);
    }

    restore_buff_fields();
    $session['debug'] = \LotgdTranslator::t('redirect.redirection', [
        'locationTo' => $location,
        'locationFrom' => \LotgdHttp::getServer('REQUEST_URI'),
        'reason' => $reason
    ], 'app-default');
    saveuser();

    $host = \LotgdHttp::getServer('HTTP_HOST');
    $http = (443 == \LotgdHttp::getServer('SERVER_PORT')) ? 'https' : 'http';
    $uri = rtrim(dirname(\LotgdHttp::getServer('PHP_SELF')), '/\\');

    header(sprintf("Location: %s://%s%s/%s",
        $http,
        $host,
        $uri,
        $location
    ));

    // we should never hit this one here. in case we do, show the debug output along with some text
    // this might be the case if your php session handling is messed up or something.
    // echo \LotgdTranslator::t('redirect.whoops', [], 'app-default');
    // echo $session['debug'];


    exit();
}
