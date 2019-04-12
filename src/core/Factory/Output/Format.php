<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Output;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Output\Format as OutputFormat;
use Zend\ServiceManager\{
    FactoryInterface,
    ServiceLocatorInterface
};
use Tracy\Debugger;

class Format implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $settings = $container->get(\Lotgd\Core\Lib\Settings::class);
        $format = new OutputFormat();

        try
        {
            $format->setDecPoint($settings->getSetting('moneydecimalpoint', '.'));
            $format->setThousandsSep($settings->getSetting('moneythousandssep', ','));
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $format->setDecPoint('.');
            $format->setThousandsSep(',');

            return $format;
        }

        return $format;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
