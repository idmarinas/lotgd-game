<?php

$order = (int) \LotgdHttp::getQuery('order');

$clanRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class);

$params['clanList'] = $clanRepository->getClanListWithMembersCount($order);

\LotgdNavigation::addHeader('category.options');

if (count($params['clanList']))
{
    \LotgdNavigation::addNav('nav.list.lobby', 'clan.php');

    \LotgdNavigation::addHeader('category.sorting');
    \LotgdNavigation::addNav('nav.list.order.count', 'clan.php?op=apply&order=0');
    \LotgdNavigation::addNav('nav.list.order.name', 'clan.php?op=apply&order=1');
    \LotgdNavigation::addNav('nav.list.order.short', 'clan.php?op=apply&order=2');
}
else
{
    \LotgdNavigation::addNav('nav.list.new', 'clan.php?op=new');
    \LotgdNavigation::addNav('nav.list.lobby', 'clan.php');
}
