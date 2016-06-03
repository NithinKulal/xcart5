<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Countries management page controller
 */
class Countries extends \XLite\Controller\Admin\AAdmin
{

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Countries');
    }

    /**
     * Action 'update'
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        parent::doActionUpdateItemsList();

        \XLite\Core\Database::getRepo('XLite\Model\State')->cleanCache();
    }

    /**
     * Do action enable
     *
     * @return void
     */
    protected function doActionEnable()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Country')->updateInBatchById(
                array_fill_keys(
                    array_keys($select),
                    array('enabled' => true)
                )
            );
            \XLite\Core\TopMessage::addInfo(
                'Countries information has been successfully updated'
            );

        } else {
           \XLite\Core\TopMessage::addWarning('Please select the countries first');
        }
    }

    /**
     * Do action disable
     *
     * @return void
     */
    protected function doActionDisable()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Country')->updateInBatchById(
                array_fill_keys(
                    array_keys($select),
                    array('enabled' => false)
                )
            );
            \XLite\Core\TopMessage::addInfo(
                'Countries information has been successfully updated'
            );

        } else {
           \XLite\Core\TopMessage::addWarning('Please select the countries first');
        }
    }

}
