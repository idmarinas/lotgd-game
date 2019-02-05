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
     *
     * @return string
     */
    public function userPetition(): string
    {
        return \LotgdTheme::renderThemeTemplate('parts/user-petition.twig', []);
    }

    /**
     * Get admin petition links.
     *
     * @return string
     */
    public function adminPetition(): string
    {
        global $session;

        $canEditPetitions = (($session['user']['superuser'] ?? 0) & SU_EDIT_PETITIONS);
        $canEditUsers = (($session['user']['superuser'] ?? 0) & SU_EDIT_USERS);
        $petitions = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];

        if (isset($session['user']['superuser']) && $canEditPetitions)
        {
            $petition = \Doctrine::getRepository(\Lotgd\Core\Entity\Petitions::class);
            $petitions = $petition->getStatusListCount();
        }

        return \LotgdTheme::renderThemeTemplate('parts/admin-petition.twig', [
            'canEditPetitions' => $canEditPetitions,
            'canEditUsers' => $canEditUsers,
            'petitions' => $petitions
        ]);
    }
}
