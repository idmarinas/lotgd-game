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

namespace Lotgd\Core\Tests;

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

        $message = '';
        if( ! $response->isSuccessful())
        {
            $message = sprintf('Error code %s, %s', $response->getStatusCode(), (string) $response->headers);
        }

        $this->assertTrue($response->isSuccessful(), $message);
    }

    /**
     * @dataProvider provideNotFoundUrls
     */
    public function testPageIsNotFound($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();

        $message = '';
        if( ! $response->isSuccessful())
        {
            $message = sprintf('Error code %s, Exception: "%s", File "%s"',
                $response->getStatusCode(),
                $response->headers->get('X-Debug-Exception'),
                $response->headers->get('X-Debug-Exception-File'),
            );
        }

        $this->assertTrue($response->isNotFound(), $message);
    }

    public function provideValidUrls()
    {
        return [
            ['/'],
            ['/about'],
            ['/about/license'],
            ['/about/bundles'],
            ['/register'],
            ['/reset-password'],
            // ...
        ];
    }

    public function provideNotFoundUrls()
    {
        return [
            ['/home']
        ];
    }
}
