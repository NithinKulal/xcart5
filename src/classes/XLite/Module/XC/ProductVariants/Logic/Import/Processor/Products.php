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
    protected $variants = array();

    /**
     * Product variants attributes
     *
     * @var array
     */
    protected $variantsAttributes = array();

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns += array(
            static::VARIANT_PREFIX . 'SKU'      => array(
                static::COLUMN_IS_MULTIROW => true,
                static::COLUMN_LENGTH      => 32,
            ),
            static::VARIANT_PREFIX . 'Price'    => array(
                static::COLUMN_IS_MULTIROW => true
            ),
            static::VARIANT_PREFIX . 'Quantity' => array(
                static::COLUMN_IS_MULTIROW => true
            ),
            static::VARIANT_PREFIX . 'Weight'   => array(
                static::COLUMN_IS_MULTIROW => true
            ),
            static::VARIANT_PREFIX . 'Image'   => array(
                static::COLUMN_IS_MULTIROW => true
            ),
            static::VARIANT_PREFIX . 'ImageAlt'   => array(
                static::COLUMN_IS_MULTIROW => true,
                static::COLUMN_LENGTH      => 255
            ),
        );

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
            + array(
                'VARIANT-PRICE-FMT'       => 'Wrong variant price format',
                'VARIANT-QUANTITY-FMT'    => 'Wrong variant quantity format',
                'VARIANT-PRODUCT-SKU-FMT' => 'SKU is already assigned to variant',
                'VARIANT-WEIGHT-FMT'      => 'Wrong variant weight format',
                'VARIANT-IMAGE-FMT'       => 'The "{{value}}" image does not exist',
            );
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
                $this->addError('VARIANT-PRODUCT-SKU-FMT', array('column' => $column, 'value' => $value));
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
                    $this->addWarning('VARIANT-PRICE-FMT', array('column' => $column, 'value' => $val));
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
                    $this->addWarning('VARIANT-QUANTITY-FMT', array('column' => $column, 'value' => $val));
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
                    $this->addWarning('VARIANT-WEIGHT-FMT', array('column' => $column, 'value' => $val));
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
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $image) {
                if (!$this->verifyValueAsEmpty($image) && !$this->verifyValueAsFile($image)) {
                    $this->addWarning('VARIANT-IMAGE-FMT', array('column' => $column, 'value' => $image));
                }
            }
        }
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
        $this->variants = $this->variantsAttributes = array();

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
        parent::importAttributesColumn($model, $value, $column);

        if ($this->multAttributes) {
            \XLite\Core\Database::getEM()->flush();

            $variantsAttributes = array();
            foreach ($this->multAttributes as $id => $values) {
                foreach ($values as $k => $v) {
                    if (1 === count($v)) {
                        $variantsAttributes[$k][$id] = array_shift($v);

                    } else {
                        unset($this->multAttributes[$id]);
                        break;
                    }
                }
            }

            if ($variantsAttributes) {
                $tmp = array();
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
                                    array(
                                        'attribute' => $attribute,
                                        'product'   => $model,
                                        'value'     => $this->normalizeValueAsBoolean($value),
                                    )
                                );

                            } else {
                                $attributeOption = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')
                                   ->findOneByNameAndAttribute($value, $attribute);
                                $values[$id] = $repo->findOneBy(
                                    array(
                                        'attribute'        => $attribute,
                                        'product'          => $model,
                                        'attribute_option' => $attributeOption,
                                    )
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
            if (isset($value[$rowIndex])) {
                $path = $value[$rowIndex];
                if ($this->verifyValueAsFile($path)) {
                    $image = $variant->getImage();
                    $isNew = false;
                    if (!$image) {
                        $isNew = true;
                        $image = new \XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image();
                    }

                    if (1 < count(parse_url($path))) {
                        $success = $image->loadFromURL($path, true);

                    } else {
                        $dir = \Includes\Utils\FileManager::getRealPath(
                            LC_DIR_VAR . $this->importer->getOptions()->dir
                        );
                        $success = $image->loadFromLocalFile($dir . LC_DS . $path);
                    }
                    if (!$success) {
                        $this->addError('PRODUCT-IMG-LOAD-FAILED', array('column' => $column, 'value' => $path));

                    } elseif ($isNew) {
                        $image->setProductVariant($variant);
                        $variant->setImage($image);
                        \XLite\Core\Database::getEM()->persist($image);
                    }
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
