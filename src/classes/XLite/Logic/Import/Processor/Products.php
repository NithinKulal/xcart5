<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Processor;

/**
 * Products import processor
 */
class Products extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Multiple attributes (cache)
     *
     * @var array
     */
    protected $multAttributes = array();

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Products imported');
    }

    /**
     * Mark all images as processed
     *
     * @return void
     */
    public function markAllImagesAsProcessed()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Image\Product\Image')->unmarkAsProcessed();
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product');
    }

    /**
     * Update model
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModel(\XLite\Model\AEntity $model, array $data)
    {
        $result = parent::updateModel($model, $data);

        if ($result) {
            if (LC_USE_CLEAN_URLS && !isset($data['cleanURL']) && !$model->getCleanURL() && $model instanceof \XLite\Model\Product) {
                $this->generateCleanURL($model);
            }

            $model->setNeedProcess(true);
        }

        return $result;
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'sku'                       => array(
                static::COLUMN_IS_KEY          => true,
                static::COLUMN_LENGTH          => 32,
            ),
            'price'                     => array(),
            'memberships'               => array(
                static::COLUMN_IS_MULTIPLE     => true
            ),
            'productClass'              => array(),
            'taxClass'                  => array(),
            'enabled'                   => array(),
            'weight'                    => array(),
            'shippable'                 => array(),
            'images'                    => array(
                static::COLUMN_IS_MULTIPLE     => true,
            ),
            'imagesAlt'                 => array(
                static::COLUMN_IS_MULTIPLE     => true,
                static::COLUMN_LENGTH          => 255,
            ),
            'arrivalDate'               => array(),
            'date'                      => array(),
            'updateDate'                => array(),
            'inventoryTrackingEnabled'  => array(),
            'stockLevel'                => array(),
            'lowLimitEnabled'           => array(),
            'lowLimitLevel'             => array(),
            'useSeparateBox'            => array(),
            'boxWidth'                  => array(),
            'boxLength'                 => array(),
            'boxHeight'                 => array(),
            'itemsPerBox'               => array(),
            'name'                      => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ),
            'categories'                => array(
                static::COLUMN_IS_MULTIPLE     => true,
            ),
            'inCategoriesPosition'       => array(
                static::COLUMN_IS_MULTIPLE     => true,
            ),
            'description'               => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
            ),
            'briefDescription'          => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
            ),
            'metaTags'                  => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ),
            'metaDesc'                  => array(
                static::COLUMN_IS_MULTILINGUAL => true,
            ),
            'metaTitle'                 => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ),
            'attributes'                => array(
                static::COLUMN_IS_MULTICOLUMN  => true,
                static::COLUMN_IS_MULTIPLE     => true,
                static::COLUMN_IS_MULTIROW     => true,
                static::COLUMN_HEADER_DETECTOR => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
            ),
            'cleanURL'                  => array(
                static::COLUMN_LENGTH          => 255,
            ),
            'metaDescType'              => array(),
        );
    }

    // }}}

    // {{{ Header detectors

    /**
     * Detect attributes header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectAttributesHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('(.+\(field:(global|product|class)([ ]*>>>.+)?\))(_([a-z]{2}))?', $row);
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
                'PRODUCT-SKU-FMT'               => 'ProductSKU is empty',
                'PRODUCT-PRICE-FMT'             => 'Wrong price format',
                'PRODUCT-ENABLED-FMT'           => 'Wrong enabled format',
                'PRODUCT-WEIGHT-FMT'            => 'Wrong weight format',
                'PRODUCT-FREE-SHIP-FMT'         => 'Wrong shippable format',
                'PRODUCT-USE-SEP-BOX-FMT'       => 'Wrong use separate box format',
                'PRODUCT-ARRIVAL-DATE-FMT'      => 'Wrong arrival date format',
                'PRODUCT-DATE-FMT'              => 'Wrong date format',
                'PRODUCT-UPDATE-DATE-FMT'       => 'Wrong update date format',
                'PRODUCT-INV-TRACKING-FMT'      => 'Wrong inventory tracking format',
                'PRODUCT-STOCK-LEVEL-FMT'       => 'Wrong stock level format',
                'PRODUCT-LOW-LIMIT-NOTIF-FMT'   => 'Wrong low stock notification format',
                'PRODUCT-LOW-LIMIT-LEVEL-FMT'   => 'Wrong low limit level format',
                'PRODUCT-NAME-FMT'              => 'The name is empty',
                'PRODUCT-BOX-WIDTH-FMT'         => 'Wrong box width format',
                'PRODUCT-BOX-LENGTH-FMT'        => 'Wrong box length format',
                'PRODUCT-BOX-HEIGHT-FMT'        => 'Wrong box height format',
                'PRODUCT-ITEMS-PRE-BOX-FMT'     => 'Wrong items per box format',
                'PRODUCT-CLEAN-URL-FMT'         => 'Wrong format of Clean URL value (allowed alpha-numeric, "_" and "-" chars)',
                'PRODUCT-IMG-LOAD-FAILED'       => 'Error of image loading. Make sure the "images" directory has write permissions.',
                'PRODUCT-IMG-URL-LOAD-FAILED'   => "Couldn't download the image {{value}} from URL",
                'PRODUCT-IMG-NOT-VERIFIED'      => 'Error of image verification ({{value}}). Make sure you have specified the correct image file or URL.',
                'PRODUCT-IN-CAT-POSITION-CNT'   => 'The count of categories specified for a product and the count of orderBy position numbers describing the position of the product within these categories must be the same.',
                'PRODUCT-CATEGORY-PATH-EMPTY'   => 'Category name should not be empty',
                'PRODUCT-IN-CAT-POSITION-FMT'   => 'OrderBy position number must be specified as a non-negative integer.',
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
        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('PRODUCT-SKU-FMT', array('column' => $column, 'value' => $value));

        } elseif (!$this->isUpdateMode()) {
            $products = \XLite\Core\Session::getInstance()->importedProductSkus;
            $products[] = $value;
            \XLite\Core\Session::getInstance()->importedProductSkus = $products;
        }
    }

    /**
     * Verify 'price' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPrice($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-PRICE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'memberships' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMemberships($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach ($value as $membership) {
                if (!$this->verifyValueAsEmpty($membership) && !$this->verifyValueAsMembership($membership)) {
                    $this->addWarning('GLOBAL-MEMBERSHIP-FMT', array('column' => $column, 'value' => $membership));
                }
            }
        }
    }

    /**
     * Verify 'product class' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyProductClass($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsProductClass($value)) {
            $this->addWarning('GLOBAL-PRODUCT-CLASS-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'tax class' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyTaxClass($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsTaxClass($value)) {
            $this->addWarning('GLOBAL-TAX-CLASS-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'enabled' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyEnabled($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-ENABLED-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'weight' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyWeight($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-WEIGHT-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'free shipping' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyShippable($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-FREE-SHIP-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'images' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyImages($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach ($value as $image) {
                if (!$this->verifyValueAsEmpty($image) && $this->verifyValueAsURL($image) && !$this->verifyValueAsFile($image)) {
                    $this->addWarning('PRODUCT-IMG-URL-LOAD-FAILED', array('column' => $column, 'value' => $value));
                } elseif (!$this->verifyValueAsEmpty($image) && !$this->verifyValueAsNull($image) && !$this->verifyValueAsFile($image)) {
                    $this->addWarning('GLOBAL-IMAGE-FMT', array('column' => $column, 'value' => $image));
                }
            }
        }
    }

    /**
     * Verify 'images alt' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyImagesAlt($value, array $column)
    {
    }

    /**
     * Verify 'arrival date' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyArrivalDate($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsDate($value)) {
            $this->addWarning('PRODUCT-ARRIVAL-DATE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'date' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyDate($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsDate($value)) {
            $this->addWarning('PRODUCT-DATE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'update date' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyUpdateDate($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsDate($value)) {
            $this->addWarning('PRODUCT-UPDATE-DATE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'categories' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyCategories($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach (array_unique($value) as $path) {
                if ($this->verifyValueAsEmpty($path)) {
                    $this->addError('PRODUCT-CATEGORY-PATH-EMPTY', array('column' => $column, 'value' => $path));
                }
                if (!$this->verifyValueAsEmpty($path) && !$this->getCategoryByPath($path)) {
                    $this->addWarning('GLOBAL-CATEGORY-FMT', array('column' => $column, 'value' => $path));
                }
            }
        }
    }

    /**
     * Verify 'categories' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyInCategoriesPosition($value, array $column)
    {
        $categoriesData = array();
        $column         = $this->getColumn('categories');
        if (isset($this->currentRowData[$column[static::COLUMN_NAME]])) {
            $categoriesData = $this->currentRowData[$column[static::COLUMN_NAME]];
        }

        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            if (is_array($categoriesData)
                && count($value) !== count($categoriesData)
            ) {
                $this->addError('PRODUCT-IN-CAT-POSITION-CNT', array('column' => $column, 'value' => $value));
            }

            foreach ($value as $inCategoryOrder) {
                if (!$this->verifyValueAsUinteger($inCategoryOrder)) {
                    $this->addError('PRODUCT-IN-CAT-POSITION-FMT', array('column' => $column, 'value' => $inCategoryOrder));
                }
            }
        }
    }

    /**
     * Verify 'inventory tracking enabled' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyInventoryTrackingEnabled($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-INV-TRACKING-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'stock level' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyStockLevel($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsUinteger($value)) {
            $this->addWarning('PRODUCT-STOCK-LEVEL-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'low limit enabled' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyLowLimitEnabled($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-LOW-LIMIT-NOTIF-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'low limit level' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyLowLimitLevel($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsUinteger($value)) {
            $this->addWarning('PRODUCT-LOW-LIMIT-LEVEL-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'clean URL' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyCleanURL($value, array $column)
    {
        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

        if (null !== $value) {
            $value = (string) $value;
            if (0 < strlen($value)
                && !preg_match('/^' . $repo->getPattern('XLite\Model\Product') . '$/S', $value)
            ) {
                $this->addError('PRODUCT-CLEAN-URL-FMT', array('column' => $column, 'value' => $value));
            }
        }
    }

    /**
     * Verify 'name' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyName($value, array $column)
    {
        $value = $this->getDefLangValue($value);
        if ($this->verifyValueAsEmpty($value) && !$this->isProductExists()) {
            $this->addError('PRODUCT-NAME-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Return true if product exists
     *
     * @return boolean
     */
    protected function isProductExists()
    {
        $result = false;

        $sku = isset($this->currentRowData['sku']) ? $this->currentRowData['sku'] : '';

        if (!\XLite\Core\Converter::isEmptyString($sku)) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBy(array('sku' => $sku));
        }

        return !empty($result);
    }

    /**
     * Verify 'description' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyDescription($value, array $column)
    {
    }

    /**
     * Verify 'brief description' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyBriefDescription($value, array $column)
    {
    }

    /**
     * Verify 'meta tags' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaTags($value, array $column)
    {
    }

    /**
     * Verify 'meta desc' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaDesc($value, array $column)
    {
    }

    /**
     * Verify 'meta title' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaTitle($value, array $column)
    {
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
    }

    /**
     * Verify 'use separate box' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyUseSeparateBox($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-USE-SEP-BOX-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'boxWidth' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyBoxWidth($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-BOX-WIDTH-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'boxLength' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyBoxLength($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-BOX-LENGTH-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'boxHeight' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyBoxHeight($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-BOX-HEIGHT-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'itemsPerBox' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyItemsPerBox($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsUinteger($value)) {
            $this->addWarning('PRODUCT-ITEMS-PRE-BOX-FMT', array('column' => $column, 'value' => $value));
        }
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'sku' value
     *
     * @param mixed @value Value
     *
     * @return string
     */
    protected function normalizeSkuValue($value)
    {
        return $this->normalizeValueAsString($value);
    }

    /**
     * Normalize 'price' value
     *
     * @param mixed @value Value
     *
     * @return float
     */
    protected function normalizePriceValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    /**
     * Normalize 'product class' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\ProductClass
     */
    protected function normalizeProductClassValue($value)
    {
        return $this->normalizeValueAsProductClass($value);
    }

    /**
     * Normalize 'tax class' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\TaxClass
     */
    protected function normalizeTaxClassValue($value)
    {
        return $this->normalizeValueAsTaxClass($value);
    }

    /**
     * Normalize 'enabled' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeEnabledValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'weight' value
     *
     * @param mixed @value Value
     *
     * @return float
     */
    protected function normalizeWeightValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    /**
     * Normalize 'free shipping' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeShippableValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'arrival date' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeArrivalDateValue($value)
    {
        return $this->normalizeValueAsDate($value);
    }

    /**
     * Normalize 'date' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeDateValue($value)
    {
        return $this->normalizeValueAsDate($value);
    }

    /**
     * Normalize 'update date' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeUpdateDateValue($value)
    {
        return $this->normalizeValueAsDate($value);
    }

    /**
     * Normalize 'use separate box' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeUseSeparateBoxValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'boxWidth' value
     *
     * @param mixed @value Value
     *
     * @return float
     */
    protected function normalizeBoxWidthValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    /**
     * Normalize 'boxLength' value
     *
     * @param mixed @value Value
     *
     * @return float
     */
    protected function normalizeBoxLengthValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    /**
     * Normalize 'boxHeight' value
     *
     * @param mixed @value Value
     *
     * @return float
     */
    protected function normalizeBoxHeightValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    /**
     * Normalize 'itemsPerBox' value
     *
     * @param mixed @value Value
     *
     * @return float
     */
    protected function normalizeItemsPerBoxValue($value)
    {
        return $this->normalizeValueAsUinteger($value);
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
        $this->getRepository()->setBlockQuickDataFlag(true);

        return parent::importData($data);
    }

    /**
     * Import 'memberships' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importMembershipsColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        if ($value) {
            if ($model->getMemberships()) {
                foreach ($model->getMemberships() as $membership) {
                    $membership->getProducts()->removeElement($model);
                }
                $model->getMemberships()->clear();
            }
            if (!$this->verifyValueAsNull($value)) {
                foreach ($value as $membership) {
                    $membership = $this->normalizeValueAsMembership($membership);
                    if ($membership && !$this->isAlreadyProductBindedWithMembership($model, $membership)) {
                        $model->addMemberships($membership);
                        $membership->addProduct($model);
                        $this->bindProductWithMembership($model, $membership);
                    }
                }
            }
        }
    }

    /**
     * Check if product binded with membership
     *
     * @param \XLite\Model\Product      $model          Product
     * @param \XLite\Model\Membership   $membership     Membership
     *
     * @return boolean
     */
    protected function isAlreadyProductBindedWithMembership(\XLite\Model\Product $model, \XLite\Model\Membership $membership)
    {
        $key = 'XLite\Model\Membership|Product';

        return isset($this->modelsLocalCache[$key][$model->getProductId()])
            && is_array($this->modelsLocalCache[$key][$model->getProductId()])
            && in_array(
                $membership->getMembershipId(),
                $this->modelsLocalCache[$key][$model->getProductId()]
            );
    }

    /**
     * Add bind to local cache
     *
     * @param \XLite\Model\Product      $model          Product
     * @param \XLite\Model\Membership   $membership     Membership
     *
     * @return void
     */
    protected function bindProductWithMembership(\XLite\Model\Product $model, \XLite\Model\Membership $membership)
    {
        $key = 'XLite\Model\Membership|Product';

        if (!isset($this->modelsLocalCache[$key])) {
            $this->modelsLocalCache[$key] = array();
        }
        $productId = $model->getProductId();
        if (!isset($this->modelsLocalCache[$key][$productId])) {
            $this->modelsLocalCache[$key][$productId] = array();
        }

        $this->modelsLocalCache[$key][$productId][] = $membership->getMembershipId();
    }

    /**
     * Import 'categories' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importCategoriesColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if ($value) {
            if (!$this->verifyValueAsNull($value)) {
                $position = array();
                foreach ($model->getCategoryProducts() as $link) {
                    $position[$link->getCategory()->getCategoryId()] = $link->getOrderby();
                }
            }

            \XLite\Core\Database::getRepo('\XLite\Model\CategoryProducts')->deleteInBatch(
                $model->getCategoryProducts()->toArray()
            );

            $model->getCategoryProducts()->clear();

            if (!$this->verifyValueAsNull($value)) {
                foreach (array_unique($value) as $path) {
                    $category = $this->addCategoryByPath($path);
                    $link  = \XLite\Core\Database::getRepo('\XLite\Model\CategoryProducts')->findOneBy(
                        array(
                            'category' => $category,
                            'product'  => $model,
                        )
                    );
                    if (!$link) {
                        $link = new \XLite\Model\CategoryProducts;
                        $link->setProduct($model);
                        $link->setCategory($category);
                        if (isset($position[$category->getCategoryId()])) {
                            $link->setOrderby($position[$category->getCategoryId()]);
                        }
                        $model->addCategoryProducts($link);
                        \XLite\Core\Database::getEM()->persist($link);
                    }
                }
            }
        }
    }

    /**
     * Import 'inCategoriesOrderBy' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importInCategoriesPositionColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if ($value && !$this->verifyValueAsNull($value)) {
            $categoryProducts = $model->getCategoryProducts();

            if (count($value) === count($categoryProducts)) {
                $i = 0;
                foreach ($categoryProducts as $categoryProduct) {
                    $categoryProduct->setOrderby($value[$i++]);
                }

            } else {
                $this->addError('PRODUCT-IN-CAT-POSITION-CNT', array('column' => $column, 'value' => $value));
            }
        }
    }
    /**
     * Import 'inventory tracking enabled' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importInventoryTrackingEnabledColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setInventoryEnabled($this->normalizeValueAsBoolean($value));
    }

    /**
     * Import 'stock level' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importStockLevelColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && $this->verifyValueAsUinteger($value)) {
            // Update quantity only if $value is non-empty integer value
            $model->setAmount(abs((int) $value));
        }
    }

    /**
     * Import 'low limit enabled' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importLowLimitEnabledColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setLowLimitEnabled($this->normalizeValueAsBoolean($value));
    }

    /**
     * Import 'low limit level' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importLowLimitLevelColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setLowLimitAmount(abs((int) $value));
    }

    /**
     * Import 'images' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importImagesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach ($value as $index => $path) {
                $file = $this->verifyValueAsLocalURL($path) ? $this->getLocalPathFromURL($path) : $path;
                if ($this->verifyValueAsFile($file)) {
                    $image = null;

                    if ($model->getImages()) {
                        $filtered = $model->getImages()->filter($this->getImageFilter($file));
                        $image = ! $filtered->isEmpty() ? $filtered->first() : null;
                        $readable = \Includes\Utils\FileManager::isReadable(LC_DIR_ROOT . $file);
                    }

                    if (!$image || !$readable) {
                        $image = new \XLite\Model\Image\Product\Image();

                        if ($this->verifyValueAsURL($file)) {
                            $success = $image->loadFromURL($path, true);

                        } else {
                            $success = $image->loadFromLocalFile(LC_DIR_ROOT . $file);
                        }

                        if (!$success) {
                            if ($image->getLoadError() === 'unwriteable') {
                                $this->addError('PRODUCT-IMG-LOAD-FAILED', array('column' => $column, 'value' => $path));
                            } elseif ($image->getLoadError()) {
                                $this->addWarning('PRODUCT-IMG-URL-LOAD-FAILED', array('column' => $column, 'value' => $path));
                            }

                        } else {
                            $image->setNeedProcess(1);
                            $image->setProduct($model);
                            $model->getImages()->add($image);
                            \XLite\Core\Database::getEM()->persist($image);
                        }
                    }
                } elseif(!$this->verifyValueAsFile($file) && $this->verifyValueAsURL($file)) {
                    $this->addWarning('PRODUCT-IMG-URL-LOAD-FAILED', array('column' => $column, 'value' => $path));
                } else {
                    $this->addWarning('PRODUCT-IMG-NOT-VERIFIED', array('column' => $column, 'value' => $path));
                }
            }
        }

        if ($value || $this->verifyValueAsNull($value)) {
            $remove = $model->getImages()->filter($this->getImageRejectFilter($value));
            foreach ($remove as $image) {
                $model->getImages()->removeElement($image);
                \XLite\Core\Database::getEM()->remove($image);
            }
        }
    }

    /**
     * Import 'images alt' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importImagesAltColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        if ($value) {
            foreach ($value as $index => $alt) {
                $image = $model->getImages()->get($index);
                if ($image) {
                    $image->setAlt($alt);
                }
            }
        }
        if ($this->verifyValueAsNull($value)) {
            foreach ($model->getImages() as $image) {
                $image->setAlt('');
            }
        }
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
        $this->multAttributes = array();
        foreach ($value as $attr => $v) {
            if (preg_match('/(.+)\(field:(global|product|class)([ ]*>>>[ ]*(.+))?\)(_([a-z]{2}))?/iSs', $attr, $m)) {
                $type = $m[2];
                $name = trim($m[1]);
                $lngCode = isset($m[6]) ? $m[6] : null;
                $productClass = 'class' === $type
                    ? $model->getProductClass()
                    : null;
                $attributeGroup = isset($m[4]) && 'product' !== $type
                    ? $this->normalizeValueAsAttributeGroup($m[4], $productClass)
                    : null;
                $product = 'product' === $type
                    ? $model
                    : null;

                $values = array();
                foreach ($v as $value) {
                    $values = array_merge($values, $value);
                }
                $values = array_values(array_unique($values));
                $shouldClear = $this->verifyValueAsNull($values);
                $data = array(
                    'value'    => array(),
                    'default'  => array(),
                    'price'    => array(),
                    'weight'   => array(),
                );
                $hasOptions = false;
                foreach ($values as $k => $value) {
                    if (preg_match('/(.+)=(default)?(\/)?((w|\$)(([+-]?\d+\.?\d*)(%)?))?(\/)?((w|\$)(([+-]?\d+\.?\d*)(%)?))?/iSs', $value, $m)) {
                        $data['value'][$k] = $m[1];
                        if (isset($m[2]) && 'default' === $m[2]) {
                            $data['default'][$k] = true;
                        }
                        $hasOptions = true;
                        foreach (array(5, 11) as $id) {
                            if (isset($m[$id])) {
                                $data['$' === $m[$id] ? 'price' : 'weight'][$k] = $m[$id + 1];
                            }
                        }

                    } else {
                        $data['value'][$k] = $value;
                    }
                }
                $data['multiple'] = 1 < count($data['value']);

                $cnd = new \XLite\Core\CommonCell();

                if ($product && $product->getId()) {
                    $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = $product;

                } else {
                    $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;
                }

                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS}   = $productClass;
                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_ATTRIBUTE_GROUP} = $attributeGroup;
                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_NAME}            = $name;

                $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd);

                if ($attribute) {
                    $attribute = $attribute[0];

                } else {
                    $type = !$data['multiple'] && !$hasOptions
                        ? \XLite\Model\Attribute::TYPE_TEXT
                        : \XLite\Model\Attribute::TYPE_SELECT;
                    if (1 === count($data['value']) || 2 === count($data['value'])) {
                        $isCheckbox = true;
                        foreach ($data['value'] as $val) {
                            $isCheckbox = $isCheckbox && $this->verifyValueAsBoolean($val);
                        }
                        if ($isCheckbox) {
                            $type = \XLite\Model\Attribute::TYPE_CHECKBOX;
                        }
                    }
                    $attribute = \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->insert(
                        array(
                            'name'           => $name,
                            'productClass'   => $productClass,
                            'attributeGroup' => $attributeGroup,
                            'product'        => $product,
                            'type'           => $type,
                        )
                    );

                    if ($attributeGroup && $productClass) {
                        $attributeGroup->setProductClass($productClass);
                    }
                }

                if ($data['multiple']) {
                    $this->multAttributes[$attribute->getId()] = $v;
                }

                $data['ignoreIds'] = true;

                if ($lngCode) {
                    $oldCode = $this->importer->getLanguageCode();
                    $this->importer->setLanguageCode($lngCode);
                }

                if ($attribute->getType() === \XLite\Model\Attribute::TYPE_CHECKBOX) {
                    foreach ($data['value'] as $k => $val) {
                        $data['value'][$k] = $this->normalizeValueAsBoolean($val);
                    }
                }
                if ($shouldClear) {
                    $attribute->setAttributeValue($model, null);
                } else {
                    $attribute->setAttributeValue($model, $data);
                }

                if ($lngCode) {
                    \XLite\Core\Database::getEM()->flush();
                    $this->importer->setLanguageCode($oldCode);
                }
            }
        }
    }

    /**
     * Import 'cleanURL' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importCleanURLColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if (LC_USE_CLEAN_URLS || !empty($value)) {
            $this->generateCleanURL($model, $value);
        }
    }

    /**
     * Generate clean URL
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value OPTIONAL
     *
     * @return void
     */
    protected function generateCleanURL(\XLite\Model\Product $model, $value = '')
    {
        if (\XLite\Core\Converter::isEmptyString($value)) {
            if (!\XLite\Core\Converter::isEmptyString($this->currentRowData['name'])) {
                // Input cleanURL value is empty, trying to get product name from current row data

                $lngCodes = array_unique(
                    array(
                        'en',
                        $this->importer->getLanguageCode(),
                    )
                );

                foreach ($lngCodes as $code) {
                    if (!empty($this->currentRowData['name'][$code])) {
                        $value = $this->currentRowData['name'][$code];
                        break;
                    }
                }
            }

            if (\XLite\Core\Converter::isEmptyString($value)) {
                // Try to get value from current product name
                $value = $model->getName();
            }
        } else {
            $value = preg_replace('/\.html$/', '', $value);
        }

        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');
        $value = $repo->generateCleanURL($model, $value);

        if (!\XLite\Core\Converter::isEmptyString($value)) {
            $this->updateCleanURL($model, $value);
        }
    }
}
