<?php

use Doctrine\DBAL\Tools\Console;
use Doctrine\ORM\Tools\Console\Command;

return [
    'service_manager' => [
        'invokables' => [
            //-- Added in version 4.2.0
            'Doctrine\ORM\Mapping\AnsiQuoteStrategy' => 'Doctrine\ORM\Mapping\AnsiQuoteStrategy',
            'DoctrineModule\Authentication\Storage\Session' => 'Laminas\Authentication\Storage\Session',
            'Doctrine\ORM\Mapping\UnderscoreNamingStrategy' => 'Doctrine\ORM\Mapping\UnderscoreNamingStrategy',
            // DBAL commands
            'doctrine.dbal_cmd.runsql' => Console\Command\RunSqlCommand::class,
            'doctrine.dbal_cmd.import' => Console\Command\ImportCommand::class,
            // ORM Commands
            'doctrine.orm_cmd.clear_cache_metadata' => Command\ClearCache\MetadataCommand::class,
            'doctrine.orm_cmd.clear_cache_result' => Command\ClearCache\ResultCommand::class,
            'doctrine.orm_cmd.clear_cache_query' => Command\ClearCache\QueryCommand::class,
            'doctrine.orm_cmd.schema_tool_create' => Command\SchemaTool\CreateCommand::class,
            'doctrine.orm_cmd.schema_tool_update' => Command\SchemaTool\UpdateCommand::class,
            'doctrine.orm_cmd.schema_tool_drop' => Command\SchemaTool\DropCommand::class,
            'doctrine.orm_cmd.convert_d1_schema' => Command\ConvertDoctrine1SchemaCommand::class,
            'doctrine.orm_cmd.generate_entities' => Command\GenerateEntitiesCommand::class,
            'doctrine.orm_cmd.generate_proxies' => Command\GenerateProxiesCommand::class,
            'doctrine.orm_cmd.convert_mapping' => Command\ConvertMappingCommand::class,
            'doctrine.orm_cmd.run_dql' => Command\RunDqlCommand::class,
            'doctrine.orm_cmd.validate_schema' => Command\ValidateSchemaCommand::class,
            'doctrine.orm_cmd.info' => Command\InfoCommand::class,
            'doctrine.orm_cmd.ensure_production_settings' => Command\EnsureProductionSettingsCommand::class,
            'doctrine.orm_cmd.generate_repositories' => Command\GenerateRepositoriesCommand::class,
        ]
    ]
];
