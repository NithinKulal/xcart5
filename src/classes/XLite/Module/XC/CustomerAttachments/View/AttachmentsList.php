<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\View;

/**
 * Widget for attachments list in admin zone
 *
 * @ListChild (list="invoice.item.name", weight="100", zone="admin")
 */
class AttachmentsList extends \XLite\View\AView
{
    /**
     * Widget param item
     */
    const PARAM_ITEM = 'item';

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
        return 'modules/XC/CustomerAttachments/attachments-list.twig';
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
     * Return true if current page is invoice
     *
     * @return boolean
     */
    protected function isInvoicePage()
    {
        return 'invoice' === \XLite\Core\Request::getInstance()->page
            || 'invoice' == \XLite\Core\Request::getInstance()->mode;
    }
}
