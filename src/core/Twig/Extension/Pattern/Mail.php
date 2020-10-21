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

/**
 * Trait to created ye olde mail link.
 */
trait Mail
{
    /**
     * Get ye olde mail link.
     */
    public function yeOldeMail(): string
    {
        global $session;

        \trigger_error(\sprintf(
            'Usage of %s (ye_olde_mail() Twig function) is obsolete since 4.5.0; and delete in version 5.0.0, use "{%% block ye_olde_mail parent()0 %%}" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        try
        {
            $mail   = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);
            $result = $mail->getCountMailOfCharacter((int) ($session['user']['acctid'] ?? 0));
        }
        catch (\Throwable $th)
        {
            $result = [
                'seenCount'    => 0,
                'notSeenCount' => 0,
            ];
        }

        $template = $this->getTemplate()->load("@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_buttons.html.twig");

        return $template->renderBlock('ye_olde_mail', ['yeOldeMail' => $result]);
    }
}
