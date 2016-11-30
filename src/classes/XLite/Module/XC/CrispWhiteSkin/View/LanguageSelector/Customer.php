<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\LanguageSelector;

/**
 * Language selector (customer)
 */
class Customer extends \XLite\View\LanguageSelector\Customer
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

        return $return;
    }
    
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'language_selector_single';
    }
}
