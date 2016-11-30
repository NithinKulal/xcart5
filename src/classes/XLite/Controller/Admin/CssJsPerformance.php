<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Performance
 */
class CssJsPerformance extends \XLite\Controller\Admin\Settings
{
    /**
     * Page
     *
     * @var string
     */
    public $page = self::PERFORMANCE_PAGE;

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list[static::PERFORMANCE_PAGE] = static::t('Performance');

        return $list;
    }

    /**
     * Clean aggregation cache directory
     *
     * @return void
     */
    public function doActionCleanAggregationCache()
    {
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_CACHE_RESOURCES);

        \Less_Cache::SetCacheDir(LC_DIR_DATACACHE);
        \Less_Cache::CleanCache();
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(
            \XLite::CACHE_TIMESTAMP,
            intval(microtime(true))
        );

        \XLite\Core\TopMessage::addInfo('Aggregation cache has been cleaned');
    }

    /**
     * Clean view cache
     *
     * @return void
     */
    public function doActionCleanViewCache()
    {
        if ($this->getContainer()->get('widget_cache_manager')->deleteAll()) {
            \XLite\Core\TopMessage::addInfo('Widgets cache has been cleaned');

        } else {
            \XLite\Core\TopMessage::addWarning('Widgets cache has not been cleaned completely');
        }
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
            $this->buildURL('css_js_performance')
        );
    }
}
