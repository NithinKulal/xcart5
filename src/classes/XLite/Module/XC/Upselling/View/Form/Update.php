<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\View\Form;

/**
 * List
 */
class Update extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'upselling_products';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Get form parameters
     *
     * @return array
     */
    protected function getFormParams()
    {
        return parent::getFormParams()
            + array(
                'parentProductId' => \XLite\Core\Request::getInstance()->product_id,
            );
    }
}
