<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Combat\Buff;
use Lotgd\Core\EntityRepository\MountsRepository;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class StableController extends AbstractController
{
    private $navigation;
    private $dispatcher;
    private $repository;
    private $log;
    private $sanitize;
    private $buffs;

    public function __construct(Navigation $navigation, EventDispatcherInterface $eventDispatcher, Log $log, Sanitize $sanitize, Buff $buffs)
    {
        $this->navigation = $navigation;
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
        $this->sanitize   = $sanitize;
        $this->buffs      = $buffs;
    }

    public function buy(array $params, Request $request): Response
    {
        global $session;

        $mountId       = $request->query->getInt('id');
        $params['tpl'] = 'buymount';

        if ($session['user']['hashorse'])
        {
            $this->navigation->addHeader('category.confirm.trade');
            $this->navigation->addNav('nav.yes', "stables.php?op=confirmbuy&id={$mountId}");
            $this->navigation->addNav('nav.no', 'stables.php');

            $params['confirm'] = 1;
        }
        else
        {
            return $this->buyConfirm($params, $request);
        }

        return $this->renderStable($params);
    }

    public function buyConfirm(array $params, Request $request): Response
    {
        global $session, $playermount;

        $mountId       = $request->query->getInt('id');
        $params['tpl'] = 'confirmbuy';

        $mount = $this->getRepository()->extractEntity($this->getRepository()->find($mountId));

        if ($mount)
        {
            $params['mountBuyed'] = true;

            if (($session['user']['gold'] + $params['repaygold']) < $mount['mountcostgold'] || ($session['user']['gems'] + $params['repaygems']) < $mount['mountcostgems'])
            {
                $params['mountBuyed'] = false;
            }
            else
            {
                $params['mountReplace'] = (bool) ($session['user']['hashorse']);
                $params['mountNameNew'] = $mount['mountname'];

                $debugmount1 = $params['player_mount']['mountname'] ?? '';

                if ($debugmount1)
                {
                    $debugmount1 = 'a '.$debugmount1;
                }
                $session['user']['hashorse'] = $mount['mountid'];
                $debugmount2                 = $mount['mountname'];
                $goldcost                    = $params['repaygold'] - $mount['mountcostgold'];
                $session['user']['gold'] += $goldcost;
                $gemcost = $params['repaygems'] - $mount['mountcostgems'];
                $session['user']['gems'] += $gemcost;

                $this->log->debug(($goldcost <= 0 ? 'spent ' : 'gained ').\abs($goldcost).' gold and '.($gemcost <= 0 ? 'spent ' : 'gained ').\abs($gemcost)." gems trading {$debugmount1} for a new mount, a {$debugmount2}");

                $mount['mountbuff']['schema'] = $mount['mountbuff']['schema'] ?? 'mounts' ?: 'mounts';

                $this->buffs->applyBuff('mount', $mount['mountbuff']);

                // Recalculate so the selling stuff works right
                $params['player_mount'] = getmount($mount['mountid']);
                $params['mountName']    = $mount['mountname'];
                $playermount            = $mount;

                if ( ! empty($mount))
                {
                    $params['repaygold'] = \round($mount['mountcostgold'] * 2 / 3, 0);
                    $params['repaygems'] = \round($mount['mountcostgems'] * 2 / 3, 0);
                    $params['grubprice'] = \round($session['user']['level'] * $mount['mountfeedcost'], 0);
                }

                // Recalculate the special name as well.
                $args = new GenericEvent();
                $this->dispatcher->dispatch($args, Events::PAGE_STABLES_MOUNT);
                modulehook('stable-mount', $args->getArguments());
                $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_STABLES_BOUGHT);
                modulehook('boughtmount');
            }
        }

        return $this->renderStable($params);
    }

    public function sell(array $params): Response
    {
        $this->navigation->addHeader('category.confirm.sell');
        $this->navigation->addNav('nav.yes', 'stables.php?op=confirmsell');
        $this->navigation->addNav('nav.no', 'stables.php');

        $params['confirm'] = 1;

        return $this->renderStable($params);
    }

    public function sellConfirm(array $params): Response
    {
        global $session;

        $session['user']['gold'] += $params['repaygold'];
        $session['user']['gems'] += $params['repaygems'];
        $debugmount = $params['player_mount']['mountname'];
        $this->log->debug("gained {$params['repaygold']} gold and {$params['repaygems']} gems selling their mount, a {$debugmount}");
        $this->buffs->stripBuff('mount');
        $session['user']['hashorse'] = 0;
        $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_STABLES_SOLD);
        modulehook('soldmount');

        $params['repayGold'] = $params['repaygold'];
        $params['repayGems'] = $params['repaygems'];

        $params['mountName'] = ($params['player_mount']['newname'] ? $params['player_mount']['newname'] : $params['player_mount']['mountname']);

        return $this->renderStable($params);
    }

    public function feed(array $params): Response
    {
        global $session;

        $params['allowFeed'] = (int) getsetting('allowfeed', 0);
        $params['haveGold']  = ($session['user']['gold'] >= $params['grubprice']);

        if ($params['haveGold'])
        {
            $mount['mountbuff']['schema'] = $mount['mountbuff']['schema'] ?? 'mounts' ?: 'mounts';
            $params['mountHungry']        = (isset($session['bufflist']['mount']) && $session['bufflist']['mount']['rounds'] == $mount['mountbuff']['rounds']);

            if ($params['mountHungry'])
            {
                $params['halfHungry'] = (isset($session['bufflist']['mount']) && $session['bufflist']['mount']['rounds'] > $mount['mountbuff']['rounds'] * .5);

                if ($params['halfHungry'])
                {
                    $params['grubprice'] = \round($params['grubprice'] / 2, 0);
                }

                $params['grubPrice'] = $params['grubprice'];

                $session['user']['gold'] -= $params['grubprice'];
                $session['user']['fedmount'] = 1;

                $this->log->debug("spent {$params['grubprice']} feeding their mount");

                $this->buffs->applyBuff('mount', $mount['mountbuff']);
            }
        }

        return $this->renderStable($params);
    }

    public function examine(array $params, Request $request): Response
    {
        $mountId = $request->query->getInt('id');

        $params['tpl']   = 'examine';
        $params['mount'] = $this->getRepository()->extractEntity($this->getRepository()->find($mountId));

        if ($params['mount'])
        {
            $this->navigation->addHeader('category.new', ['params' => ['name' => $params['mount']['mountname']]]);
            $this->navigation->addNav('nav.buy', "stables.php?op=buymount&id={$params['mount']['mountid']}");
        }

        return $this->renderStable($params);
    }

    public function index(array $params): Response
    {
        $params['tpl'] = 'default';

        return $this->renderStable($params);
    }

    private function renderStable(array $params): Response
    {
        global $session;

        if (0 == $params['confirm'])
        {
            if ($session['user']['hashorse'] > 0)
            {
                $params['costGold']  = $params['repaygold'];
                $params['costGems']  = $params['repaygems'];
                $params['mountName'] = $params['player_mount']['mountname'];

                $this->navigation->addHeaderNotl($this->sanitize->fullSanitize($params['player_mount']['mountname']));

                $this->navigation->addNav('nav.sell', 'stables.php?op=sellmount', [
                    'params' => [
                        'name' => $params['player_mount']['mountname'],
                    ],
                ]);

                if (getsetting('allowfeed', 0) && 0 == $session['user']['fedmount'])
                {
                    $this->navigation->addNav('nav.feed', 'stables.php?op=feed', [
                        'params' => [
                            'name'      => $params['player_mount']['mountname'],
                            'grubPrice' => $params['grubprice'],
                        ],
                    ]);
                }
            }

            $result = $this->getRepository()->getMountsByLocation($session['user']['location']);

            $category = '';

            foreach ($result as $row)
            {
                if ($category != $row->getMountcategory())
                {
                    $this->navigation->addHeaderNotl($row->getMountcategory());
                    $category = $row->getMountcategory();
                }

                if ($row->getMountdkcost() <= $session['user']['dragonkills'])
                {
                    $this->navigation->addNav('nav.examine', "stables.php?op=examine&id={$row->getMountid()}", [
                        'params' => [
                            'name' => $row->getMountname(),
                        ],
                    ]);
                }
            }
        }

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_STABLES_POST);
        $params = modulehook('page-stables-tpl-params', $args->getArguments());

        return $this->render('page/stables.html.twig', $params);
    }

    private function getRepository(): MountsRepository
    {
        if ( ! $this->repository instanceof MountsRepository)
        {
            $this->repository = $this->getDoctrine()->getRepository('LotgdCore:Mounts');
        }

        return $this->repository;
    }
}
