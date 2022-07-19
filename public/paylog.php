<?php

// mail ready
// addnews ready
// translator ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

check_su_access(SU_EDIT_PAYLOG);
/*
+-----------+---------------------+------+-----+---------+----------------+
| Field     | Type                | Null | Key | Default | Extra          |
+-----------+---------------------+------+-----+---------+----------------+
| payid     | int(11)             |      | PRI | NULL    | auto_increment |
| info      | text                |      |     |         |                |
| response  | text                |      |     |         |                |
| txnid     | varchar(32)         |      | MUL |         |                |
| amount    | float(9,2)          |      |     | 0.00    |                |
| name      | varchar(50)         |      |     |         |                |
| acctid    | int(11) unsigned    |      |     | 0       |                |
| processed | tinyint(4) unsigned |      |     | 0       |                |
| filed     | tinyint(4) unsigned |      |     | 0       |                |
| txfee     | float(9,2)          |      |     | 0.00    |                |
+-----------+---------------------+------+-----+---------+----------------+
*/

$textDomain = 'grotto_paylog';

$month = (int) LotgdRequest::getQuery('month');

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$repository = Doctrine::getRepository('LotgdCore:Paylog');

$params = [
    'textDomain' => $textDomain,
];

LotgdNavigation::superuserGrottoNav();
LotgdNavigation::addNav('paylog.nav.refresh', 'paylog.php');

$args = new GenericEvent();
LotgdEventDispatcher::dispatch($args, Events::PAGE_PAYLOG);

$repository->updateProcessDate();
$months = $repository->getMonths();

LotgdNavigation::addHeader('paylog.category.months');
foreach ($months as $val)
{
    LotgdNavigation::addNav('paylog.nav.month', "paylog.php?month={$val['month']}", [
        'params' => [
            'profit' => $val['profit'],
            'symbol' => LotgdSetting::getSetting('paypalcurrency', 'USD'),
            'date'   => $val['date'],
        ],
    ]);
}

$params['paylog'] = $repository->getList($month);

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/paylog.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
