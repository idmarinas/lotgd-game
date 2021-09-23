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

use Laminas\Filter;
use Lotgd\Core\Entity\Logdnet;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Output\Censor;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LogdnetController extends AbstractController
{
    private $client;
    private $cache;
    private $sanitize;
    private $validator;
    private $settings;
    private $censor;
    private $session;

    public function __construct(
        HttpClientInterface $client,
        CacheInterface $coreLotgdCache,
        Sanitize $sanitize,
        ValidatorInterface $validator,
        Settings $settings,
        Censor $censor,
        SessionInterface $session
    ) {
        $this->client    = $client;
        $this->cache     = $coreLotgdCache;
        $this->sanitize  = $sanitize;
        $this->validator = $validator;
        $this->settings  = $settings;
        $this->censor    = $censor;
        $this->session = $session;
    }

    /**
     * Register server in central_server and show image.
     */
    public function image(Request $request): Response
    {
        global $session;

        //-- Default response
        $file     = 'public/images/paypal1.gif';
        $response = new BinaryFileResponse($file);

        $op      = $request->get('op', '');
        $logdnet = $this->session->get('logdnet', []);

        if ('register' == $op)
        {
            $info = [];

            if (empty($logdnet) || ! isset($logdnet['']) || '' == $logdnet[''])
            {
                $serverCentral = \preg_replace('/\/$/', '', $this->settings->getSetting('logdnetserver', 'http://lotgd.net/'));

                $url = "{$serverCentral}/logdnet.php";

                try
                {
                    $res = $this->client->request('GET', $url, [
                        'query' => [
                            'addy'    => \rawurlencode($request->get('a', '')), //server URL
                            'desc'    => \rawurlencode($request->get('d', '')), //server description
                            'version' => \rawurlencode($request->get('v', '')), //game version
                            'admin'   => \rawurlencode($request->get('e', '')), //admin email
                            'c'       => \rawurlencode($request->get('c', '')), // player count (for my own records, this isn't used in the sorting mechanism)
                            'l'       => \rawurlencode($request->getLocale()), // primary language of this server -- you may change this if it turns out to be inaccurate.
                            'v'       => 2,   // LoGDnet version.
                        ],
                    ]);
                    $result = \unserialize(\base64_decode($res->getContent()));

                    $info         = $result;
                    $info['when'] = \date('Y-m-d H:i:s');
                    $info['note'] = "\n<!-- registered with logdnet successfully -->";
                    $info['note'] .= "\n<!-- {$url} -->";
                }
                catch (\Throwable $th)
                {
                    $info['when'] = \date('Y-m-d H:i:s');
                    $info['note'] = "\n<!-- There was trouble registering on logdnet. -->";
                    $info['note'] .= "\n<!-- {$url} -->";
                }
            }

            $this->session->set('logdnet', $info);

            if (isset($result) && ($session['user']['loggedin'] ?? false))
            {
                $logdnet = $this->session->get('logdnet', []);
                $refer   = $request->server->get('HTTP_REFERER', '');
                $content = $logdnet['note']."\n";
                $content .= "<!-- At {$logdnet['when']} -->\n";
                $content .= \sprintf($info[''],
                    $session['user']['login'],
                    \htmlentities($session['user']['login']).':'.$request->server->get('HTTP_HOST', '').$refer, ENT_COMPAT, 'UTF-8');

                return new Response($content, 200, [
                    'Content-Type' => 'image/gif',
                ]);
            }
        }
        elseif ( ! empty($logdnet))
        {
            return new Response($logdnet['image'], 200, [
                'Content-Type'   => $logdnet['content-type'],
                'Content-Length' => \strlen($logdnet['image']),
            ]);
        }

        return $response;
    }

    /**
     * List of server.
     */
    public function net(): JsonResponse
    {
        /** @var \Lotgd\Core\Repository\LogdnetRepository $repository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:Logdnet');
        $entities   = $repository->getNetServerList();

        return $this->json($entities);
    }

    /**
     * Register server on our list of servers.
     */
    public function register(Request $request)
    {
        /** @var Lotgd\Bundle\CoreBundle\Repository\LogdnetRepository $repository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:Logdnet');
        //-- Get server from DB if exists
        $entity = $repository->findOneBy(['address' => $request->get('addy', '')]);
        $newRow = ( ! $entity);
        /** @var Logdnet $entity */
        $entity = $entity ?: new Logdnet();

        $vers  = (string) $request->get('version', 'Unknown');
        $desc  = (string) $request->get('desc', '');
        $admin = (string) $request->get('admin', 'unknown');

        if ('' == $admin || 'postmaster@localhost.com' == $admin)
        {
            $admin = 'unknown';
        }

        // Clean up the desc
        $desc = $this->sanitize->logdnetSanitize($desc ?: '');
        $desc = $this->censor->filter($desc);

        //-- Set data
        $entity->setAddress((string) $request->get('addy', ''))
            ->setDescription($desc)
            ->setVersion($vers ?: 'Unknown')
            ->setAdmin($admin)
            ->setCount((int) ($request->get('c', 0) * 1))
            ->setLang((string) $request->get('l', ''))
            ->setLastping(new \DateTime('now'))
            ->setLastupdate(new \DateTime('now'))
            ->setRecentips($request->server->get('REMOTE_ADDR'))
        ;

        $dateUpdate = new \DateTime('now');
        $dateUpdate->sub(new \DateInterval('PT1M'));

        // Only one update per minute allowed.
        if ( ! $newRow && $entity->getLastping() < $dateUpdate)
        {
            $entity->setPriority($entity->getPriority() + 1);
        }

        $this->getDoctrine()->getManager()->persist($entity);
        $this->getDoctrine()->getManager()->flush();

        //-- Deleted older server
        $repository->deletedOlderServer();

        //-- Degrade the popularity of any server which hasn't been updated in the past X minutes by 1%.
        $repository->degradePopularity();

        //Now, if we're using version 2 of LoGDnet, we'll return the appropriate code.
        $v = (int) $request->get('v', 0);

        if ($v >= 2)
        {
            $currency = $this->settings->getSetting('paypalcurrency', 'USD');
            $info     = [];
            $info[''] = '<!--data from '.$request->server->get('HTTP_HOST', '').'-->
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="logd@mightye.org">
            <input type="hidden" name="item_name" value="Legend of the Green Dragon Author Donation from %s">
            <input type="hidden" name="item_number" value="%s">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="notify_url" value="http://lotgd.net/payment.php">
            <input type="hidden" name="cn" value="Your Character Name">
            <input type="hidden" name="cs" value="1">
            <input type="hidden" name="currency_code" value="'.$currency.'">
            <input type="hidden" name="tax" value="0">
            <input type="image" src="images/logdnet.php" border="0" width="62" height="57" name="submit" alt="Donate!">
            </form>';
            $info['image']        = \implode('', \file('images/paypal1.gif'));
            $info['content-type'] = 'image/gif';

            return new Response(\base64_encode(\serialize($info)));
        }

        return new Response();
    }

    /**
     * Show list of server from central_server.
     */
    public function list()
    {
        $serverCentral = \preg_replace('/\/$/', '', $this->settings->getSetting('logdnetserver', 'http://lotgd.net/'));

        $servers = $this->cache->get('lotgd_bundle.logdnet.central_server.net', function (ItemInterface $item) use ($serverCentral)
        {
            $item->expiresAt(new \DateTime('tomorrow')); //-- Cache 1 day for not sature central_server

            try
            {
                $response = $this->client->request('GET', "{$serverCentral}/logdnet.php", [
                    'query' => [
                        'op' => 'net',
                    ],
                ]);

                $content = $response->getContent();
                $content = 'application/json' == $response->getHeaders()['content-type'][0] ? \json_decode($content, null, 512, JSON_THROW_ON_ERROR) : \explode("\n", $content);

                $filterChain = new Filter\FilterChain();
                $filterChain
                    ->attach(new Filter\StringTrim())
                    ->attach(new Filter\StripTags())
                    ->attach(new Filter\StripNewlines())
                ;

                foreach ($content as $key => &$server)
                {
                    $server = \unserialize($server);
                    //-- Delete server with invalid uri
                    if ( ! \is_array($server) || \count($this->validator->validate((string) $server['address'], [new Assert\Url(), new Assert\NotBlank()])))
                    {
                        unset($content[$key]);

                        continue;
                    }

                    //-- Fixed error with characters
                    $server['description'] = \iconv(\mb_detect_encoding($server['description'], 'auto'), 'UTF-8//IGNORE', $server['description']);
                    //-- Filter description
                    $server['description'] = $filterChain->filter(\stripslashes($server['description']));
                    //-- Sanitize LoTGD color pattern
                    $server['description'] = $this->sanitize->logdnetSanitize($server['description'] ?: '');
                }

                return $content;
            }
            catch (\Throwable $th)
            {
                return [];
            }
        });

        return $this->render('page/logdnet.html.twig', [
            'servers' => $servers,
        ]);
    }
}
