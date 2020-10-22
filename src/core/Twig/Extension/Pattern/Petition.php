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
 * Trait to created user petition link.
 */
trait Petition
{
    /**
     * Get user petition link.
     */
    public function userPetition(): string
    {
        \trigger_error(\sprintf(
            'Usage of %s (user_petition() Twig function) is obsolete since 4.5.0; and delete in version 5.0.0, use "{%% block user_petition parent() %%}" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $template = $this->getTemplate()->load("@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_buttons.html.twig");

        return $template->renderBlock('user_petition', []);
    }

    /**
     * Get admin petition links.
     */
    public function adminPetition(): string
    {
        global $session;

        \trigger_error(\sprintf(
            'Usage of %s (admin_petition() Twig function) is obsolete since 4.5.0; and delete in version 5.0.0, use "{%% block admin_petition parent() %%}" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $canEditPetitions = (($session['user']['superuser'] ?? 0) & SU_EDIT_PETITIONS);
        $canEditUsers     = (($session['user']['superuser'] ?? 0) & SU_EDIT_USERS);
        $petitions        = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];

        if (isset($session['user']['superuser']) && $canEditPetitions)
        {
            $petition  = \Doctrine::getRepository(\Lotgd\Core\Entity\Petitions::class);
            $petitions = $petition->getStatusListCount();
        }

        $template = $this->getTemplate()->load("@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_buttons.html.twig");

        return $template->renderBlock('admin_petition', [
            'adminPetition' => [
                'canEditPetitions' => $canEditPetitions,
                'canEditUsers'     => $canEditUsers,
                'petitions'        => $petitions,
            ]
        ]);
    }
}
