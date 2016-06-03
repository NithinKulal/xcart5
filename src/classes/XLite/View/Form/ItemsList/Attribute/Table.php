<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\ItemsList\Attribute;

/**
 * Attributes list table form
 */
class Table extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'attributes';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'updateItemsList';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return array(
            'product_class_id'  => intval(\XLite\Core\Request::getInstance()->product_class_id),
            'itemsList'         => '\XLite\View\ItemsList\Model\Attribute',
        );
    }

}
