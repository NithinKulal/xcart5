<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    // {{{ Copy fields from 'inventory' to 'products' table

    $tablePrefix = \XLite::getInstance()->getOptions(array('database_details', 'table_prefix'));

    $tableNameProducts = $tablePrefix . 'products';
    $tableNameInventory = $tablePrefix . 'inventory';

    $tableColumnsProducts = \XLite\Core\Database::getEM()->getConnection()->getSchemaManager()->listTableColumns($tableNameProducts);

    // Check if table `products` contains new columns
    $found = false;
    $field = '';

    $newColumns = array('inventoryenabled', 'amount', 'lowlimitenabledcustomer', 'lowlimitenabled', 'lowlimitamount');
    foreach (array_keys($tableColumnsProducts) as $column) {
        if (in_array(strtolower($column), $newColumns)) {
            $field = $column;
            $found = true;
            break;
        }
    }

    if (!$found) {
        // New columns not found, alter this table...
        $queries[] =<<<SQL
ALTER TABLE `$tableNameProducts`
  ADD COLUMN `inventoryEnabled` tinyint(1) NOT NULL AFTER `needProcess`,
  ADD COLUMN `amount` int(10) unsigned NOT NULL AFTER `inventoryEnabled`,
  ADD COLUMN `lowLimitEnabledCustomer` tinyint(1) NOT NULL AFTER `amount`,
  ADD COLUMN `lowLimitEnabled` tinyint(1) NOT NULL AFTER `lowLimitEnabledCustomer`,
  ADD COLUMN `lowLimitAmount` int(10) unsigned NOT NULL AFTER `lowLimitEnabled`;
SQL;
        $queries[] =<<<SQL
UPDATE `$tableNameProducts` p, `$tableNameInventory` i
  SET p.`inventoryEnabled` = i.`enabled`,
      p.`amount` = i.`amount`,
      p.`lowLimitEnabledCustomer` = i.`lowLimitEnabledCustomer`,
      p.`lowLimitEnabled` = i.`lowLimitEnabled`,
      p.`lowLimitAmount` = i.`lowLimitAmount`
  WHERE p.`product_id` = i.`id`;
SQL;
        \XLite\Core\Database::getInstance()->executeQueries($queries);

    } else {
        \XLite\Upgrade\Logger::getInstance()->logError('Inventory fields cannot be copied to the products table as this table already contain field `' . $field . '` [upgrade hook ' . __FILE__ . ']');
    }

    // }}}
};
