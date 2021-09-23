<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.1.0
 */

namespace Lotgd\Core\Block;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;

final class DonationButtonsBlock extends AbstractBlockService
{
    protected $request;
    protected $doctrine;
    protected $settings;

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        global $session;

        $uri  = $this->request->getServer('REQUEST_URI');
        $host = $this->request->getServer('HTTP_HOST');
        $now  = new \DateTime('now');
        $now->sub(new \DateInterval('PT1H'));
        $cacheTime = 900;

        $alreadyRegisteredLogdnet = true;

        if (
            'prod' == $this->request->server->get('APP_ENV')
            && ('' == ($session['logdnet'][''] ?? '') || ! isset($session['user']['laston']) || $now > $session['user']['laston'])
        ) {
            $cacheTime                = 0;
            $alreadyRegisteredLogdnet = false;
        }

        $author['register_logdnet'] = false;

        if ($this->settings->getSetting('logdnet', 0) && $session['user']['loggedin'] && ! $alreadyRegisteredLogdnet)
        {
            //account counting, just for my own records, I don't use this in the calculation for server order.
            $c = $this->doctrine->getRepository('LotgdCore:User')->count([]);
            $a = $this->settings->getSetting('serverurl', "//{$host}/");

            if ( ! \preg_match("/\/$/", $a))
            {
                $a .= '/';
                $this->settings->saveSetting('serverurl', $a);
            }

            $l = $this->settings->getSetting('defaultlanguage', 'en');
            $d = $this->settings->getSetting('serverdesc', 'Another LoGD Server');
            $e = $this->settings->getSetting('gameadminemail', 'postmaster@localhost.com');
            $u = $this->settings->getSetting('logdnetserver', 'https://lotgd.net/');

            if ( ! \preg_match("/\/$/", $u))
            {
                $u .= '/';
                $this->settings->saveSetting('logdnetserver', $u);
            }

            $author['register_logdnet'] = true;
            $author['v']                = \rawurlencode(\Lotgd\Core\Kernel::VERSION);
            $author['c']                = \rawurlencode($c);
            $author['a']                = \rawurlencode($a);
            $author['l']                = \rawurlencode($l);
            $author['d']                = \rawurlencode($d);
            $author['e']                = \rawurlencode($e);
            $author['u']                = \rawurlencode($u);
        }

        return $this->renderResponse('admin/paypal.html.twig', [
            'settings'    => $blockContext->getSettings(),
            'block'       => $blockContext->getBlock(),
            'item_number' => \htmlentities($session['user']['login'], ENT_COMPAT, 'UTF-8').':'.$host.'/'.$uri,
            'notify_url'  => '//'.$host.\dirname($uri).'/payment.php',
            'author'      => $author,
        ], $response)->setTtl($cacheTime);
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setDoctrine(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setSettings(Settings $settings)
    {
        $this->settings = $settings;
    }
}
