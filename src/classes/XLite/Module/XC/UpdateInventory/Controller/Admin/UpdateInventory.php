<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UpdateInventory\Controller\Admin;

/**
 * Update inventory page controller
 */
class UpdateInventory extends \XLite\Controller\Admin\Import
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Update quantity');
    }

    /**
     * Get import target
     *
     * @return string
     */
    public function getImportTarget()
    {
        return \XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY;
    }

    /**
     * Get array of import options
     *
     * @param array $options Array of additional options OPTIONAL
     *
     * @return array
     */
    protected function getImportOptions($options = array())
    {
        $options = parent::getImportOptions($options);

        $options['target'] = \XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY;
        $options['warningsAccepted'] = true;
        $options['importMode'] = \XLite\View\Import\Begin::MODE_UPDATE_ONLY;

        if (!empty(\XLite\Core\Request::getInstance()->options['delimiter'])) {
            $options['delimiter'] = \XLite\Core\Request::getInstance()->options['delimiter'];
        }

        return $options;
    }
}
