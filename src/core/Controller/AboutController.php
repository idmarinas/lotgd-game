<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Bundle\Contract\LotgdBundleInterface;
use Lotgd\Core\Events;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Tool\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AboutController extends AbstractController
{
    private $dispatcher;
    private $settings;
    private $cache;
    private $dateTime;
    private $bundles = [];

    public function __construct(EventDispatcherInterface $eventDispatcher, Settings $settings, CacheInterface $coreLotgdCache, DateTime $dateTime)
    {
        $this->dispatcher = $eventDispatcher;
        $this->settings   = $settings;
        $this->cache      = $coreLotgdCache;
        $this->dateTime   = $dateTime;
    }

    public function setup()
    {
        $params['block_tpl'] = 'about_setup';

        $data = $this->cache->get('lotgd_core.about.game_setup', function (ItemInterface $item)
        {
            $item->expiresAfter(43200); //-- Expire after 12 hours

            $details = $this->dateTime->gameTimeDetails();
            $secstonextday = $this->dateTime->secondsToNextGameDay($details);
            $useful_vals = [
                'dayduration'   => \round(($details['dayduration'] / 60 / 60), 0).' hours',
                'curgametime'   => $this->dateTime->getGameTime(),
                'curservertime' => \date('Y-m-d h:i:s a'),
                'lastnewday'    => \date('h:i:s a', \strtotime("-{$details['realsecssofartoday']} seconds")),
                'nextnewday'    => \date('h:i:s a', \strtotime("+{$details['realsecstotomorrow']} seconds")).' ('.\date('H\\h i\\m s\\s', $secstonextday).')',
            ];

            $localsettings = $this->settings->getArray();

            $vals = \array_merge($localsettings, $useful_vals);

            return [
                'game_setup' => $vals,
                'newday'     => $vals,
                'bank'       => $vals,
                'forest'     => $vals,
                'mail'       => $vals,
                'content'    => $vals,
                'info'       => $vals,
            ];
        });

        $form = $this->createForm(\Lotgd\Core\Form\AboutType::class, $data, [
            'action' => 'none',
            'method' => 'none',
        ]);

        $params['form'] = $form->createView();

        return $this->renderAbout($params);
    }

    public function index(): Response
    {
        $params = [
            'block_tpl' => 'about_home',
        ];

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_ABOUT);
        $results = modulehook('about', $args->getArguments());

        if (\is_array($results) && \count($results))
        {
            $params['hookAbout'] = $results;
        }

        return $this->renderAbout($params);
    }

    public function modules()
    {
        $params = [
            'block_tpl' => 'about_modules',
            'result'    => $this->getDoctrine()->getRepository('LotgdCore:Modules')->findBy(['active' => 1], ['category' => 'ASC', 'formalname' => 'ASC']),
        ];

        return $this->renderAbout($params);
    }

    public function bundles(): Response
    {
        return $this->renderAbout([
            'block_tpl'             => 'about_bundles',
            'bundles_total_enabled' => \is_countable($this->bundles) ? \count($this->bundles) : 0,
            'bundles_lotgd_enabled' => \array_filter($this->bundles, function ($var)
            {
                return $var instanceof LotgdBundleInterface;
            }),
        ]);
    }

    public function license()
    {
        $params = ['block_tpl' => 'about_license'];

        return $this->renderAbout($params);
    }

    public function setBundles(array $bundles): void
    {
        $this->bundles = $bundles;
    }

    private function renderAbout(array $params): Response
    {
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_ABOUT_POST);
        $params = modulehook('page-about-tpl-params', $args->getArguments());

        return $this->render('admin/page/about.html.twig', $params);
    }
}
