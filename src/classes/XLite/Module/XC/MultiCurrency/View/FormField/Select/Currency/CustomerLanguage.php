<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Select\Currency;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Customer currency selector
 */
class CustomerLanguage extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return \XLite\Core\Session::getInstance()->getLanguage()->getCode();
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $return = array();

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Language')->findActiveLanguages() as $language) {
            $return[$language->getCode()] = $language->getName();
        }

        return $return;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Language');
    }
}