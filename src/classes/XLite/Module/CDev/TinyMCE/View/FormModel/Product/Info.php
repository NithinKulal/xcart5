<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\TinyMCE\View\FormModel\Product;

class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * Replace description fields with TinyMCE inputs
     *
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $schema[self::SECTION_DEFAULT]['description']['type'] =
            'XLite\Module\CDev\TinyMCE\View\FormModel\Type\TinymceType';
        $schema[self::SECTION_DEFAULT]['full_description']['type'] =
            'XLite\Module\CDev\TinyMCE\View\FormModel\Type\TinymceType';

        return $schema;
    }
}
