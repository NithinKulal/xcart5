<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Tabs;

/**
 * Tabs related to paypal settings page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Settings extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'paypal_settings';
        $list[] = 'paypal_credit';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'paypal_settings' => [
                'weight' => 100,
                'title'  => static::t('Settings'),
                'widget' => 'XLite\Module\CDev\Paypal\View\Settings',
            ],
            'paypal_credit' => [
                'weight' => 200,
                'title'  => static::t('Paypal Credit'),
                'widget' => 'XLite\Module\CDev\Paypal\View\Settings',
            ],
        ];
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        $controller = \XLite::getController();
        $rightController = $controller instanceof \XLite\Module\CDev\Paypal\Controller\Admin\PaypalSettings
            && !($controller instanceof \XLite\Module\CDev\Paypal\Controller\Admin\PaypalCredit);

        if ($rightController
            && $this->getPaymentMethod()
            && $this->getPaymentMethod()->getServiceName() === \XLite\Module\CDev\Paypal\Main::PP_METHOD_PC
        ) {
            unset($this->tabs['paypal_credit']);
        }

        return parent::prepareTabs();
    }

    /**
     * Returns an URL to a tab
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL($target, '', ['method_id' => \XLite\Core\Request::getInstance()->method_id]);
    }
}
