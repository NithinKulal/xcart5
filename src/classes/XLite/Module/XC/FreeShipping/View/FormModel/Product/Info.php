<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\FormModel\Product;

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

        $currency = \XLite::getInstance()->getCurrency();
        $currencySymbol = $currency->getCurrencySymbol(false);

        $schema = static::compose(
            $schema,
            [
                'shipping' => [
                    'requires_shipping' => [
                        'free_shipping'          => [
                            'label'            => static::t('Free shipping'),
                            'show_label_block' => false,
                            'type'             => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                            'show_when'        => [
                                '..' => [
                                    'requires_shipping' => true,
                                ],
                            ],
                            'position'         => 200,
                        ],
                        'fixed_shipping_freight' => [
                            'label'       => static::t('Freight'),
                            'help'        => static::t('This field can be used to set a fixed shipping fee for the product. Make sure the field value is a positive number (greater than zero).'),
                            'type'        => 'XLite\View\FormModel\Type\SymbolType',
                            'symbol'      => $currencySymbol,
                            'pattern'     => [
                                'alias'          => 'xcdecimal',
                                'prefix'         => '',
                                'rightAlign'     => false,
                                'digits'         => $currency->getE(),
                            ],
                            'constraints' => [
                                'Symfony\Component\Validator\Constraints\GreaterThanOrEqual' => [
                                    'value'   => 0,
                                    'message' => static::t('Minimum value is X', ['value' => 0]),
                                ],
                            ],
                            'show_when'   => [
                                '..' => [
                                    'requires_shipping' => true,
                                    'free_shipping'     => false,
                                ],
                            ],
                            'position'    => 300,
                        ],
                    ],
                ],
            ]
        );

        return $schema;
    }
}
