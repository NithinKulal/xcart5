<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\FormModel\Product;

class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/CDev/Sale/form_model/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $schema = static::compose(
            $schema,
            [
                'prices_and_inventory' => [
                    'price' => [
                        'participate_sale' => [
                            'label'            => static::t('Sale'),
                            'show_label_block' => false,
                            'type'             => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                            'position'         => 300,
                        ],
                        'sale_price'       => [
                            'type'             => 'XLite\Module\CDev\Sale\View\FormModel\Type\Sale',
                            'show_label_block' => false,
                            'show_when'        => [
                                '..' => [
                                    'participate_sale' => true,
                                ],
                            ],
                            'position'         => 400,
                        ],
                    ],
                ],
            ]
        );

        return $schema;
    }
}
