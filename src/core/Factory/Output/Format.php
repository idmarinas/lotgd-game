<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Output;

use Lotgd\Core\Output\Format as OutputFormat;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\{
    FactoryInterface,
    ServiceLocatorInterface
};

class Format implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $doctrine = $container->get(\Lotgd\Core\Db\Doctrine::class);
        $repository = $doctrine->getRepository(\Lotgd\Core\Entity\Settings::class);
        $config = $repository->findBySetting(['moneydecimalpoint', 'moneythousandssep']);

        $format = new OutputFormat();

        if (! empty($config) && is_array($config))
        {
            foreach($config as $setting)
            {
                if ('moneydecimalpoint' == $setting->getSetting())
                {
                    $format->setDecPoint($setting->getValue());
                }
                elseif('moneythousandssep' == $setting->getSetting())
                {
                    $format->setThousandsSep($setting->getValue());
                }
            }
        }

        return $format;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
