<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use XLite\Module\XC\WebmasterKit\Logic\DebugBarSettingsManager;

/**
 * Settings widget (not really a data collector)
 */
class SettingsDataCollector extends DataCollector implements Renderable
{
    public function getName()
    {
        return 'widget_settings';
    }

    public function getWidgets()
    {
        return [
            "settings" => [
                "icon"    => "tasks",
                "widget"  => "PhpDebugBar.XCartWidgets.SettingsWidget",
                "map"     => "widget_settings",
                "default" => "{}",
            ],
        ];
    }

    public function collect()
    {
        $settingsMgr = new DebugBarSettingsManager();

        return [
            'widgetsSqlQueryStacktracesEnabled' => $settingsMgr->areWidgetsSqlQueryStacktracesEnabled(),
            'widgetsTabEnabled'                 => $settingsMgr->areWidgetsTabEnabled(),
            'databaseDetailedModeEnabled'       => $settingsMgr->areDatabaseDetailedModeEnabled(),
        ];
    }
}
