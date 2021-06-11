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

namespace Lotgd\Bundle\Tests\TwigExtension;

use Lotgd\Core\Http\Request;
use Lotgd\Core\Output\Format;
use Lotgd\Core\Tool\Sanitize;
use Lotgd\Core\Twig\Extension\CensorExtension;
use Lotgd\Core\Twig\Extension\FormatExtension;
use Lotgd\Core\Twig\Extension\GameCore;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Twig\Test\IntegrationTestCase;

/**
 * Test Twig Extensions.
 *
 * @internal
 * @coversNothing
 */
class IntegrationTest extends IntegrationTestCase
{
    public function getExtensions()
    {
        $client    = (new Kernel())->returnClient();
        $container = $client->getContainer();

        return [
            new CensorExtension($container->get('lotgd_core.censor')),
            new FormatExtension($container->get(Format::class)),
            new GameCore(
                $container->get(Request::class),
                $container->get(Sanitize::class),
                $container->get('translator'),
                $container->get('lotgd_core.event_dispatcher'),
                $container->get('lotgd_core.entity_manager'),
                $container->get('session')
            ),
        ];
    }

    public function getFixturesDir()
    {
        return __DIR__.'/Fixtures/';
    }
}

/**
 * @internal
 * @coversNothing
 */
class Kernel extends WebTestCase
{
    public function returnClient()
    {
        self::ensureKernelShutdown();

        return self::createClient();
    }
}
