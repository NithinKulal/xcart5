<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Product;

/**
 * Product form model
 */
class Info extends \XLite\View\FormModel\AFormModel
{
    /**
     * Do not render form_start and form_end in null returned
     *
     * @return string|null
     */
    protected function getTarget()
    {
        return 'product';
    }

    /**
     * @return string
     */
    protected function getAction()
    {
        return 'update';
    }

    /**
     * @return array
     */
    protected function getActionParams()
    {
        $identity = $this->getDataObject()->default->identity;

        return $identity ? ['product_id' => $identity] : [];
    }

    /**
     * @return array
     */
    protected function defineSections()
    {
        return array_replace(parent::defineSections(), [
            'prices_and_inventory' => [
                'label'    => static::t('Prices & Inventory'),
                'position' => 100,
            ],
            'shipping'             => [
                'label'    => static::t('Shipping'),
                'position' => 200,
            ],
            'marketing'            => [
                'label'    => static::t('Marketing'),
                'position' => 300,
            ],
        ]);
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $skuMaxLength  = \XLite\Core\Database::getRepo('XLite\Model\Product')->getFieldInfo('sku', 'length');
        $nameMaxLength = \XLite\Core\Database::getRepo('XLite\Model\ProductTranslation')->getFieldInfo('name', 'length');

        $memberships = [];
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Membership')->findActiveMemberships() as $membership) {
            $memberships[$membership->getMembershipId()] = $membership->getName();
        }

        $taxClasses = [];
        foreach (\XLite\Core\Database::getRepo('XLite\Model\TaxClass')->findAll() as $taxClass) {
            $taxClasses[$taxClass->getId()] = $taxClass->getName();
        }

        $taxClassSchema = [
            'label'    => static::t('Tax class'),
            'position' => 200,
        ];
        if ($taxClasses) {
            $taxClassSchema = array_replace(
                $taxClassSchema,
                [
                    'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                    'choices'           => array_flip($taxClasses),
                    'choices_as_values' => true,
                    'placeholder'       => static::t('Default'),
                ]
            );
        } else {
            $taxClassSchema = array_replace(
                $taxClassSchema,
                [
                    'type'    => 'XLite\View\FormModel\Type\CaptionType',
                    'caption' => static::t('Default'),
                ]
            );
        }

        $currency       = \XLite::getInstance()->getCurrency();
        $currencySymbol = $currency->getCurrencySymbol(false);

        $weightFormat           = \XLite\Core\Config::getInstance()->Units->weight_format;
        $weightFormatDelimiters = \XLite\View\FormField\Select\FloatFormat::getDelimiters($weightFormat);

