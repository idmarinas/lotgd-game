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

use Doctrine\ORM\EntityManagerInterface;
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

class BansController extends AbstractController implements LotgdControllerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function showAffected(Request $request)
    {
        $id = $request->query->get('id', '');
        $ip = $request->query->get('ip', '');

        try
        {
            $query = $this->em->createQuery("SELECT c.name FROM Lotgd\Core\Entity\Bans b, LotgdCore:User a
                LEFT JOIN LotgdCore:Avatar c WITH c.acct = a.acctid
                WHERE
                    (b.ipfilter = :ip AND b.uniqueid = :id) AND
                    ( (substring(a.lastip,1,length(b.ipfilter)) = b.ipfilter AND b.ipfilter != '') OR (a.uniqueid = b.uniqueid AND b.uniqueid != '') )
            ");

            $query->setParameter('id', $id)
                ->setParameter('ip', $ip)
            ;

            $result = $query->execute();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            $result = [];
        }

        return $this->render('admin/page/bans/show-affected-modal.html.twig', ['result' => $result]);
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
