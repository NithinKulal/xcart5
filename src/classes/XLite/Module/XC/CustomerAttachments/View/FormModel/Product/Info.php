<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\View\FormModel\Product;

class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $schema[self::SECTION_DEFAULT]['is_customer_attachments_available'] = [
            'label'    => static::t('Allow buyers to attach files to this product'),
            'type'     => 'XLite\View\FormModel\Type\SwitcherType',
            'position' => 610,
        ];

        $schema[self::SECTION_DEFAULT]['is_customer_attachments_required'] = [
            'label'     => static::t('File Attaching is mandatory for this product'),
            'type'      => 'XLite\View\FormModel\Type\SwitcherType',
            'position'  => 620,
            'show_when' => [
                'default' => [
                    'is_customer_attachments_available' => '1',
                ],
            ],
        ];

        return $schema;
    }
}
