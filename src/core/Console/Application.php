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

namespace Lotgd\Core\Console;

use Lotgd\Core\Kernel;
use Lotgd\Core\ServiceManager;
use Symfony\Component\Console\Application as ConsoleApplication;

final class Application extends ConsoleApplication
{
    protected $serviceManager;
    protected $appKernel;

    public function __construct(ServiceManager $sm, Kernel $kernel)
    {
        $this->serviceManager = $sm;
        $this->appKernel = $kernel;

        parent::__construct('LoTGD', Kernel::VERSION);

        $this->registerCommands();
    }

    /**
     * Get Service Manager associated with this console.
     */
    public function getServiceManager(): ServiceManager
    {
        return $this->serviceManager;
    }

    /**
     * Get App kernel.
     */
    public function getKernel(): Kernel
    {
        return $this->appKernel;
    }

    protected function registerCommands()
    {
        $config   = $this->serviceManager->get('GameConfig');
        $commands = $config['console']['commands'];

        foreach ($commands as $command)
        {
            $this->add(new $command());
        }
    }
}
