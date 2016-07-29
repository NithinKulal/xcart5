<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export\Step;

/**
 * Products
 */
class Products extends \XLite\Logic\Export\Step\Base\I18n
{
    // {{{ Data

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
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'products.csv';
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'sku'                       => array(),
            'price'                     => array(),
            'memberships'               => array(),
            'productClass'              => array(),
            'taxClass'                  => array(),
            'enabled'                   => array(),
            'weight'                    => array(),
            'shippable'                 => array(),
            'images'                    => array(),
            'imagesAlt'                 => array(),
            'arrivalDate'               => array(),
            'date'                      => array(),
            'updateDate'                => array(),
            'categories'                => array(),
            'inCategoriesPosition'       => array(),
            'inventoryTrackingEnabled'  => array(),
            'stockLevel'                => array(),
            'lowLimitEnabled'           => array(),
            'lowLimitLevel'             => array(),
            'cleanURL'                  => array(),
            'useSeparateBox'            => array(),
            'boxWidth'                  => array(),
            'boxLength'                 => array(),
            'boxHeight'                 => array(),
            'itemsPerBox'               => array(),
            'metaDescType'              => array(),
        );

        $columns += $this->assignI18nColumns(
            array(
                'name'             => array(),
                'description'      => array(),
                'briefDescription' => array(),
                'metaTags'         => array(),
                'metaDesc'         => array(),
                'metaTitle'        => array(),
            )
        );

        $columns += $this->getAttributesColumns();

        return $columns;
    }

    /**
     * Get product attributes columns
     *
     * @return array
     */
    protected function getAttributesColumns()
    {
        $columns = array();

        if ('none' !== $this->generator->getOptions()->attrs) {

            $cnd = new \XLite\Core\CommonCell();

            if ('global' === $this->generator->getOptions()->attrs) {
                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;
                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS} = null;

            } elseif ('global_n_classes' === $this->generator->getOptions()->attrs) {
                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;
            }

            $count = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd, true);

            if ($count) {
                $limit = 100;
                $start = 0;

                do {
                    $cnd->{\XLite\Model\Repo\Attribute::P_LIMIT} = array($start, $limit);
                    foreach (\XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd) as $attribute) {
                        $name = $this->getUniqueFieldName($attribute);
                        $column = $this->getAttributeColumn($attribute);
                        if (\XLite\Model\Attribute::TYPE_TEXT === $attribute->getType()) {
                            foreach ($this->getRepository()->getTranslationRepository()->getUsedLanguageCodes() as $code) {
                                $columns[$name . '_' . $code] = $column;
                            }

                        } else {
                            $columns[$name] = $column;
                        }
                    }

                    $count -= $limit;
                    $start += $limit;

                } while ($count > 0);
            }
        }

        return $columns;
    }

    /**
     * Get attribute column data
     *
     * @param \XLite\Model\Attribute $attribute Attribute object
     *
     * @return array
     */
    protected function getAttributeColumn($attribute)
    {
        return array(
            static::COLUMN_GETTER => 'getAttributeColumnValue',
            'attributeId'         => $attribute->getId(),
            'attributeName'       => $attribute->getName(),
            'attributeGroup'      => $attribute->getAttributeGroup() ? $attribute->getAttributeGroup()->getName() : '',
            'attributeIsProduct'  => (bool) $attribute->getProduct(),
            'attributeIsClass'    => (bool) $attribute->getProductClass(),
        );
    }

    /**
     * Return unique field name
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return string
     */
    protected function getUniqueFieldName(\XLite\Model\Attribute $attribute)
    {
        $result = $attribute->getName() . ' (field:';

        $cnd = new \XLite\Core\CommonCell;
        $cnd->name = $attribute->getName();

        if ($attribute->getProduct()) {
            $result .= 'product';

        } elseif ($attribute->getProductClass()) {
            $result .= 'class';

        } else {
            $result .= 'global';
        }

        if ($attribute->getAttributeGroup()) {
            $result .= ' >>> ' . $attribute->getAttributeGroup()->getName();
        }

        $result .= ')';

        return $result;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'sku' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getSkuColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'sku');
    }

    /**
     * Get column value for 'price' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPriceColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'price');
    }

    /**
     * Get column value for 'productClass' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getProductClassColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getProductClass();
    }

    /**
     * Get column value for 'memberships' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getMembershipsColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getMemberships() as $membership) {
            $result[] = $membership->getName();
        }

        return $result;
    }

    /**
     * Get column value for 'taxClass' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getTaxClassColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getTaxClass();
    }

    /**
     * Get column value for 'enabled' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getEnabledColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'enabled');
    }

    /**
     * Get column value for 'weight' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getWeightColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'weight');
    }

    /**
     * Get column value for 'freeShipping' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getShippableColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'shippable');
    }

    /**
     * Get column value for 'images' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getImagesColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getImages() as $image) {
            $result[] = $this->formatImageModel($image);
        }

        return $result;
    }

    /**
     * Get column value for 'imagesAlt' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getImagesAltColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getImages() as $image) {
            $result[] = $image->getAlt();
        }

        return $result;
    }

    /**
     * Get column value for 'arrivalDate' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getArrivalDateColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'arrivalDate');
    }

    /**
     * Format 'arrivalDate' field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatArrivalDateColumnValue($value, array $dataset, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Get column value for 'date' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getDateColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'date');
    }

    /**
     * Format 'date' field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatDateColumnValue($value, array $dataset, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Get column value for 'updateDate' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getUpdateDateColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'updateDate');
    }

    /**
     * Format 'updateDate' field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatUpdateDateColumnValue($value, array $dataset, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Get column value for 'categories' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getCategoriesColumnValue(array $dataset, $name, $i)
    {
        $result = array();
        foreach ($dataset['model']->getCategories() as $category) {
            $path = array();
            foreach ($category->getRepository()->getCategoryPath($category->getCategoryId()) as $c) {
                $path[] = $c->getName();
            }
            $result[] = implode(' >>> ', $path);
        }

        return $result;
    }

    /**
     * Get column value for 'categories' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getInCategoriesPositionColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        // N.B. There is no need to order this
        // because Model\Product#getCategories() method just wrapping Model\Product#getCategoryProducts()
        // So order is the same anyway
        foreach ($dataset['model']->getCategoryProducts() as $categoryProduct) {
            $result[] = $categoryProduct->getOrderBy();
        }

        return $result;
    }

    /**
     * Get column value for 'inventoryTrackingEnabled' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getInventoryTrackingEnabledColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getInventoryEnabled();
    }

    /**
     * Get column value for 'stockLevel' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getStockLevelColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getPublicAmount();
    }

    /**
     * Get column value for 'lowLimitEnabled' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getLowLimitEnabledColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getLowLimitEnabled();
    }

    /**
     * Get column value for 'lowLimitLevel' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getLowLimitLevelColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getLowLimitAmount();
    }

    /**
     * Get column value for 'cleanUrl' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getCleanUrlColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'cleanUrl');
    }

    /**
     * Get column value for 'useSeparateBox' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getUseSeparateBoxColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'useSeparateBox');
    }

    /**
     * Get column value for 'boxWidth' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getBoxWidthColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'boxWidth');
    }

    /**
     * Get column value for 'boxLength' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getBoxLengthColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'boxLength');
    }

    /**
     * Get column value for 'itemsPerBox' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getItemsPerBoxColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'itemsPerBox');
    }

    /**
     * Get column value for 'boxHeight' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getBoxHeightColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'boxHeight');
    }

    /**
     * Get column value for abstract 'attribute' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAttributeColumnValue(array $dataset, $name, $i)
    {
        $columns = $this->getColumns();
        $column = $columns[$name];
        $result = array();
        $attribute = !empty($dataset['attribute']) ? $dataset['attribute'] : null;

        if (null === $attribute && $column['attributeIsProduct']) {
            $attribute = $this->findProductSpecifiedAttribute($dataset['model'], $column['attributeName']);
        }

        if (null === $attribute && $column['attributeIsClass']) {
            $attribute = $this->findProductClassAttribute(
                $dataset['model'],
                $column['attributeName'],
                $column['attributeGroup']
            );
        }

        if (null === $attribute) {
            $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($column['attributeId']);
        }

        $repo = \XLite\Core\Database::getRepo(
            $attribute->getAttributeValueClass(
                $attribute->getType()
            )
        );

        if (\XLite\Model\Attribute::TYPE_TEXT === $attribute->getType()) {
            $value = $repo->findOneBy(array('product' => $dataset['model'], 'attribute' => $attribute));
            if ($value) {
                $result[] = $value->getTranslation(substr($name, -2))->getValue();
            }

        } else {
            $values = $repo->findBy(array('product' => $dataset['model'], 'attribute' => $attribute));

            if ($values) {
                $isMultiple = $attribute->isMultiple($dataset['model']);
                foreach ($values as $value) {
                    $modifiers = array();
                    if ($isMultiple) {
                        if ($value->getDefaultValue()) {
                            $modifiers[] = 'default';
                        }
                        foreach ($value->getModifiers() as $field => $modifier) {
                            $str = $value->getModifier($field);
                            if ($str) {
                                $modifiers[] .= $modifier['symbol'] . $str;
                            }
                        }
                    }

                    $result[] = $value->asString() . ($modifiers ? '=' . implode('/', $modifiers)  : '');
                }
            }
        }

        return $result;
    }

    /**
     * Returns product specified variant attribute by name
     *
     * @param \XLite\Model\Product $model Product
     * @param string               $name  Attribute name
     *
     * @return \XLite\Model\Attribute
     */
    protected function findProductSpecifiedAttribute($model, $name)
    {
        /** @var \XLite\Model\Attribute $item */
        return array_reduce($model->getAttributes()->toArray(), function ($carry, $item) use ($name) {
            return null === $carry && $name === $item->getName()
                ? $item
                : $carry;
        }, null);
    }

    /**
     * Returns product class variant attribute by name and group
     *
     * @param \XLite\Model\Product $model Product
     * @param string               $name  Attribute name
     * @param string               $group Attribute group
     *
     * @return \XLite\Model\Attribute
     */
    protected function findProductClassAttribute($model, $name, $group)
    {
        $attributes = $model->getProductClass() ? $model->getProductClass()->getAttributes()->toArray() : array();

        /** @var \XLite\Model\Attribute $item */
        return array_reduce($attributes, function ($carry, $item) use ($name, $group) {
            return null === $carry
            && $name === $item->getName()
            && $group === ($item->getAttributeGroup() ? $item->getAttributeGroup()->getName() : '')
                ? $item
                : $carry;
        }, null);
    }

    /**
     * Get column value for 'metaDescType' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getMetaDescTypeColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'metaDescType') ?: 'A';
    }

    /**
     * Copy resource
     *
     * @param \XLite\Model\Base\Storage $storage      Storage
     * @param string                    $subdirectory Subdirectory
     *
     * @return boolean
     */
    protected function copyResource(\XLite\Model\Base\Storage $storage, $subdirectory)
    {
        if ($storage instanceof \XLite\Model\Base\Image) {
            $subdirectory .= LC_DS . 'products';
        }

        return parent::copyResource($storage, $subdirectory);
    }

    // }}}
}
