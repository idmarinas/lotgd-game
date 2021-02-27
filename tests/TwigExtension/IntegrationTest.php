<?php

/**
 * This file is part of Bundle "IDM Advertising Bundle".
 *
 * @see https://github.com/idmarinas/advertising-bundle
 *
 * @license https://github.com/idmarinas/advertising-bundle/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 0.1.0
 */

namespace Lotgd\Bundle\Tests\TwigExtension;

use Lotgd\Bundle\CoreBundle\Twig\Extension\GameCoreExtension;
use Twig\Test\IntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test Twig Extensions
 */
class IntegrationTest extends IntegrationTestCase
{
    public function getExtensions()
    {
        $client = (new Kernel())->returnClient();
        $container = $client->getContainer();

        return [
            new GameCoreExtension(
                $container->get('request_stack'),
                $container->get('lotgd_core.censor'))
        ];
    }

    public function getFixturesDir()
    {
        return __DIR__.'/Fixtures/';
    }
}

class Kernel extends WebTestCase
{
    public function returnClient()
    {
        self::ensureKernelShutdown();
        return self::createClient();
    }
}
