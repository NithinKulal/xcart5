<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View\Tabs;

/**
 * Tabs related to paypal settings page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Settings extends \XLite\View\Tabs\ATabs
{
    /**
     * Information on tab widgets and their targets defined as an array(tab) descriptions
     *
     * @return array
     */
    protected function defineTabs()
    {
        return array(
            'pilibaba_registration' => array(
                'title'    => 'Registration',
                'widget'   => 'XLite\Module\XC\Pilibaba\View\Registration',
            ),
            'pilibaba_settings' => array(
                'title'    => 'Connection',
                'widget'   => 'XLite\Module\XC\Pilibaba\View\Config',
            ),
        );
    }

    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $method = \XLite\Module\XC\Pilibaba\Main::getPaymentMethod();
        if ($method->getProcessor()
            && $method->getProcessor()->isConfigured($method)
        ) {
            if (isset($this->tabs['pilibaba_registration'])) {
                unset($this->tabs['pilibaba_registration']);
            }
        }
    }

    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'pilibaba_settings';
        $list[] = 'pilibaba_registration';

        return $list;
    }
}
