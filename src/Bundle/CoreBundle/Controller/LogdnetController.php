<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Controller;

use Laminas\Filter;
use Lotgd\Bundle\CoreBundle\Entity\Logdnet;
use Lotgd\Bundle\CoreBundle\Installer\Pattern\Version;
use Lotgd\Bundle\CoreBundle\Tool\Censor;
use Lotgd\Bundle\CoreBundle\Tool\Sanitize;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/logdnet")
 */
class LogdnetController extends AbstractController
{
    public const TRANSLATOR_DOMAIN = 'lotgd_core_page_logdnet';

    /**
     * Register server in central_server and show image.
     *
     * @Route("/image", name="lotgd_core_page_logdnet_image")
     */
    public function image(Request $request, SessionInterface $session, HttpClientInterface $client): Response
    {
        //-- Default response
        $file     = 'bundles/lotgdui/images/paypal1.gif';
        $response = new BinaryFileResponse($file);

        $op      = $request->get('op', '');
        $logdnet = $session->get('logdnet', []);

        if ('register' == $op)
        {
            if (empty($logdnet) || ! isset($logdnet['']) || '' == $logdnet[''])
            {
                $serverCentral = \preg_replace('/\/$/', '', $this->getParameter('lotgd_bundle.logdnet.central_server'));

                $url = "{$serverCentral}/logdnet.php";

                try {
                    $response = $client->request('GET', $url, [
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
                    $result = \unserialize(\base64_decode($response->getContent()));

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

            $session->set('logdnet', $info);

            if (isset($result) && $user = $this->getUser())
            {
                $logdnet = $session->get('logdnet', []);
                $refer   = $request->server->get('HTTP_REFERER', '');
                $content = $logdnet['note']."\n";
                $content .= "<!-- At {$logdnet['when']} -->\n";
                $content .= \sprintf($info[''],
                    $user['username'],
                    \htmlentities($user['username']).':'.$request->server->get('HTTP_HOST', '').$refer, ENT_COMPAT, 'UTF-8');

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
     *
     * @Route("/net", name="lotgd_core_page_logdnet_net")
     */
    public function net()
    {
        /** @var \Lotgd\Bundle\CoreBundle\Repository\LogdnetRepository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:Logdnet');
        $entities   = $repository->getnetServerList();
        \usort($entities, [$this, 'lotgdSort']);

        return $this->json($entities);
    }

    /**
     * Register server on our list of servers.
     *
     * @Route("/register", name="lotgd_core_page_logdnet_register")
     */
    public function register(Censor $censor, Request $request, Sanitize $sanitize)
    {
        /** @var Lotgd\Bundle\CoreBundle\Repository\LogdnetRepository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:Logdnet');
        //-- Get server from DB if exists
        $entity = $repository->findOneBy(['address' => $request->get('addy', '')]);
        $newRow = ( ! $entity);
        /** @var Logdnet */
        $entity = $entity ?: new Logdnet();

        $vers  = (string) $request->get('version', 'Unknown');
        $desc  = (string) $request->get('desc', '');
        $admin = (string) $request->get('admin', 'unknown');

        if ('' == $admin || 'postmaster@localhost.com' == $admin)
        {
            $admin = 'unknown';
        }

        // Clean up the desc
        $desc = $sanitize->logdnet($desc ?: '');
        $desc = $censor->filter($desc);

        //-- Set data
        $entity->setAddress((string) $request->get('addy', ''))
            ->setDescription((string) $desc)
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
            $currency = $this->getParameter('lotgd_bundle.paypal.currency');
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
            $info['image']        = \implode('', \file('bundles/lotgdui/images/paypal1.gif'));
            $info['content-type'] = 'image/gif';

            return new Response(\base64_encode(\serialize($info)));
        }

        return new Response();
    }

    /**
     * Show list of server from central_server.
     *
     * @Route("/list", name="lotgd_core_page_logdnet_list")
     */
    public function list(HttpClientInterface $client, CacheInterface $lotgdBundlePackageCache, Sanitize $sanitize, ValidatorInterface $validator)
    {
        $serverCentral = \preg_replace('/\/$/', '', $this->getParameter('lotgd_bundle.logdnet.central_server'));

        $servers = $lotgdBundlePackageCache->get('lotgd_bundle.logdnet.central_server.net', function (ItemInterface $item) use ($client, $serverCentral, $sanitize, $validator)
        {
            $item->expiresAt(new \DateTime('tomorrow')); //-- Cache 1 day for not sature central_server

            try {
                $response = $client->request('GET', "{$serverCentral}/logdnet.php", [
                    'query' => [
                        'op' => 'net',
                    ],
                ]);

                $content = $response->getContent();
                $content = 'application/json' == $response->getHeaders()['content-type'][0] ? \json_decode($content) : \explode("\n", $content);

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
                    if ( ! \is_array($server) || \count($validator->validate((string) $server['address'], [new Assert\Url(), new Assert\NotBlank()])))
                    {
                        unset($content[$key]);

                        continue;
                    }

                    //-- Fixed error with characters
                    $server['description'] = \iconv(\mb_detect_encoding($server['description'], 'auto'), 'UTF-8//IGNORE', $server['description']);
                    //-- Filter description
                    $server['description'] = $filterChain->filter(\stripslashes($server['description']));
                    //-- Sanitize LoTGD color pattern
                    $server['description'] = $sanitize->logdnet($server['description'] ?: '');
                }

                return $content;
            }
            catch (\Throwable $th)
            {
                return [];
            }
        });

        return $this->render('@LotgdCore/logdnet/list.html.twig', [
            'servers' => $servers,
        ]);
    }

    private function lotgdSort($a, $b)
    {
        $official_prefixes = (new class() {
            use Version;
        })->getFullListOfVersion();

        unset($official_prefixes['Clean Install']);
        $official_prefixes = \array_keys($official_prefixes);

        $aver = \strtolower(\str_replace(' ', '', $a['version']));
        $bver = \strtolower(\str_replace(' ', '', $b['version']));

        // Okay, if $a and $b are the same version, use the priority
        // This is true whether or not they are the official version or not.
        // We bubble the official version to the top below.
        if (0 == \strcmp($aver, $bver))
        {
            if ($a['priority'] == $b['priority'])
            {
                return 0;
            }

            return ($a['priority'] < $b['priority']) ? 1 : -1;
        }

        // Unknown versions are always worse than non-unknown
        if (0 == \strcmp($aver, 'unknown') && 0 != \strcmp($bver, 'unknown'))
        {
            return 1;
        }
        elseif (0 == \strcmp($bver, 'unknown') && 0 != \strcmp($aver, 'unknown'))
        {
            return -1;
        }

        // Check if either of them are a prefix.
        $costa = 10000;
        $costb = 10000;

        foreach ($official_prefixes as $index => $value)
        {
            if (0 == \strncmp($aver, $value, \strlen($value)) && 10000 == $costa)
            {
                $costa = $index;
            }

            if (0 == \strncmp($bver, $value, \strlen($value)) && 10000 == $costb)
            {
                $costb = $index;
            }
        }

        // If both are the same prefix (or no prefix), just strcmp.
        if ($costa == $costb)
        {
            return \strcmp($aver, $bver);
        }

        return ($costa < $costb) ? -1 : 1;
    }
}
