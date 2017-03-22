<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Logic\Import\Processor;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    const VARIANT_PREFIX = 'variant';

    /**
     * Product variants
     *
     * @var array
     */
    protected $variants = [];

    /**
     * Product variants attributes
     *
     * @var array
     */
    protected $variantsAttributes = [];

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns += [
            static::VARIANT_PREFIX . 'SKU'      => [
                static::COLUMN_IS_MULTIROW => true,
                static::COLUMN_LENGTH      => 32,
            ],
            static::VARIANT_PREFIX . 'Price'    => [
                static::COLUMN_IS_MULTIROW => true
            ],
            static::VARIANT_PREFIX . 'Quantity' => [
                static::COLUMN_IS_MULTIROW => true
            ],
            static::VARIANT_PREFIX . 'Weight'   => [
                static::COLUMN_IS_MULTIROW => true
            ],
            static::VARIANT_PREFIX . 'Image'   => [
                static::COLUMN_IS_MULTIROW => true
            ],
            static::VARIANT_PREFIX . 'ImageAlt'   => [
                static::COLUMN_IS_MULTIROW => true,
                static::COLUMN_LENGTH      => 255
            ],
        ];

        return $columns;
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'VARIANT-PRICE-FMT'       => 'Wrong variant price format',
                'VARIANT-QUANTITY-FMT'    => 'Wrong variant quantity format',
                'VARIANT-PRODUCT-SKU-FMT' => 'SKU is already assigned to variant',
                'VARIANT-WEIGHT-FMT'      => 'Wrong variant weight format',
                'VARIANT-IMAGE-FMT'       => 'The "{{value}}" image does not exist',
                'VARIANT-ATTRIBUTE-FMT'   => 'Variant attribute "{{column}}" cannot be empty',
        ];
    }

    /**
     * Verify 'attributes' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyAttributes($value, array $column)
    {
        parent::verifyAttributes($value, $column);

        if (is_array($value)) {
            foreach ($value as $name => $attribute) {
                if ($this->isVariantValues($attribute)) {
                    foreach ($attribute as $offset => $line) {
                        foreach ($line as $val) {
                            if (empty($val)) {
                                $this->addError(
                                    'VARIANT-ATTRIBUTE-FMT',
                                    [
                                        'column' => array_merge($column, [static::COLUMN_NAME => $name]),
                                        'value' => $attribute
                                    ],
                                    $offset + 1 - $this->rowStartIndex
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if attribute column multiline(is variants)
     *
     * @param $attribute
     *
     * @return bool
     */
    protected function isAttributeRowMultiline($attribute)
    {
        $attribute = array_slice($attribute, 1);

        foreach ($attribute as $line) {
            foreach ($line as $value) {
                if (!empty($value)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verify 'SKU' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifySku($value, array $column)
    {
        parent::verifySku($value, $column);

        if (!$this->verifyValueAsEmpty($value)) {
            $entity = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                ->findOneBySku($value);

            if ($entity) {
                $this->addError('VARIANT-PRODUCT-SKU-FMT', ['column' => $column, 'value' => $value]);
            }
        }
    }

    /**
     * Verify 'variantSKU' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyVariantSKU($value, array $column)
    {
    }

    /**
     * Verify 'variantPrice' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyVariantPrice($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('VARIANT-PRICE-FMT', ['column' => $column, 'value' => $val]);
                }
            }
        }
    }

    /**
     * Verify 'variantQuantity' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyVariantQuantity($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('VARIANT-QUANTITY-FMT', ['column' => $column, 'value' => $val]);
                }
            }
        }
    }

    /**
     * Verify 'variantWeight' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyVariantWeight($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('VARIANT-WEIGHT-FMT', ['column' => $column, 'value' => $val]);
                }
            }
        }
    }

    /**
     * Verify 'image' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyVariantImage($value, array $column)
    {
        parent::verifyImages($value, $column);
    }

    // }}}

    // {{{ Import

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        $this->variants = $this->variantsAttributes = [];

        return parent::importData($data);
    }

    /**
     * Import 'attributes' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAttributesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        foreach ($value as $k => $v) {
            if (!$this->isVariantValues($v)) {
                $value[$k] = array_splice($v, 0, 1);
            }
        }

        parent::importAttributesColumn($model, $value, $column);

        if ($this->multAttributes) {
            \XLite\Core\Database::getEM()->flush();

            $variantsAttributes = [];
            foreach ($this->multAttributes as $id => $values) {
                if ($this->isVariantValues($values)) {
                    foreach ($values as $k => $v) {
                        $variantsAttributes[$k][$id] = array_shift($v);
                    }
                } else {
                    unset($this->multAttributes[$id]);
                    continue;
                }
            }

            if ($variantsAttributes) {
                $tmp = [];
                foreach ($variantsAttributes as $k => $v) {
                    $tmp[$k] = implode('::', $v);
                }
                if (count($tmp) === count($variantsAttributes)) {
                    foreach ($variantsAttributes as $rowIndex => $values) {
                        foreach ($values as $id => $value) {
                            if (!isset($this->variantsAttributes[$id])) {
                                $this->variantsAttributes[$id] = \XLite\Core\Database::getRepo('XLite\Model\Attribute')
                                    ->find($id);
                            }
                            $attribute = $this->variantsAttributes[$id];

                            $repo = \XLite\Core\Database::getRepo($attribute->getAttributeValueClass($attribute->getType()));
                            if ($attribute::TYPE_CHECKBOX == $attribute->getType()) {
                                $values[$id] = $repo->findOneBy(
                                    [
                                        'attribute' => $attribute,
                                        'product'   => $model,
                                        'value'     => $this->normalizeValueAsBoolean($value),
                                    ]
                                );

                            } else {
                                $attributeOption = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')
                                   ->findOneByNameAndAttribute($value, $attribute);
                                $values[$id] = $repo->findOneBy(
                                    [
                                        'attribute'        => $attribute,
                                        'product'          => $model,
                                        'attribute_option' => $attributeOption,
                                    ]
                                );
                            }

                        }

                        $variant = $model->getVariantByAttributeValues($values);

                        if (!$variant) {
                            $variant = new \XLite\Module\XC\ProductVariants\Model\ProductVariant();
                            foreach ($values as $attributeValue) {
                                $method = 'addAttributeValue' . $attributeValue->getAttribute()->getType();
                                $variant->$method($attributeValue);
                                $attributeValue->addVariants($variant);
                            }
                            $variant->setProduct($model);
                            $model->addVariants($variant);
                            \XLite\Core\Database::getEM()->persist($variant);
                        }

                        $this->variants[$rowIndex] = $variant;
                    }
                }

                foreach ($model->getVariantsAttributes() as $va) {
                    $model->getVariantsAttributes()->removeElement($va);
                    $va->getVariantsProducts()->removeElement($model);
                }

                foreach ($this->variantsAttributes as $va) {
                    $model->addVariantsAttributes($va);
                    $va->addVariantsProducts($model);
                }

            }

            $model->assignDefaultVariant();
        }
    }

    /**
     * Check if values belong to variant(1 val for each row)
     *
     * @param array $values
     *
     * @return bool
     */
    protected function isVariantValues(array $values)
    {
        foreach ($values as $k => $value) {
            if (!is_array($value) || count($value) > 1 || !array_filter($value, function ($v) {
                    return $v !== '';
                })) {
                return false;
            }
        }

        return true;
    }

    /**
     * Import 'variantSKU' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantSKUColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setSku(isset($value[$rowIndex]) ? $value[$rowIndex] : '');
        }
    }

    /**
     * Import 'variantPrice' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantPriceColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setPrice($this->normalizeValueAsFloat(isset($value[$rowIndex]) ? $value[$rowIndex] : 0));
            $variant->setDefaultPrice(!isset($value[$rowIndex]));
        }
    }

    /**
     * Import 'variantQuantity' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantQuantityColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setAmount($this->normalizeValueAsUinteger(isset($value[$rowIndex]) ? $value[$rowIndex] : 0));
            $variant->setDefaultAmount(!isset($value[$rowIndex]));
        }
    }

    /**
     * Import 'variantWeight' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantWeightColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setWeight($this->normalizeValueAsFloat(isset($value[$rowIndex]) ? $value[$rowIndex] : 0));
            $variant->setDefaultWeight(!isset($value[$rowIndex]));
        }
    }

    /**
     * Import 'variantImage' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantImageColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            if ($this->verifyValueAsNull($value[$rowIndex])) {
                $image = $variant->getImage();
                if ($image) {
                    \XLite\Core\Database::getEM()->remove($image);
                }
                $variant->setImage(null);

            } elseif (isset($value[$rowIndex]) && !$this->verifyValueAsEmpty($value[$rowIndex])) {
                $path = $value[$rowIndex];
                $file = $this->verifyValueAsLocalURL($path) ? $this->getLocalPathFromURL($path) : $path;
                if ($this->verifyValueAsFile($file)) {
                    $image = $variant->getImage();
                    $isNew = false;
                    if (!$image) {
                        $isNew = true;
                        $image = new \XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image();
                    }

                    if ($this->verifyValueAsURL($file)) {
                        $success = $image->loadFromURL($file, true);

                    } else {
                        $success = $image->loadFromLocalFile(LC_DIR_ROOT . $file);
                    }

                    if (!$success) {
                        if ($image->getLoadError() === 'unwriteable') {
                            $this->addError('PRODUCT-IMG-LOAD-FAILED', [
                                'column' => $column,
                                'value' => $this->verifyValueAsURL($file) ? $path : LC_DIR_ROOT . $file
                            ]);
                        } elseif ($image->getLoadError()) {
                            $this->addError('PRODUCT-IMG-URL-LOAD-FAILED', [
                                'column' => $column,
                                'value' => $this->verifyValueAsURL($file) ? $path : LC_DIR_ROOT . $file
                            ]);
                        }
                    } elseif ($isNew) {
                        $image->setProductVariant($variant);
                        $variant->setImage($image);
                        \XLite\Core\Database::getEM()->persist($image);
                    }

                } elseif(!$this->verifyValueAsFile($file) && $this->verifyValueAsURL($file)) {
                    $this->addWarning('PRODUCT-IMG-URL-LOAD-FAILED', [
                        'column' => $column,
                        'value' => $path
                    ]);
                } else {
                    $this->addWarning('PRODUCT-IMG-NOT-VERIFIED', [
                        'column' => $column,
                        'value' => $this->verifyValueAsURL($file) ? $path : LC_DIR_ROOT . $file
                    ]);
                }
            }
        }
    }

    /**
     * Import 'image alt' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantImageAltColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            if (isset($value[$rowIndex])) {
                $alt = $value[$rowIndex];
                $image = $variant->getImage();
                if ($image) {
                    $image->setAlt($alt);
                }
            }
        }
    }

    // }}}
}
