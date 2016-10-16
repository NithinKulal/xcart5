<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2015 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

function isSettingExists($adaptiveMethod)
{
    return \XLite\Core\Database::getRepo('XLite\Model\Payment\MethodSetting')
        ->findOneBy(array(
            'payment_method'    => $adaptiveMethod,
            'name'              => 'matchCriteria'
        )
    );
}

return function()
{
    $adaptiveMethod = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
        ->findOneBy(array('service_name' => 'PaypalAdaptive'));

    if ($adaptiveMethod && !isSettingExists($adaptiveMethod)) {
        $setting = new \XLite\Model\Payment\MethodSetting();

        $setting->setName('matchCriteria');
        $setting->setValue('none');
        $setting->setPaymentMethod($adaptiveMethod);
        $setting->persist();

        $adaptiveMethod->addSettings($setting);
    }

    \XLite\Core\Database::getEM()->flush();
};
