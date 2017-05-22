<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Customer;

/**
 * Customer order messages
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Customer\Order implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses()
            . (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed() ? ' multivendor-enabled' : '');
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetParameters()
    {
        return parent::getWidgetParameters() + array(
            'recipient_id' => intval(\XLite\Core\Request::getInstance()->recipient_id),
        );
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $initialize = !isset($this->commonParams);

        $this->commonParams = parent::getCommonParams();

        if ($initialize) {
            $this->commonParams += array(
                'recipient_id' => intval(\XLite\Core\Request::getInstance()->recipient_id),
            );
        }

        return $this->commonParams;
    }

    /**
     * Get order items
     *
     * @return \XLite\Model\OrderItem[]
     */
    protected function getItems()
    {
        return $this->getCurrentThreadOrder()->getItems();
    }

}
