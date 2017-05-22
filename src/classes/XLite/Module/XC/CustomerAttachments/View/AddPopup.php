<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\View;

/**
 * Add popup link in cart item info
 *
 * @ListChild (list="cart.item.info", zone="customer", weight="80")
 */
class AddPopup extends \XLite\View\AView
{
    /**
     * Widget param
     */
    const PARAM_ITEM = 'item';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/CustomerAttachments/attachment.popup.js';

        return $list;
    }

    /**
     * Register CSS files. Styles for popup
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/CustomerAttachments/style.css';

        return $list;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CustomerAttachments/cart_item_info_attachment.twig';
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
            static::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Order Item', null, false, '\XLite\Model\OrderItem'),
        );
    }

    /**
     * Get order item
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(static::PARAM_ITEM);
    }

    /**
     * Get attached files quantity
     *
     * @return integer
     */
    protected function getAttachedFilesQuantity()
    {
        return count($this->getItem()->getCustomerAttachments());
    }

    /**
     * Check if attachment need for this item
     *
     * @return boolean
     */
    protected function isAttachmentNeed()
    {
        $count = count($this->getItem()->getCustomerAttachments());
        $isRequired = $this->getItem()->getObject()->isCustomerAttachmentsMandatory();

        return $isRequired && !$count;
    }
} 
