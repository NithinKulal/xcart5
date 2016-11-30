<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Logic;

/**
 * DebugBar settings model
 */
class DebugBarSettingsManager
{
    const WIDGETS_SQL_QUERY_STACKTRACES_ENABLED = 'widgetsSqlQueryStacktracesEnabled';
    const WIDGETS_TAB_ENABLED                   = 'widgetsTabEnabled';
    const WIDGETS_DETAILED_MODE_ENABLED         = 'databaseDetailedModeEnabled';
    const SQL_QUERY_STACKTRACES_ENABLED         = 'sqlQueryStacktracesEnabled';

    public function areWidgetsSqlQueryStacktracesEnabled()
    {
        return $this->areWidgetsTabEnabled()
            && (bool)$this->getSettings()[self::WIDGETS_SQL_QUERY_STACKTRACES_ENABLED];
    }

    public function setWidgetsSqlQueryStacktracesEnabled($areEnabled)
    {
        $this->updateSetting(self::WIDGETS_SQL_QUERY_STACKTRACES_ENABLED, (bool) $areEnabled);
    }

    public function areSqlQueryStacktracesEnabled()
    {
        return (bool)($this->getSettings()[self::SQL_QUERY_STACKTRACES_ENABLED]);
    }

    public function setSqlQueryStacktracesEnabled($areEnabled)
    {
        $this->updateSetting(self::SQL_QUERY_STACKTRACES_ENABLED, (bool) $areEnabled);
    }

    public function areWidgetsTabEnabled()
    {
        return (bool)$this->getSettings()[self::WIDGETS_TAB_ENABLED];
    }

    public function setWidgetsTabEnabled($areEnabled)
    {
        $this->updateSetting(self::WIDGETS_TAB_ENABLED, (bool) $areEnabled);
    }

    public function areDatabaseDetailedModeEnabled()
    {
        return (bool)$this->getSettings()[self::WIDGETS_DETAILED_MODE_ENABLED];
    }

    public function setDatabaseDetailedModeEnabled($areEnabled)
    {
        $this->updateSetting(self::WIDGETS_DETAILED_MODE_ENABLED, (bool) $areEnabled);
    }

    protected function getSettings()
    {
        $settings = [];

        foreach ($this->getSettingKeys() as $key) {
            $settings[$key] = isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        }

        return $settings;
    }

    protected function updateSettings($settings)
    {
        $request = \XLite\Core\Request::getInstance();

        foreach ($this->getSettingKeys() as $key) {
            $request->setCookie($key, $settings[$key]);
        }
    }

    protected function updateSetting($name, $setting)
    {
        $request = \XLite\Core\Request::getInstance();

        $request->setCookie($name, $setting);
    }

    protected function getSettingKeys()
    {
        return [
            self::WIDGETS_SQL_QUERY_STACKTRACES_ENABLED,
            self::SQL_QUERY_STACKTRACES_ENABLED,
            self::WIDGETS_TAB_ENABLED,
            self::WIDGETS_DETAILED_MODE_ENABLED
        ];
    }
}
