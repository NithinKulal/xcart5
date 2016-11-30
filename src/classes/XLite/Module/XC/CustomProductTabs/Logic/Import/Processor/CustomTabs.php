<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Logic\Import\Processor;

/**
 * Product tabs import processor
 */
class CustomTabs extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Check - specified file is imported by this processor or not
     *
     * @param \SplFileInfo $file File
     *
     * @return boolean
     */
    protected function isImportedFile(\SplFileInfo $file)
    {
        return 0 === strpos($file->getFilename(), 'product-custom-tabs');
    }

    /**
     * Get import file name format
     *
     * @return string
     */
    public function getFileNameFormat()
    {
        return 'product-custom-tabs.csv';
    }

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Tabs imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\Tab');
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_IS_KEY          => true,
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'content' => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
            ],
            'enabled' => [],
            'position' => [],
            'product' => [
                static::COLUMN_IS_KEY          => true,
            ],
        ];
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
            'TABS-PRODUCT-FMT'  => 'The product with "{{value}}" SKU does not exist',
            'TABS-NAME-FMT'     => 'The name is empty',
            'TABS-ENABLED-FMT'  => 'Wrong enabled format',
            'TABS-POSITION-FMT' => 'Wrong position format',
        );
    }

    /**
     * Get error texts
     *
     * @return array
     */
    public static function getErrorTexts()
    {
        return parent::getErrorTexts()
        + array(
            'ATTR-GROUP-FMT'    => 'New group will be created',
        );
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
        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('TABS-NAME-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'content' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyContent($value, array $column)
    {
    }

    /**
     * Verify 'position' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPosition($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsUinteger($value)) {
            $this->addWarning('TABS-POSITION-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'product' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyProduct($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsProduct($value)) {
            $this->addWarning('TABS-PRODUCT-FMT', array('column' => $column, 'value' => $value));
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
    protected function verifyTabsEnabled($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('TABS-ENABLED-FMT', array('column' => $column, 'value' => $value));
        }
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'position' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizePositionValue($value)
    {
        return $this->normalizeValueAsUinteger($value);
    }

    /**
     * Normalize 'position' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeEnabledValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'product' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\ProductClass
     */
    protected function normalizeProductValue($value)
    {
        return $this->normalizeValueAsProduct($value);
    }

    // }}}

    // {{{ Import

    /**
     * Get tab by default lang name
     *
     * @param \XLite\Model\Product $model
     * @param                      $name
     *
     * @return null | \XLite\Module\XC\CustomProductTabs\Model\Product\Tab
     */
    protected function getTabByName(\XLite\Model\Product $model, $name)
    {
        foreach ($model->getTabs() as $tab) {
            if ($tab->getName() === $name) {
                return $tab;
            }
        }

        return null;
    }

    // }}}
}