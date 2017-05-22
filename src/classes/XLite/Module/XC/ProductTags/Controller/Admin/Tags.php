<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Controller\Admin;

/**
 * Tags controller
 *
 */
class Tags extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Product tags');
    }

    // {{{ Actions

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new \XLite\Module\XC\ProductTags\View\ItemsList\Model\Tag;
        $list->processQuick();

    }

    /**
     * Do action 'delete'
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('\XLite\Module\XC\ProductTags\Model\Tag')->deleteInBatchById($select);
            \XLite\Core\TopMessage::addInfo(
                'Selected tags have been deleted'
            );

        } else {
            \XLite\Core\TopMessage::addWarning('Please select the tags first');
        }
    }

    // }}}
}
