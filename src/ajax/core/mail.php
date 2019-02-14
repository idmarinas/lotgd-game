<?php

namespace Lotgd\Ajax\Core;

class Mail
{
    public function status()
    {
        global $session;

        $response = new \Jaxon\Response\Response();

        //-- Do nothing if there is no active session
        if (! $session['user']['loggedin'])
        {
            return $response;
        }

        $mail = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);
        $result = $mail->getCountMailOfCharacter((int) ($session['user']['acctid'] ?? 0));

        $response->html('ye-olde-mail-count-text', \LotgdTranslator::t('parts.mail.title', [
            'new' => $result['notSeenCount'],
            'old' => $result['seenCount']
        ], 'app-default'));

        return $response;
    }
}
