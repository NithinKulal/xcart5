<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\Database\Migration;

/**
 * UnsupportedDatabaseOperationException is raised when the DB operation (performed in the storefront) has failed because of the incompatible changes in the ongoing database schema migration executed as part of the rebuild process.
 */
class UnsupportedDatabaseOperationDuringMaintenanceException extends \RuntimeException
{
}