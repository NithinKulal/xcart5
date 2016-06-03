<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Doctrine-based MySQL schema manager
 */
class MySqlSchemaManager extends \Doctrine\DBAL\Schema\MySqlSchemaManager
{
    /**
     * Return a list of all tables in the current database
     *
     * @return array
     */
    public function listTableNames()
    {
        $options = \XLite::getInstance()->getOptions('database_details');

        return preg_grep('/^' . preg_quote($options['table_prefix'], '.+/') . '/Ss', parent::listTableNames());
    }

    /**
     * {@inheritdoc}
     *
     * TODO: remove once https://github.com/doctrine/dbal/pull/881 is merged
     */
    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $column = parent::_getPortableTableColumnDefinition($tableColumn);

        $tableColumn = array_change_key_case($tableColumn, CASE_LOWER);

        if (isset($tableColumn['characterset'])) {
            $column->setPlatformOption('charset', $tableColumn['characterset']);
        }

        return $column;
    }
}
