# Command for LoTGD Console

Can add your own commands to LoTGD Console. Only need add files here and them include in config.

> Create command:
```php
//-- scrc/local/Command/YourCommand.php

namespace Lotgd\Local\Command;

use Lotgd\Core\ServiceManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to display information about the current application.
 */
final class YourCommand extends Command
{
    use FormatTrait;

    protected static $defaultName = 'name';

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ServiceManager $sm */
        $sm     = $this->getApplication()->getServiceManager();
        
        //-- Your code

        //-- Can see core commands for examples

        return Command::SUCCESS;
    }
}

```

> Include command in config
```php
//-- config/autoload/local/console-lotgd-core.php
return [
    'console' => [
        'commands' => [
            Lotgd\Local\Command\YourCommand::class,
        ]
    ]
];
```
