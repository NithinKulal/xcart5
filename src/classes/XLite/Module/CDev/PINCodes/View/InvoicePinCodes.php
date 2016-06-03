<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View;

/**
 * Invoice item pin codes
 *
 * @ListChild (list="invoice.item.name", weight="200")
 * @ListChild (list="invoice.item.name", weight="30", zone="admin")
 */
class InvoicePinCodes extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_ITEM = 'item';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/PINCodes/invoice/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/PINCodes/invoice/pin_codes.twig';
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
            self::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Order item', null, false, '\\XLite\\\Model\\OrderItem'),
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
            && (
                (!\XLite::isAdminZone() && 0 < count($this->getParam(self::PARAM_ITEM)->getSoldPinCodes()))
                || (\XLite::isAdminZone() && 0 < count($this->getParam(self::PARAM_ITEM)->getPinCodes()))
            );
    }

    /**
     * Get pin codes 
     * 
     * @return array
     */
    protected function getPinCodes()
    {
        $codes = array();

        $list = \XLite::isAdminZone()
            ? $this->getParam(self::PARAM_ITEM)->getPinCodes()
            : $this->getParam(static::PARAM_ITEM)->getSoldPinCodes();

        foreach ($list as $code) {
            $codes[] = $code->getCode();
        }

        return $codes;
    }

    /**
     * Get comma separated pin codes 
     *
     * @return string
     */
    protected function getCommaSeparatedPinCodes()
    {
        return implode(', ', $this->getPinCodes());
    }

}
