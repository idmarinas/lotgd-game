<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension\Pattern;

use Lotgd\Core\Entity\Petitions;
use Twig\Environment;

/**
 * Trait to created user petition link.
 */
trait Petition
{
    /**
     * Get user petition link.
     */
    public function userPetition(Environment $env): string
    {
        return $env->load('_blocks/_buttons.html.twig')->renderBlock('user_petition', []);
    }

    /**
     * Get admin petition links.
     */
    public function adminPetition(Environment $env): string
    {
        global $session;

        $canEditPetitions = (($session['user']['superuser'] ?? 0) & SU_EDIT_PETITIONS);
        $canEditUsers     = (($session['user']['superuser'] ?? 0) & SU_EDIT_USERS);
        $petitions        = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];

        if (isset($session['user']['superuser']) && $canEditPetitions)
        {
            $petition  = $this->doctrine->getRepository(Petitions::class);
            $petitions = $petition->getStatusListCount();
        }

        return $env->load('_blocks/_buttons.html.twig')->renderBlock('admin_petition', [
            'canEditPetitions' => $canEditPetitions,
            'canEditUsers'     => $canEditUsers,
            'petitions'        => $petitions,
        ]);
    }
}
