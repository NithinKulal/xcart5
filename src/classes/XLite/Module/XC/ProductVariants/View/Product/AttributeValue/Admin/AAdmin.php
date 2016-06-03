<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\AttributeValue\Admin;

/**
 * Abstract attribute value (admin)
 */
abstract class AAdmin extends \XLite\View\Product\AttributeValue\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * Check attribute is modified or not
     *
     * @return boolean
     */
    protected function isModified()
    {
        $result = parent::isModified();

        if ($result && $this->getAttribute() && $this->getProduct()->mustHaveVariants()) {
            foreach ($this->getProduct()->getVariantsAttributes() as $attr) {
                if ($attr->getId() == $this->getAttribute()->getId()) {
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }
}
