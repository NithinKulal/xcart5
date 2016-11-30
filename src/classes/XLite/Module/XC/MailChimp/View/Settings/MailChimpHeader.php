<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Settings;

use  XLite\Module\XC\MailChimp\Core;

/**
 * Warning
 *
 * @ListChild (list="crud.modulesettings.header", zone="admin", weight="100")
 */
class MailChimpHeader extends \XLite\Module\XC\MailChimp\View\Settings\ASettings
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $return = parent::getCSSFiles();

        $return[] = 'main/style.css';
        $return[] = $this->getDir() . '/header.css';

        return $return;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/header.twig';
    }

    /**
     * Get current sections
     *
     * @return array
     */
    protected function getSections()
    {
        return $this->getAllSections();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !Core\MailChimp::hasAPIKey();
    }
}
