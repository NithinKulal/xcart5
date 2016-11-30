<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\PdfPage;

/**
 * Pdf test page
 */
class Invoice extends \XLite\View\APdfPage
{
    /**
     * Widget parameter names
     */
    const PARAM_ORDER = 'order';

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Get pdf language
     *
     * @return string
     */
    public function getLanguageCode()
    {
        if ($this->getOrder() && $this->getOrder()->getProfile() && $this->getInterface() === \XLite::CUSTOMER_INTERFACE) {
            return $this->getOrder()->getProfile()->getLanguage();
        } else {
            return parent::getLanguageCode();
        }
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
            static::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                'XLite\Model\Order'
            ),
        );
    }

    /**
     * Returns PDF document title
     *
     * @return string
     */
    public function getDocumentTitle()
    {
        return $this->getOrder()
            ? 'Order ' . $this->getOrder()->getPrintableOrderNumber() . ' invoice'
            : 'Order invoice';
    }


    /**
     * Page Html template path
     *
     * @return string
     */
    public function getPdfStylesheets()
    {
        return array_merge(
            parent::getPdfStylesheets(),
            array(
                'order/invoice/common.less',
                'order/invoice/style.less',
                'order/invoice/print.css',
            )
        );
    }

    /**
     * Page Html template path
     *
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'order/invoice/page.twig';
    }
}
