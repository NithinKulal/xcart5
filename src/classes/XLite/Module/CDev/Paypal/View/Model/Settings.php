<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

/**
 * Settings dialog model widget
 */
abstract class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->isPaypalSettings()) {
            $list[] = 'modules/CDev/Paypal/settings/style.css';
        }

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isPaypalSettings()) {
            $list[] = 'modules/CDev/Paypal/settings/module.js';
        }

        return $list;
    }
    
    /**
     * Check if current page is page with paypal settings
     *
     * @return boolean
     */
    protected function isPaypalSettings()
    {
        return 'module' == $this->getTarget()
            && $this->getModule()
            && 'CDev\Paypal' == $this->getModule()->getActualName();
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFieldsForSection($section)
    {
        $list = parent::getSchemaFieldsForSection($section);
        
        if ($this->isPaypalSettings()) {
            foreach ($list as $name => $option) {
                if ('show_admin_welcome' == $name) {
                    unset($list[$name]);
                }
            }
        }

        return $list;
    }
}

