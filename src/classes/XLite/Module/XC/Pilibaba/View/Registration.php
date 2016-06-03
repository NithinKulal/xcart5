<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View;

/**
 * Registration widget
 */
class Registration extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'pilibaba_registration';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Pilibaba/info_block.css';

        return $list;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'modules/XC/Pilibaba/registration.twig';
    }

    /**
     * Get payment method
     *
     * @return  \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        return \XLite\Module\XC\Pilibaba\Main::getPaymentMethod();
    }

    /**
     * Get payment method information url
     *
     * @return string
     */
    public function getInformationURL()
    {
        return $this->getPaymentMethod() && $this->getPaymentMethod()->getSetting('mode') === 'test'
            ? 'http://preen.pilibaba.com'
            : 'http://en.pilibaba.com';
    }
}
