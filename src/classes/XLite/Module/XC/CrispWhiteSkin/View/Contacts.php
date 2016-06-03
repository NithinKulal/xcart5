<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Contact Us
 */
class Contacts extends \XLite\View\AView
{
    public function getCSSFiles()
    {
        return [
            [
                'file'  => 'contact_us/style.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ],
        ];
    }
    public function getPhone()
    {
        return \XLite\Core\Config::getInstance()->Company->company_phone;
    }

    public function getLocation()
    {
        $parts = [
            \XLite\Core\Config::getInstance()->Company->location_address,
            \XLite\Core\Config::getInstance()->Company->location_city,
            \XLite\Core\Config::getInstance()->Company->location_state,
            \XLite\Core\Config::getInstance()->Company->location_country,
        ];

        return implode(', ', $parts);
    }

    public function getEmail()
    {
        return \XLite\Core\Config::getInstance()->Company->support_department;
    }

    protected function getDefaultTemplate()
    {
        return 'contact_us/template.twig';
    }
}
