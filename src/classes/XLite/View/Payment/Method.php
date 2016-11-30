<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment;

/**
 * Payment method
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Method extends \XLite\View\Dialog
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'payment_method';

        return $result;
    }

    /**
     * Check widget visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible()
            && $this->getPaymentMethod()
            && $this->getPaymentMethod()->getProcessor()
            && $this->getPaymentMethod()->getProcessor()->getSettingsWidget();
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->find(\XLite\Core\Request::getInstance()->method_id);
    }

    /**
     * Check - is settings widget is widget class or not
     *
     * @return boolean
     */
    public function isWidgetSettings()
    {
        $widget = $this->getPaymentMethod()->getProcessor()->getSettingsWidget();

        return 0 === strpos($widget, '\XLite\View\\')
            || 0 === strpos($widget, '\XLite\Module\\');
    }


    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'payment/method';
    }
}
