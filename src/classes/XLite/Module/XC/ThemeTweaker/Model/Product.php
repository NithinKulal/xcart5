<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model;

class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Checks if given property is available to modification through layout editor mode.
     *
     * @param  string  $property Checked entity property
     * @return boolean
     */
    public function isEditableProperty($property)
    {
        $editable = array('description', 'briefDescription');

        return in_array($property, $editable, true);
    }

    /**
     * Provides metadata for the property
     *
     * @param  string  $property Checked entity property
     * @return boolean
     */
    public function getFieldMetadata($property)
    {
        return array_merge(
            parent::getFieldMetadata($property),
            array(
                'data-inline-editable' => 'data-inline-editable',
            )
        );
    }
}
