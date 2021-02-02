<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.6.0
 */

namespace Lotgd\Core;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const VERSION             = '5.1.0 IDMarinas Edition'; // Version of game in public display format.
    public const VERSION_ID          = 50100; // Identify version of game in numeric format.
    public const MAJOR_VERSION       = 5;
    public const MINOR_VERSION       = 1;
    public const RELEASE_VERSION     = 0;
    public const EXTRA_VERSION       = '';
    public const VERSION_NUMBER      = self::VERSION_ID; //-- Alias of VERSION_ID
    public const FILE_DB_CONNECT     = 'config/autoload/local/dbconnect.php'; // The file where the database connection data is stored.
    public const TEXT_DOMAIN_DEFAULT = 'app_default';

    /**
     * This series of scripts (collectively known as Legend of the Green Dragon or LotGD) is copyright as per below.
     * You are prohibited by law from removing or altering this copyright information in any fashion except as follows:
     *      if you have added functionality to the code, you may append your
     *      name at the end indicating which parts are copyright by you.
     *  Eg:
     *  Copyright 2002-2004, Game: Eric Stevens & JT Traub, modified by Your Name.
     *
     * @var string
     */
    public const COPYRIGHT = 'Game Design and Code: Copyright &copy; 2002-2005, Eric Stevens & JT Traub, &copy; 2006-2007, Dragonprime Development Team, &copy; 2015-2019 IDMarinas remodelling and enhancing';

    /**
     * This series of scripts (collectively known as Legend of the Green Dragon or LotGD) is licensed according to the Creating Commons Attribution
     * Non-commercial Share-alike license.  The terms of this license must be followed for you to legally use or distribute this software. This
     * license must be used on the distribution of any works derived from this work. This license text may not be removed nor altered in any way.
     * Please see the file LICENSE for a full textual description of the license.
     *
     * @var string
     */
    public const LICENSE = "\n<!-- Creative Commons License -->\n<a rel='license' href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank' rel='noopener noreferrer'><img clear='right' align='left' alt='Creative Commons License' border='0' src='images/somerights20.gif' /></a>\nThis work is licensed under a <a rel='license' href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank' rel='noopener noreferrer'>Creative Commons License</a>.<br />\n<!-- /Creative Commons License -->\n<!--\n  <rdf:RDF xmlns='http://web.resource.org/cc/' xmlns:dc='http://purl.org/dc/elements/1.1/' xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'>\n	<Work rdf:about=''>\n	  <dc:type rdf:resource='http://purl.org/dc/dcmitype/Interactive' />\n	  <license rdf:resource='http://creativecommons.org/licenses/by-nc-sa/2.0/' />\n	</Work>\n	<License rdf:about='http://creativecommons.org/licenses/by-nc-sa/2.0/'>\n	  <permits rdf:resource='http://web.resource.org/cc/Reproduction' />\n	  <permits rdf:resource='http://web.resource.org/cc/Distribution' />\n	  <requires rdf:resource='http://web.resource.org/cc/Notice' />\n	  <requires rdf:resource='http://web.resource.org/cc/Attribution' />\n	  <prohibits rdf:resource='http://web.resource.org/cc/CommercialUse' />\n	  <permits rdf:resource='http://web.resource.org/cc/DerivativeWorks' />\n	  <requires rdf:resource='http://web.resource.org/cc/ShareAlike' />\n	</License>\n  </rdf:RDF>\n-->\n";

    /**
     * {@inheritdoc}
     */
    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import($this->getProjectDir().'/config/{packages}/*.yaml');
        $container->import($this->getProjectDir().'/config/{packages}/'.$this->environment.'/*.yaml');

        if (\is_file($this->getProjectDir().'/config/services.yaml'))
        {
            $container->import($this->getProjectDir().'/config/services.yaml');
            $container->import($this->getProjectDir().'/config/{services}_'.$this->environment.'.yaml');
        }
        elseif (\is_file($path = $this->getProjectDir().'/config/services.php'))
        {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir().'/config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import($this->getProjectDir().'/config/{routes}/*.yaml');

        if (\is_file($this->getProjectDir().'/config/routes.yaml'))
        {
            $routes->import($this->getProjectDir().'/config/routes.yaml');
        }
        elseif (\is_file($path = $this->getProjectDir().'/config/routes.php'))
        {
            (require $path)($routes->withPath($path), $this);
        }
    }
}
