<?php

namespace Lotgd\Core\Doctrine\Strategy;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\QuoteStrategy;

/**
 * A set of rules for determining the physical column, alias and table quotes.
 */
class Quote implements QuoteStrategy
{
    /**
     * {@inheritdoc}
     */
    public function getColumnName($fieldName, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $platform->quoteIdentifier($class->fieldMappings[$fieldName]['columnName']);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName(ClassMetadata $class, AbstractPlatform $platform)
    {
        return $platform->quoteIdentifier($class->table['name']);
    }

    /**
     * {@inheritdoc}
     */
    public function getSequenceName(array $definition, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $platform->quoteIdentifier($definition['sequenceName']);
    }

    /**
     * {@inheritdoc}
     */
    public function getJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $platform->quoteIdentifier($joinColumn['name']);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferencedJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $platform->quoteIdentifier($joinColumn['referencedColumnName']);
    }

    /**
     * {@inheritdoc}
     */
    public function getJoinTableName(array $association, ClassMetadata $class, AbstractPlatform $platform)
    {
        return $platform->quoteIdentifier($association['joinTable']['name']);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierColumnNames(ClassMetadata $class, AbstractPlatform $platform)
    {
        $quotedColumnNames = [];

        foreach ($class->identifier as $fieldName)
        {
            if (isset($class->fieldMappings[$fieldName]))
            {
                $quotedColumnNames[] = $this->getColumnName($fieldName, $class, $platform);

                continue;
            }
            // Association defined as Id field
            $joinColumns            = $class->associationMappings[$fieldName]['joinColumns'];
            $assocQuotedColumnNames = \array_map(
                fn($joinColumn) => $platform->quoteIdentifier($joinColumn['name']),
                $joinColumns
            );
            $quotedColumnNames = \array_merge($quotedColumnNames, $assocQuotedColumnNames);
        }

        return $quotedColumnNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnAlias($columnName, $counter, AbstractPlatform $platform, ?ClassMetadata $class = null)
    {
        // 1 ) Concatenate column name and counter
        // 2 ) Trim the column alias to the maximum identifier length of the platform.
        //     If the alias is to long, characters are cut off from the beginning.
        // 3 ) Strip non alphanumeric characters
        // 4 ) Prefix with "_" if the result its numeric
        $columnName .= $counter;
        $columnName = \substr($columnName, -$platform->getMaxIdentifierLength());
        $columnName = \preg_replace('/[^A-Za-z0-9_]/', '', $columnName);
        $columnName = \is_numeric($columnName) ? '_'.$columnName : $columnName;

        return $platform->getSQLResultCasing($columnName);
    }
}
