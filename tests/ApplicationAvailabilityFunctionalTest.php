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

namespace Lotgd\Bundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider provideValidUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful(), $this->messageError($response, $response->isSuccessful()));
    }

    /**
     * @dataProvider provideNotFoundUrls
     */
    public function testPageIsNotFound($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();

        $this->assertTrue($response->isNotFound(), $this->messageError($response, $response->isNotFound()));
    }

    /**
     * @dataProvider provideRedirectionUrls
     */
    public function testRedirectionRoute($url, $redirected)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection($redirected), $this->messageError($response, $response->isRedirection($redirected)));
    }

    public function provideValidUrls()
    {
        yield ['/'];
        yield ['/game/login'];
        yield ['/game/about'];
        yield ['/game/about/bundles'];
        yield ['/game/about/game/setup'];
        yield ['/game/about/license'];
        yield ['/game/register'];
        yield ['/game/reset-password'];
        yield ['/game/logdnet/net'];
        yield ['/game/logdnet/list'];
        yield ['/_grotto/login'];
    }

    public function provideNotFoundUrls()
    {
        yield ['/home'];
    }

    public function provideRedirectionUrls()
    {
        yield ['/game/reset-password/check-email', '/game/reset-password'];
        yield ['/about.php', '/game/about'];
        yield ['/about.php?op=license', '/game/about/license'];
        yield ['/create.php', '/game/register'];
        yield ['/create.php?op=forgot', '/game/reset-password'];
        yield ['/play/profile', '/'];
    }

    private function messageError($response, $isSuccessful): string
    {
        $message = '';

        if( ! $isSuccessful)
        {
            $message = sprintf(" Error code %s\n Exception: '%s'\n File '%s'",
                $response->getStatusCode(),
                urldecode($response->headers->get('X-Debug-Exception', '')),
                urldecode($response->headers->get('X-Debug-Exception-File', '')),
            );
        }
        return $message;
    }
}
