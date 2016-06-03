<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Attribute groups selector
 */
class AttributeGroups extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get attribute groups list
     *
     * @return array
     */
    protected function getAttributeGroupsList()
    {
        $list = array();
        $cnd = new \XLite\Core\CommonCell;
        $cnd->productClass = $this->getProductClass();

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\AttributeGroup')->search($cnd) as $e) {
            $list[$e->getId()] = htmlspecialchars($e->getName());
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            '0' => static::t('No group'),
        ) + $this->getAttributeGroupsList();
    }
}
