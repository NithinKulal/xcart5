<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\XC\MultiCurrency\View\LanguageSelector;

/**
 * Language selector (customer)
 *
 * @Decorator\Depend ("XC\MultiCurrency")
 */
class Customer extends \XLite\View\LanguageSelector\Customer implements \XLite\Base\IDecorator
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $return = parent::getJSFiles();

        $return[] = $this->getDir() . LC_DS . 'script.js';
        $return[] = $this->getDir() . LC_DS . 'select.js';

        return $return;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        $return = 'modules' . LC_DS . 'XC' . LC_DS . 'MultiCurrency' . LC_DS . 'language_selector';

        return $return;
    }
}