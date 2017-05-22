<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\View;

/**
 * Attachment description widget
 */
class AttachmentDescription extends \XLite\View\AView
{
    /**
     * Widget params
     */
    const PARAM_ORDER_ITEM = 'orderItem';
    const PARAM_IS_DETAIL_PAGE = 'isDetailPage';

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CustomerAttachments/attachment_description.twig';
    }

    /**
     * Get human readable allowed file size
     *
     * @return string
     */
    public function getAllowedSizeHumanReadable()
    {
        return \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments::getAllowedSize() / \XLite\Core\Converter::MEGABYTE . 'MB';
    }

    /**
     * Get allowed file extensions string
     *
     * @return string
     */
    public function getAllowedExtensionsString()
    {
        $config = \XLite\Core\Config::getInstance()->XC->CustomerAttachments;

        return preg_replace('/[^,\w+]/', '', $config->extensions);
    }

    /**
     * Get allowed to attach files quantity
     *
     * @return integer
     */
    public function getAllowedQuantity()
    {
        return $this->getItem()
            ? \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments::getAllowedQuantity($this->getItem())
            : \XLite\Core\Config::getInstance()->XC->CustomerAttachments->quantity;
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
            self::PARAM_ORDER_ITEM => new \XLite\Model\WidgetParam\TypeObject('Order item', null, false, '\XLite\Model\OrderItem'),
            self::PARAM_IS_DETAIL_PAGE => new \XLite\Model\WidgetParam\TypeBool('Is uses on product detail page', false),
        );
    }

    /**
     * Get order item
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(self::PARAM_ORDER_ITEM);
    }
}