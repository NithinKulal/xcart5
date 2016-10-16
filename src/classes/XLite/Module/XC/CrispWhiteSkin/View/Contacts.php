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
        $config = \XLite\Core\Config::getInstance()->Company;
        $parts = [
            $config->location_address,
            $config->location_city,
        ];

        $hasStates = $config->locationCountry && $config->locationCountry->hasStates();

        if ($hasStates) {
            $locationState = \XLite\Core\Database::getRepo('XLite\Model\State')->find($config->location_state);
            $locationState = $locationState ? $locationState->getCode() : null;
        } else {
            $locationState = \XLite\Core\Database::getRepo('XLite\Model\State')->getOtherState($config->location_custom_state);
            $locationState = $locationState ? $locationState->getState() : null;
        }

        $parts[] = $locationState;
        $parts[] = $config->location_country;
        $parts[] = $config->location_zipcode;

        return implode(', ', array_filter($parts));
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
