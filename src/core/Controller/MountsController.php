<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 8.0.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Http\Request;
use Lotgd\Core\Repository\AvatarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Throwable;
use Tracy\Debugger;

class MountsController extends AbstractController implements LotgdControllerInterface
{
    private AvatarRepository $avatarRepository;

    public function __construct(AvatarRepository $avatarRepository)
    {
        $this->avatarRepository = $avatarRepository;
    }

    public function listOfOwner(Request $request)
    {
        $mountId = $request->query->getInt('mountId', 0);

        try
        {
            $entities = $this->avatarRepository->findBy(['hashorse' => $mountId]);
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            $entities = [];
        }

        return $this->render('admin/page/mounts/owners-list-modal.html.twig', ['entities' => $entities]);
    }

    /** {@inheritDoc} */
    public function allowAnonymous(): bool
    {
        return false;
    }

    /** {@inheritDoc} */
    public function overrideForcedNav(): bool
    {
        return true;
    }
}
