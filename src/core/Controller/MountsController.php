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

use Lotgd\Core\Controller\NewdayController\DragonPointSpendTrait;
use Lotgd\Core\Controller\NewdayController\NewDayTrait;
use Lotgd\Core\Controller\NewdayController\RecalculateDragonPointTrait;
use Lotgd\Core\Controller\NewdayController\SetRaceTrait;
use Lotgd\Core\Controller\NewdayController\SetSpecialtyTrait;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Repository\AvatarRepository;
use Lotgd\Core\Tool\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tracy\Debugger;
use Throwable;

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

    /** @inheritDoc */
    public function allowAnonymous(): bool
    {
        return false;
    }

    /** @inheritDoc */
    public function overrideForcedNav(): bool
    {
        return true;
    }
}
