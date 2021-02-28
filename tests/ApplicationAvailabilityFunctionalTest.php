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

        $this->assertTrue($response->isSuccessful(), $this->messageError($response));
    }

    /**
     * @dataProvider provideNotFoundUrls
     */
    public function testPageIsNotFound($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();

        $this->assertTrue($response->isNotFound(), $this->messageError($response));
    }

    /**
     * @dataProvider privideRedirectionUrls
     */
    public function testRedirectionRoute($url, $redirected)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect($redirected), $this->messageError($response));
    }

    public function provideValidUrls()
    {
        yield ['/'];
        yield ['/about'];
        yield ['/about/bundles'];
        yield ['/about/game/setup'];
        yield ['/about/license'];
        yield ['/register'];
        yield ['/reset-password'];
    }

    public function provideNotFoundUrls()
    {
        yield ['/home'];
    }

    public function privideRedirectionUrls()
    {
        yield ['/reset-password/check-email', '/reset-password'];
    }

    private function messageError($response): string
    {
        $message = '';

        if( ! $response->isSuccessful())
        {
            $message = sprintf("Error code %s\n Exception: '%s'\n File '%s",
                $response->getStatusCode(),
                urldecode($response->headers->get('X-Debug-Exception', '')),
                urldecode($response->headers->get('X-Debug-Exception-File', '')),
            );
        }
        return $message;
    }
}