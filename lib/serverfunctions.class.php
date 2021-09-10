<?php

use Lotgd\Core\Event\Other;

class ServerFunctions
{
    public static function isTheServerFull()
    {
        if (\abs(\LotgdSetting::getSetting('OnlineCountLast', 0) - \strtotime('now')) > 60)
        {
            /** @var \Lotgd\Core\Repository\UserRepository $repository */
            $repository = \Doctrine::getRepository('LotgdCore:User');
            $counter    = $repository->count(['locked' => 0, 'loggedin' => 1]);

            \LotgdSetting::saveSetting('OnlineCount', $counter);
            \LotgdSetting::saveSetting('OnlineCountLast', \strtotime('now'));
        }

        $onlinecount = (int) LotgdSetting::getSetting('OnlineCount', 0);

        return (bool) ($onlinecount >= LotgdSetting::getSetting('maxonline', 0) && 0 != LotgdSetting::getSetting('maxonline', 0));
    }

    public static function resetAllDragonkillPoints($acctid = false)
    {
        /** @var \Lotgd\Core\Repository\AvatarRepository $repository */
        $repository = \Doctrine::getRepository('LotgdCore:Avatar');
        $query      = $repository->createQueryBuilder('u');

        $query->where("u.dragonpoints <> ''");

        if (\is_numeric($acctid))
        {
            $query->andWhere('u.acct = :acct')
                ->setParameter('acct', $acctid)
            ;
        }
        elseif (\is_array($acctid))
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

            $entity->setDragonpoints([])
                ->setStrength($entity->getStrength() - (int) $distribution['str'])
                ->setConstitution($entity->getConstitution() - (int) $distribution['con'])
                ->setIntelligence($entity->getIntelligence() - (int) $distribution['int'])
                ->setWisdom($entity->getWisdom() - (int) $distribution['wis'])
                ->setDexterity($entity->getDexterity() - (int) $distribution['dex'])
            ;

            \Doctrine::persist($entity);
        }

        \Doctrine::flush();

        //adding a hook, nasty, but you don't call this too often
        $args = new Other([$result]);
        \LotgdEventDispatcher::dispatch($args, Other::SERVER_DRAGON_POINT_RESET);
        modulehook('dragonpointreset', $args->getData());
    }
}
