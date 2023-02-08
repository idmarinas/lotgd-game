<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.2.0
 */

namespace Lotgd\Core\Service;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Event\Other;
use Lotgd\Core\Lib\Settings;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServerFunction
{
    private $settings;
    private $doctrine;
    private $dispatcher;

    public function __construct(Settings $settings, EntityManagerInterface $doctrine, EventDispatcherInterface $dispatcher)
    {
        $this->settings   = $settings;
        $this->doctrine   = $doctrine;
        $this->dispatcher = $dispatcher;
    }

    public function isTheServerFull()
    {
        if (abs($this->settings->getSetting('OnlineCountLast', 0) - strtotime('now')) > 60)
        {
            /** @var \Lotgd\Core\Repository\UserRepository $repository */
            $repository = $this->doctrine->getRepository('LotgdCore:User');
            $counter    = $repository->count(['locked' => 0, 'loggedin' => 1]);

            $this->settings->saveSetting('OnlineCount', $counter);
            $this->settings->saveSetting('OnlineCountLast', strtotime('now'));
        }

        $onlinecount = (int) $this->settings->getSetting('OnlineCount', 0);

        return $onlinecount >= $this->settings->getSetting('maxonline', 0) && 0 != $this->settings->getSetting('maxonline', 0);
    }

    public function resetAllDragonkillPoints($acctid = false)
    {
        global $session;

        /** @var \Lotgd\Core\Repository\AvatarRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Avatar');
        $query      = $repository->createQueryBuilder('u');

        $query->where("u.dragonpoints <> ''");

        if (is_numeric($acctid) && 0 != $acctid)
        {
            $query->andWhere('u.acct = :acct')
                ->setParameter('acct', $acctid)
            ;
        }
        elseif (\is_array($acctid) && ! empty($acctid))
        {
            $query->andWhere('u.acct IN (:acct)')
                ->setParameter('acct', $acctid)
            ;
        }

        $result = $query->getQuery()->getResult();

        //this is ugly, but fortunately only needed out of the ordinary
        foreach ($result as $entity)
        {
            $dkpoints = $entity->getDragonpoints();

            if (empty($dkpoints))
            {
                continue;
            } //-- Not do nothing if is an empty array

            $distribution = array_count_values($dkpoints);

            $entity->setDragonpoints([]);

            $entity->setTurns($entity->getTurns() - ($distribution['ff'] ?? 0));
            $entity->setStrength($entity->getStrength() - ($distribution['str'] ?? 0));
            $entity->setConstitution($entity->getConstitution() - ($distribution['con'] ?? 0));
            $entity->setIntelligence($entity->getIntelligence() - ($distribution['int'] ?? 0));
            $entity->setWisdom($entity->getWisdom() - ($distribution['wis'] ?? 0));
            $entity->setDexterity($entity->getDexterity() - ($distribution['dex'] ?? 0));

            $this->doctrine->persist($entity);

            if ($session['user']['acctid'] == $entity->getAcct()->getAcctid())
            {
                /** @var \Lotgd\Core\Repository\UserRepository $repository */
                $repository      = $this->doctrine->getRepository('LotgdCore:User');
                $session['user'] = $repository->getUserById($entity->getAcct()->getAcctid());
            }
        }

        $this->doctrine->flush();

        //adding a hook, nasty, but you don't call this too often
        $args = new Other([$result]);
        $this->dispatcher->dispatch($args, Other::SERVER_DRAGON_POINT_RESET);
    }
}
