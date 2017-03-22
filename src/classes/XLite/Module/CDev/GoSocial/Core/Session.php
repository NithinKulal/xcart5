<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Core;


class Session extends \XLite\Core\Session implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    public function getLocale()
    {
        return sprintf('%s_%s',
            $this->preprocessLang($this->getLanguageCodeForLocale()),
            $this->getCountryCodeForLocale()
        );
    }

    /**
     * @return string
     */
    protected function getLanguageCodeForLocale()
    {
        $defaultLng = \XLite::isAdminZone()
            ? \XLite\Core\Config::getInstance()->General->default_admin_language
            : \XLite\Core\Config::getInstance()->General->default_language;

        return $this->getCurrentLanguage() ?: $defaultLng;
    }

    /**
     * @return string
     */
    protected function getCountryCodeForLocale()
    {
        if (\XLite\Core\Config::getInstance()->Company->location_country) {
            $country = \XLite\Core\Config::getInstance()->Company->location_country;
        } else {
            $country = \XLite\Model\Address::getDefaultFieldValue('country')
                ? \XLite\Model\Address::getDefaultFieldValue('country')->getCode()
                : 'US';
        }

        return $country;
    }

    protected function preprocessLang($code)
    {
        if (strtolower($code) === 'gb') {
            return 'en';
        }

        return $code;
    }
}
