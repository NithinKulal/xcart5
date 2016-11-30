<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Controller\Customer;

use XLite\Module\XC\WebmasterKit\Logic\DebugBarSettingsManager;

/**
 * DebugBar settings update controller
 */
class DebugBarSettings extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Update settings and reload the page
     *
     * @return void
     */
    protected function doActionUpdateSettings()
    {
        $settingsMgr = new DebugBarSettingsManager();

        $request = \XLite\Core\Request::getInstance();

        $settingsMgr->setWidgetsSqlQueryStacktracesEnabled($request->{DebugBarSettingsManager::WIDGETS_SQL_QUERY_STACKTRACES_ENABLED});
        $settingsMgr->setSqlQueryStacktracesEnabled($request->{DebugBarSettingsManager::SQL_QUERY_STACKTRACES_ENABLED});
        $settingsMgr->setWidgetsTabEnabled($request->{DebugBarSettingsManager::WIDGETS_TAB_ENABLED});
        $settingsMgr->setDatabaseDetailedModeEnabled($request->{DebugBarSettingsManager::WIDGETS_DETAILED_MODE_ENABLED});

        $this->setReturnURL($_SERVER['HTTP_REFERER']);
    }
}
