<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment;

/**
 * List of payment methods for popup widget
 */
class MethodsPopupList extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'payment_method_selection';

        return $list;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'payment/methods_popup_list/body.twig';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            \XLite\View\Button\Payment\AddMethod::PARAM_PAYMENT_METHOD_TYPE => new \XLite\Model\WidgetParam\TypeCollection('Payment methods type', array()),
        );
    }

    /**
     * Return payment type for the payment methods list
     *
     * @return string
     */
    protected function getPaymentType()
    {
        return $this->getParam(\XLite\View\Button\Payment\AddMethod::PARAM_PAYMENT_METHOD_TYPE);
    }

    /**
     * Return payment methods list structure to use in the widget
     *
     * @return array
     */
    protected function getPaymentMethods()
    {
        $result = array();

        $list = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findForAdditionByType($this->getPaymentType());

        foreach ($list as $entry) {
            $result[$entry->getModuleName()][] = $entry;
        }

        return $result;
    }
}
