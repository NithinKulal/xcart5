<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguageSelector;

/**
 * Language selector (customer)
 *
 *
 * @ListChild (list="layout.header.bar.links.newby", weight="999999", zone="customer")
 * @ListChild (list="layout.header.bar.links.logged", weight="999999", zone="customer")
 * @ListChild (list="header.language.menu", weight="100", zone="customer")
 */
class Customer extends \XLite\View\LanguageSelector\ALanguageSelector
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Check if language is active
     *
     * @param \XLite\Model\Language $language Language to check
     *
     * @return boolean
     */
    protected function isActiveLanguage(\XLite\Model\Language $language)
    {
        return !$this->isLanguageSelected($language);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return !$this->isCheckoutLayout() && 0 < count($this->getActiveLanguages());
    }

}
