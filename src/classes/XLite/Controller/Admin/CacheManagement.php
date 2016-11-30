<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Cache management page controller
 */
class CacheManagement extends \XLite\Controller\Admin\Settings
{
    /**
     * Values to use for $page identification
     */
    const CACHE_MANAGEMENT_PAGE = 'CacheManagement';

    /**
     * Page
     *
     * @var string
     */
    public $page = self::CACHE_MANAGEMENT_PAGE;

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return LC_DEVELOPER_MODE
            ? array_merge(parent::defineFreeFormIdActions(), array('rebuild'))
            : parent::defineFreeFormIdActions();
    }

    /**
     * doActionRebuild
     *
     * @return void
     */
    public function doActionRebuild()
    {
        \XLite::setCleanUpCacheFlag(true);

        // To avoid the infinite loop
        $this->setReturnURL($this->buildURL());
    }

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list[static::CACHE_MANAGEMENT_PAGE] = static::t('Cache management');

        return $list;
    }

    /**
     * Get resize
     *
     * @return \XLite\Logic\QuickData\Generator
     */
    public function getQuickDataGenerator()
    {
        return \XLite\Logic\QuickData\Generator::getInstance();
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isQuickDataNotFinished()
    {
        $eventName = \XLite\Logic\QuickData\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                array(\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS)
            )
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getQuickDataCancelFlagVarName());
    }

    /**
     * Perform some actions before redirect
     *
     * FIXME: check. Action should not be an optional param
     *
     * @param string|null $action Performed action OPTIONAL
     *
     * @return void
     */
    protected function actionPostprocess($action = null)
    {
        parent::actionPostprocess($action);

        $this->setReturnURL(
            $this->buildURL('cache_management')
        );
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionQuickData()
    {
        \XLite\Logic\QuickData\Generator::run($this->assembleQuickDataOptions());

        \XLite\Core\Database::getRepo('XLite\Model\Category')->correctCategoriesStructure();
    }
    
    /**
     * Export action
     *
     * @return void
     */
    protected function doActionQuickDataToggle()
    {
        $value = !\XLite\Core\Config::getInstance()->CacheManagement->quick_data_rebuilding;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'CacheManagement',
                'name'     => 'quick_data_rebuilding',
                'value'    => $value,
            )
        );

        \XLite\Core\TopMessage::addInfo(
            $value
                ? 'Quick data calculation during store re-deployment is enabled'
                : 'Quick data calculation during store re-deployment is disabled'
        );
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionClearCache()
    {
        \XLite\Core\Database::getCacheDriver()->deleteAll();
        \XLite::getInstance()->getContainer()->get('widget_cache')->deleteAll();
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionRebuildViewLists()
    {
        $plugins = [
            new \Includes\Decorator\Plugin\Templates\Plugin\ViewLists\Main(),
            new \Includes\Decorator\Plugin\Templates\Plugin\Compiler\Main(),
            new \Includes\Decorator\Plugin\ModuleHandlers\Main(),
            new \Includes\Decorator\Plugin\Templates\Plugin\ViewListsPostprocess\Main(),
        ];

        foreach ($plugins as $plugin) {
            $plugin->executeHookHandler();
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleQuickDataOptions()
    {
        $request = \XLite\Core\Request::getInstance();

        return array(
            'include' => $request->section,
        );
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionQuickDataCancel()
    {
        \XLite\Logic\QuickData\Generator::cancel();
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->quick_data_completed) {
            \XLite\Core\TopMessage::addInfo('The calculation of quick data has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('cache_management')
            );

        } elseif ($request->quick_data_failed) {
            \XLite\Core\TopMessage::addError('The calculation of quick data has been stopped.');

            $this->setReturnURL(
                $this->buildURL('cache_management')
            );
        }
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getQuickDataCancelFlagVarName()
    {
        return \XLite\Logic\QuickData\Generator::getCancelFlagVarName();
    }
}
