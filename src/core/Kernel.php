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
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const VERSION         = '4.8.0 IDMarinas Edition'; // Version of game in public display format.
    const VERSION_ID      = 40800; // Identify version of game in numeric format.
    const MAJOR_VERSION   = 4;
    const MINOR_VERSION   = 8;
    const RELEASE_VERSION = 0;
    const EXTRA_VERSION   = '';
    const VERSION_NUMBER  = self::VERSION_ID; //-- Alias of VERSION_ID
    const FILE_DB_CONNECT = 'config/autoload/local/dbconnect.php'; // The file where the database connection data is stored.

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
    const COPYRIGHT = 'Game Design and Code: Copyright &copy; 2002-2005, Eric Stevens & JT Traub, &copy; 2006-2007, Dragonprime Development Team, &copy; 2015-2019 IDMarinas remodelling and enhancing';

    /**
     * This series of scripts (collectively known as Legend of the Green Dragon or LotGD) is licensed according to the Creating Commons Attribution
     * Non-commercial Share-alike license.  The terms of this license must be followed for you to legally use or distribute this software. This
     * license must be used on the distribution of any works derived from this work. This license text may not be removed nor altered in any way.
     * Please see the file LICENSE for a full textual description of the license.
     *
     * @var string
     */
    const LICENSE = "\n<!-- Creative Commons License -->\n<a rel='license' href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank' rel='noopener noreferrer'><img clear='right' align='left' alt='Creative Commons License' border='0' src='images/somerights20.gif' /></a>\nThis work is licensed under a <a rel='license' href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank' rel='noopener noreferrer'>Creative Commons License</a>.<br />\n<!-- /Creative Commons License -->\n<!--\n  <rdf:RDF xmlns='http://web.resource.org/cc/' xmlns:dc='http://purl.org/dc/elements/1.1/' xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'>\n	<Work rdf:about=''>\n	  <dc:type rdf:resource='http://purl.org/dc/dcmitype/Interactive' />\n	  <license rdf:resource='http://creativecommons.org/licenses/by-nc-sa/2.0/' />\n	</Work>\n	<License rdf:about='http://creativecommons.org/licenses/by-nc-sa/2.0/'>\n	  <permits rdf:resource='http://web.resource.org/cc/Reproduction' />\n	  <permits rdf:resource='http://web.resource.org/cc/Distribution' />\n	  <requires rdf:resource='http://web.resource.org/cc/Notice' />\n	  <requires rdf:resource='http://web.resource.org/cc/Attribution' />\n	  <prohibits rdf:resource='http://web.resource.org/cc/CommercialUse' />\n	  <permits rdf:resource='http://web.resource.org/cc/DerivativeWorks' />\n	  <requires rdf:resource='http://web.resource.org/cc/ShareAlike' />\n	</License>\n  </rdf:RDF>\n-->\n";

    /**
     * {@inheritdoc}
     */
    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader): void
    {
        $confDir = $this->getProjectDir().'/config';

        $container->import($confDir.'/{packages}/*.yaml');
        $container->import($confDir.'/{packages}/'.$this->environment.'/*.yaml');

        $container->import($confDir.'/services.yaml');
        $container->import($confDir.'/{services}_'.$this->environment.'.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/'.$this->environment.'/*.yaml');
        $routes->import($confDir.'/{routes}/*.yaml');
        $routes->import($confDir.'/routes.yaml');
    }
}
