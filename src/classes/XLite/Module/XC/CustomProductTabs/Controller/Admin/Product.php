<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Controller\Admin;

/**
 * Product
 */
class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * is edited tab
     *
     * @return boolean
     */
    public function isProductTabPage()
    {
        return isset(\XLite\Core\Request::getInstance()->tab_id);
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        if (!$this->isNew()) {
            $list['tabs'] = static::t('Tabs');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if (!$this->isNew()) {
            $list['tabs']    = 'modules/XC/CustomProductTabs/product/tabs.twig';
        }

        return $list;
    }

    /**
     * Update product tabs list
     *
     * @return void
     */
    protected function doActionUpdateProductTabs()
    {
        $list = new \XLite\Module\XC\CustomProductTabs\View\ItemsList\Model\Product\Tab;
        $list->processQuick();
    }

    /**
     * Update product tab model
     *
     * @return void
     */
    protected function doActionUpdateProductTab()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(
                \XLite\Core\Converter::buildURL(
                    'product',
                    null,
                    array(
                        'product_id' => \XLite\Core\Request::getInstance()->product_id,
                        'page'       => 'tabs',
                        'tab_id'     => $this->getModelForm()->getModelObject()->getId()
                    )
                )
            );
        }
    }

    /**
     * Update model and close page
     *
     * @return void
     */
    protected function doActionUpdateProductTabAndClose()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(
                \XLite\Core\Converter::buildUrl(
                    'product',
                    null,
                    array(
                        'product_id' => \XLite\Core\Request::getInstance()->product_id,
                        'page'       => 'tabs',
                    )
                )
            );
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return \XLite\Core\Request::getInstance()->page == 'tabs'
            ? 'XLite\Module\XC\CustomProductTabs\View\Model\Product\Tab'
            : parent::getModelFormClass();
    }
}
