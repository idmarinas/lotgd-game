<?php

// translator ready
// addnews ready
// mail ready
function redirect($location, $reason = false)
{
    global $session, $REQUEST_URI, $lotgdServiceManager;

    // This function is deliberately not localized.  It is meant as error
    // handling.
    if (! isset($session['debug']))
    {
        $session['debug'] = '';
    }

    if (false === strpos($location, 'badnav.php'))
    {
        //deliberately html in translations so admins can personalize this, also in one schema
        $session['allowednavs'] = [];
        addnav('', $location);
        $failoutput = $lotgdServiceManager->build(Lotgd\Core\Output\Collector::class);
        $failoutput->output_notl('`lWhoops, your navigation is broken. Hopefully we can restore it.`n`n');
        $failoutput->output_notl('`$');
        $failoutput->rawoutput('<a href="'.htmlentities($location, ENT_COMPAT, getsetting('charset', 'UTF-8')).'">'.translate_inline('Click here to continue.', 'badnav').'</a>');
        $failoutput->output_notl(translate_inline("`n`n`\$If you cannot leave this page, notify the staff via <a href='petition.php'>petition</a> `\$and tell them where this happened and what you did. Thanks.", 'badnav'), true);
        $text = $failoutput->get_output();
        $title = translate_inline('Your navigation is broken');
        $session['output'] = "<html><head><title>$title</title></head><body style='background-color: #ffffff'>$text</body></html>";
    }
    restore_buff_fields();
    $session['debug'] .= "Redirected to $location from $REQUEST_URI.  $reason<br>";
    saveuser();
    $host = $_SERVER['HTTP_HOST'];

    if (443 == $_SERVER['SERVER_PORT'])
    {
        $http = 'https';
    }
    else
    {
        $http = 'http';
    }

    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: $http://$host$uri/$location");

    // we should never hit this one here. in case we do, show the debug output along with some text
    // this might be the case if your php session handling is messed up or something.
    echo translate_inline("Whoops. There has been an error concering redirecting your to your new page. Please inform the admins about this. More Information for your petition down below:\n\n");
    echo $session['debug'];

    exit();
}
