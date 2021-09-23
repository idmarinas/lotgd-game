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
        $this->settings = $settings;
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;
    }

    public function isTheServerFull()
    {
        if (\abs($this->settings->getSetting('OnlineCountLast', 0) - \strtotime('now')) > 60)
        {
            /** @var \Lotgd\Core\Repository\UserRepository $repository */
            $repository = $this->doctrine->getRepository('LotgdCore:User');
            $counter    = $repository->count(['locked' => 0, 'loggedin' => 1]);

            $this->settings->saveSetting('OnlineCount', $counter);
            $this->settings->saveSetting('OnlineCountLast', \strtotime('now'));
        }

        $onlinecount = (int) $this->settings->getSetting('OnlineCount', 0);

        return (bool) ($onlinecount >= $this->settings->getSetting('maxonline', 0) && 0 != $this->settings->getSetting('maxonline', 0));
    }

    public function resetAllDragonkillPoints($acctid = false)
    {
        global $session;

        /** @var \Lotgd\Core\Repository\AvatarRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Avatar');
        $query      = $repository->createQueryBuilder('u');

        $query->where("u.dragonpoints <> ''");

        if (\is_numeric($acctid) && 0 != $acctid)
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

            $distribution = \array_count_values($dkpoints);

            $entity->setDragonpoints([]);

            isset($distribution['ff']) && $entity->setTurns($entity->getTurns() - (int) $distribution['ff']);
            isset($distribution['str']) && $entity->setStrength($entity->getStrength() - (int) $distribution['str']);
            isset($distribution['con']) && $entity->setConstitution($entity->getConstitution() - (int) $distribution['con']);
            isset($distribution['int']) && $entity->setIntelligence($entity->getIntelligence() - (int) $distribution['int']);
            isset($distribution['wis']) && $entity->setWisdom($entity->getWisdom() - (int) $distribution['wis']);
            isset($distribution['dex']) && $entity->setDexterity($entity->getDexterity() - (int) $distribution['dex']);

            $this->doctrine->persist($entity);

            if ($session['user']['acctid'] == $entity->getAcct()->getAcctid())
            {
                /** @var \Lotgd\Core\Repository\UserRepository */
                $repository = $this->doctrine->getRepository('LotgdCore:User');
                $session['user'] = $repository->getUserById($entity->getAcct()->getAcctid());
            }
        }

        $this->doctrine->flush();

        //adding a hook, nasty, but you don't call this too often
        $args = new Other([$result]);
        $this->dispatcher->dispatch($args, Other::SERVER_DRAGON_POINT_RESET);
        modulehook('dragonpointreset', $args->getData());
    }
}