        $inventoryTrackingDescription = $this->getDataObject()->default->identity ? $this->getWidget([
            'template' => 'form_model/product/info/inventory_tracking_description.twig',
        ])->getContent() : '';

        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getDataObject()->default->identity);
        $images  = [];
        if ($product) {
            $images = $product->getImages();
        }

        $schema = [
            self::SECTION_DEFAULT  => [
                'name'               => [
                    'label'       => static::t('Product name'),
                    'required'    => true,
                    'constraints' => [
                        'Symfony\Component\Validator\Constraints\NotBlank' => [
                            'message' => static::t('This field is required'),
                        ],
                        'XLite\Core\Validator\Constraints\MaxLength'       => [
                            'length'  => $nameMaxLength,
                            'message' =>
                                static::t('Name length must be less then {{length}}', ['length' => $nameMaxLength + 1]),
                        ],
                    ],
                    'position'    => 100,
                ],
                'sku'                => [
                    'label'       => static::t('SKU'),
                    'constraints' => [
                        'XLite\Core\Validator\Constraints\MaxLength' => [
                            'length'  => $skuMaxLength,
                            'message' =>
                                static::t('SKU length must be less then {{length}}', ['length' => $skuMaxLength + 1]),
                        ],
                    ],
                    'position'    => 200,
                ],
                'images'             => [
                    'label'        => static::t('Images'),
                    'type'         => 'XLite\View\FormModel\Type\OldType',
                    'oldType'      => 'XLite\View\FormField\FileUploader\Image',
                    'fieldOptions' => ['value' => $images, 'multiple' => true],
                    'position'     => 300,
                ],
                'category'           => [
                    'label'       => static::t('Category'),
                    'description' => static::t('Switch to Category tree'),
                    'type'        => 'XLite\View\FormModel\Type\ProductCategoryType',
                    'multiple'    => true,
                    'show_when' => [
                        'default' => [
                            'category_widget_type' => 'search',
                        ],
                    ],
                    'position'    => 400,
                ],
                'category_tree'      => [
                    'label'       => static::t('Category'),
                    'description' => static::t('Switch to Category search'),
                    'type'        => 'XLite\View\FormModel\Type\ProductCategoryTreeType',
                    'multiple'    => true,
                    'show_when' => [
                        'default' => [
                            'category_widget_type' => 'tree',
                        ],
                    ],
                    'position'    => 450,
                ],
                'category_widget_type' => [
                    'type' => 'Symfony\Component\Form\Extension\Core\Type\HiddenType',
                ],
                'description'        => [
                    'label'    => static::t('Description'),
                    'type'     => 'XLite\View\FormModel\Type\TextareaAdvancedType',
                    'position' => 500,
                ],
                'full_description'   => [
                    'label'    => static::t('Full description'),
                    'type'     => 'XLite\View\FormModel\Type\TextareaAdvancedType',
                    'position' => 600,
                ],
                'available_for_sale' => [
                    'label'    => static::t('Available for sale'),
                    'type'     => 'XLite\View\FormModel\Type\SwitcherType',
                    'position' => 700,
                ],
                'arrival_date'       => [
                    'label'    => static::t('Arrival date'),
                    'type'     => 'XLite\View\FormModel\Type\DatepickerType',
                    'position' => 800,
                ],
            ],
            'prices_and_inventory' => [
                'memberships'        => [
                    'label'             => static::t('Memberships'),
                    'type'              => 'XLite\View\FormModel\Type\Select2Type',
                    'multiple'          => true,
                    'choices'           => array_flip($memberships),
                    'choices_as_values' => true,
                    'position'          => 100,
                ],
                'tax_class'          => $taxClassSchema,
                'price'              => [
                    'label'       => static::t('Price'),
                    'type'        => 'XLite\View\FormModel\Type\SymbolType',
                    'symbol'      => $currencySymbol,
                    'pattern'     => [
                        'alias'      => 'xcdecimal',
                        'prefix'     => '',
                        'rightAlign' => false,
                        'digits'     => $currency->getE(),
                    ],
                    'constraints' => [
                        'Symfony\Component\Validator\Constraints\GreaterThanOrEqual' => [
                            'value'   => 0,
                            'message' => static::t('Minimum value is X', ['value' => 0]),
                        ],
                    ],
                    'position'    => 300,
                ],
                'inventory_tracking' => [
                    'label'       => static::t('Inventory tracking for this product is'),
                    'description' => $inventoryTrackingDescription,
                    'type'        => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'      => [
                        'inventory_tracking' => [
                            'type'     => 'XLite\View\FormModel\Type\SwitcherType',
                            'position' => 100,
                        ],
                        'quantity'           => [
                            'label'     => static::t('Quantity in stock'),
                            'type'      => 'XLite\View\FormModel\Type\PatternType',
                            'pattern'   => [
                                'alias'      => 'integer',
                                'rightAlign' => false,
                            ],
                            'show_when' => [
                                'prices_and_inventory' => [
                                    'inventory_tracking' => [
                                        'inventory_tracking' => '1',
                                    ],
                                ],
                            ],
                            'position'  => 200,
                        ],
                    ],
                    'position'    => 400,
                ],
            ],
            'shipping'             => [
                'weight'            => [
                    'label'    => static::t('Weight'),
                    'type'     => 'XLite\View\FormModel\Type\SymbolType',
                    'symbol'   => \XLite\Core\Config::getInstance()->Units->weight_symbol,
                    'pattern'  => [
                        'alias'          => 'xcdecimal',
                        'digitsOptional' => false,
                        'rightAlign'     => false,
                        'digits'         => 4,
                    ],
                    'position' => 100,
                ],
                'requires_shipping' => [
                    'label'    => static::t('Requires shipping'),
                    'type'     => 'XLite\View\FormModel\Type\SwitcherType',
                    'position' => 200,
                ],
                'shipping_box'      => [
                    'type'      => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'    => [
                        'separate_box' => [
                            'label'    => static::t('Separate box'),
                            'type'     => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                            'position' => 100,
                        ],
                        'dimensions'   => [
                            'label'     => static::t('Length x Width x Height') . ' (' . \XLite\Core\Translation::translateDimSymbol() . ')',
                            'type'      => 'XLite\View\FormModel\Type\DimensionsType',
                            'show_when' => [
                                'shipping' => [
                                    'shipping_box' => [
                                        'separate_box' => 1,
                                    ],
                                ],
                            ],
                            'position'  => 200,
                        ],
                    ],
                    'show_when' => [
                        'shipping' => [
                            'requires_shipping' => '1',
                        ],
                    ],
                    'position'  => 300,
                ],
                'items_in_box'      => [
                    'type'      => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'    => [
                        'items_in_box' => [
                            'label'            => static::t('Maximum items in box'),
                            'show_label_block' => true,
                            'show_when'        => [
                                'shipping' => [
                                    'shipping_box' => [
                                        'separate_box' => 1,
                                    ],
                                ],
                            ],
                            'position'         => 100,
                        ],
                    ],
                    'show_when' => [
                        'shipping' => [
                            'requires_shipping' => '1',
                        ],
                    ],
                    'position'  => 400,
                ],
            ],
            'marketing'            => [
                'meta_description_type' => [
                    'label'             => static::t('Meta description'),
                    'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                    'choices'           => array_flip([
                        'A' => static::t('Autogenerated'),
                        'C' => static::t('Custom'),
                    ]),
                    'choices_as_values' => true,
                    'placeholder'       => false,
                    'position'          => 100,
                ],
                'meta_description'      => [
                    'label'              => ' ',
                    'type'               => 'XLite\View\FormModel\Type\MetaDescriptionType',
                    'required'           => true,
                    'constraints'        => [
                        'XLite\Core\Validator\Constraints\MetaDescription' => [
                            'message'          => static::t('This field is required'),
                            'dependency'       => 'form.marketing.meta_description_type',
                            'dependency_value' => 'C',
                        ],
                    ],
                    'validation_trigger' => 'form.marketing.meta_description_type',
                    'show_when'          => [
                        'marketing' => [
                            'meta_description_type' => 'C',
                        ],
                    ],
                    'position'           => 200,
                ],
                'meta_keywords'         => [
                    'label'    => static::t('Meta keywords'),
                    'position' => 300,
                ],
                'product_page_title'    => [
                    'label'       => static::t('Product page title'),
                    'description' => static::t('Leave blank to use product name as Page Title.'),
                    'position'    => 400,
                ],
                'clean_url'             => [
                    'label'           => static::t('Clean URL'),
                    'type'            => 'XLite\View\FormModel\Type\CleanURLType',
                    'extension'       => '.html',
                    'objectClassName' => 'XLite\Model\Product',
                    'objectId'        => $this->getDataObject()->default->identity,
                    'objectIdName'    => 'product_id',
                    'position'        => 500,
                ],
            ],
        ];

        return $schema;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result   = parent::getFormButtons();
        $identity = $this->getDataObject()->default->identity;

        $label            = $identity ? 'Update product' : 'Add product';
        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        if ($identity) {
            $url                     = $this->buildURL(
                'product',
                'clone',
                ['product_id' => $identity]
            );
            $result['clone-product'] = new \XLite\View\Button\Link(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Clone this product',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'model-button always-enabled',
                    \XLite\View\Button\Link::PARAM_LOCATION => $url,
                ]
            );

            $url                       = \XLite\Core\Converter::buildURL(
                'product',
                'preview',
                ['product_id' => $identity],
                \XLite::getCustomerScript()
            );
            $result['preview-product'] = new \XLite\View\Button\SimpleLink(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Preview product page',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'model-button link action',
                    \XLite\View\Button\Link::PARAM_BLANK    => true,
                    \XLite\View\Button\Link::PARAM_LOCATION => $url,
                ]
            );
        }

        return $result;
    }

    protected function getInventoryTrackingURL()
    {
        $identity = $this->getDataObject()->default->identity;

        return $this->buildURL(
            'product',
            '',
            [
                'product_id' => $identity,
                'page'       => 'inventory',
            ]
        );
    }
}
