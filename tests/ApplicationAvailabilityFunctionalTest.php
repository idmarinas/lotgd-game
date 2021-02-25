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
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provideUrls()
    {
        return [
            // ['/'],
            ['/about'],
            ['/about/license'],
            ['/about/bundles'],
            ['/register'],
            ['/reset-password'],
            // ['/contact'],
            // ...
        ];
    }
}
