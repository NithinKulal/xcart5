<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Notifications common page controller
 */
class NotificationAttachments extends \XLite\Controller\Admin\Settings
{

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Attachments');
    }

    /**
     * Get options for current tab (category)
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')->findByCategoryAndVisible('NotificationAttachments');
    }

    /**
     * Update model
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\NotificationAttachments';
    }


    /**
     * Get recommended module URL
     *
     * @return string
     */
    public function getRecommendedModuleURL()
    {
        if (!isset($this->recommendedModuleURL)) {
            $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(
                array(
                    'author' => 'QSL',
                    'name'   => 'PDFInvoice',
                ),
                array(
                    'fromMarketplace' => 'ASC',
                )
            );

            if ($module && !$module->getEnabled()) {
                // Module disabled or not installed
                $this->recommendedModuleURL = $module->getFromMarketplace()
                    ? $module->getMarketplaceURL()
                    : $module->getInstalledURL();
            }

            if (empty($this->recommendedModuleURL)) {
                $this->recommendedModuleURL = '';
            }
        }

        return $this->recommendedModuleURL;
    }

    /**
     * Return text of recommended module URL
     *
     * @return string
     */
    public function getRecommendedModuleText()
    {
        return $this->getRecommendedModuleURL()
            ? static::t('Get a more customizeable PDF invoice solution for your store', array('url' => $this->getRecommendedModuleURL()))
            : '';
    }
}
