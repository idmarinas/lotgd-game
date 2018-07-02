<?php

//translator ready

define('OVERRIDE_FORCED_NAV', true);
require_once 'common.php';

function mail_status($args = false)
{
    if (false === $args)
    {
        return;
    }
    $timeout_setting = 120; // seconds
    $new = maillink();
    $objResponse = new xajaxResponse();
    $objResponse->assign('maillink', 'innerHTML', $new);
    /*	global $session;
        $warning='';
        $timeout=strtotime($session['user']['laston'])-strtotime(date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds")));
        if ($timeout<=1) {
            $warning="<br/>".appoencode("`\$`b")."Your session has timed out!".appoencode("`b");
        } elseif ($timeout<120){
            $warning="<br/>".appoencode("`t").sprintf("TIMEOUT in %s seconds!",$timeout);
        } else $warning='';
        $objResponse->assign("notify","innerHTML", $warning);*/
    return $objResponse;
}

function timeout_status($args = false)
{
    if (false === $args)
    {
        return;
    }

    global $session;

    $timeout_setting = 120; // seconds
    $warning = '';
    $timeout = strtotime($session['user']['laston']) - strtotime(date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds')));

    if ($timeout <= 1)
    {
        $text = translate_inline('Your session has timed out!');
        $warning = '<b>'.$text.'</b>';
    }
    elseif ($timeout < 120)
    {
        $text = translate_inline('TIMEOUT in %s seconds!');
        $warning = sprintf($text, $timeout);
    }
    else
    {
        $warning = '';
    }

    if ('' == $warning)
    {
        return;
    }

    $objResponse = new xajaxResponse();
    $objResponse->assign('notify', 'innerHTML', $warning);

    return $objResponse;
}

function commentary_text($args = false)
{
    global $session;

    if (false === $args || ! is_array($args))
    {
        return;
    }
    $section = $args['section'];
    $message = '';
    $limit = 25;
    $talkline = translate_inline('says');
    $schema = $args['schema'];
    $viewonly = $args['viewonly'];
    $new = viewcommentary($section, $message, $limit, $talkline, $schema, $viewonly, 1);
    $new = maillink();
    $objResponse = new xajaxResponse();
    $objResponse->assign($section, 'innerHTML', $new);
}

require 'mailinfo_common.php';
$xajax->processRequest();
