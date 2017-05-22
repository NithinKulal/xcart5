<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\FormModel\Product;

class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $schema[self::SECTION_DEFAULT]['tags'] = [
            'label'      => static::t('Tags'),
            'type'       => 'XLite\Module\XC\ProductTags\View\FormModel\Type\TagsType',
            'multiple'   => true,
            'position'   => 650,
        ];

        return $schema;
    }
}
