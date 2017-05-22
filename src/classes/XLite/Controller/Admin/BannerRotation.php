<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use \XLite\Logic\BannerRotation\Processor;

/**
 * Banner rotation controller
 */
class BannerRotation extends \XLite\Controller\Admin\Settings
{
    /**
     * Recommended module URL (run-time cache)
     *
     * @var string
     */
    protected $recommendedModuleURL = null;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Front page');
    }

    /**
     * Update model
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $list = new \XLite\View\ItemsList\BannerRotationImages();
        $list->processQuick();

        $this->getModelForm()->performAction('update');
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Settings';
    }

    /**
     * Get options for current tab (category)
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')->findByCategoryAndVisible('BannerRotation');
    }
}
