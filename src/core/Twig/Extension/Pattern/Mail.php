<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension\Pattern;

use Twig\Environment;

/**
 * Trait to created ye olde mail link.
 */
trait Mail
{
    /**
     * Get ye olde mail link.
     */
    public function yeOldeMail(Environment $env): string
    {
        global $session;

        try
        {
            $mail   = $this->doctrine->getRepository(\Lotgd\Core\Entity\Mail::class);
            $result = $mail->getCountMailOfCharacter((int) ($session['user']['acctid'] ?? 0));
        }
        catch (\Throwable $th)
        {
            $result = [
                'seenCount'    => 0,
                'notSeenCount' => 0,
            ];
        }

        return $env->load('{theme}/_blocks/_buttons.html.twig')->renderBlock('ye_olde_mail', $result);
    }
}
