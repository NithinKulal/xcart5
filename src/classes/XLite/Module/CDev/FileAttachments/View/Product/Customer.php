<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\View\Product;

/**
 * File attachments list for customer interface
 *
 * @ListChild (list="product.details.page.tab.description.file-attachments", weight="10")
 */
class Customer extends \XLite\View\AView
{
    /**
     * Widget parameter name
     */
    const PARAM_PRODUCT = 'product';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/FileAttachments/style.css';
        $list[] = 'browse_server/icons.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/FileAttachments/product.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, 'XLite\Model\Product'),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getProduct()
            && $this->getAttachments();
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * Get attachments
     *
     * @return array
     */
    protected function getAttachments()
    {
        return $this->getProduct()->getAttachments()->toArray();
    }
}
