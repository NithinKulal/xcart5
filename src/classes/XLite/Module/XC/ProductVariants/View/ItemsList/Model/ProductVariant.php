<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\ItemsList\Model;

/**
 * Product variants items list
 */
class ProductVariant extends \XLite\View\ItemsList\Model\Table
{
    const COLUMN_PRODUCT_ATTRIBUTE  = 'product_attribute';

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getColumnValue(array $column, \XLite\Model\AEntity $entity)
    {
        if (isset($column[static::COLUMN_PRODUCT_ATTRIBUTE])) {
            $result = $entity->getAttributeValue($column[static::COLUMN_PRODUCT_ATTRIBUTE]);
            $result = $result ? $result->asString() : '';

        } else {
            $result = parent::getColumnValue($column, $entity);
        }

        return $result;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'image' => array(
                static::COLUMN_NAME         => '',
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\FileUploader\Image',
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\EmptyField',
                static::COLUMN_PARAMS       => array('required' => false),
                static::COLUMN_ORDERBY      => 100,
            )
        );
        $step = 200;
        foreach ($this->getVariantsAttributes() as $a) {
            $columns['attributeValue' . $a->getId()] = array(
                static::COLUMN_NAME              => $a->getName(),
                static::COLUMN_SUBHEADER         =>  static::t(
                    '{{count}} options',
                    array(
                        'count' => count($a->getAttributeValue($this->getProduct()))
                    )
                ),
                static::COLUMN_NO_WRAP           => true,
                static::COLUMN_PRODUCT_ATTRIBUTE => $a,
                static::COLUMN_CREATE_CLASS      => 'XLite\Module\XC\ProductVariants\View\FormField\Inline\Select\AttributeValues',
                static::COLUMN_PARAMS            => array(
                    'attribute' => $a,
                    'product'   => $this->getProduct(),
                ),
                static::COLUMN_ORDERBY  => $step,
            );

            $step += 100;
        }

        return $columns + array(
            'price' => array(
                static::COLUMN_NAME      => static::t('Price'),
                static::COLUMN_SUBHEADER => static::t('Default') . ': ' . $this->formatPrice($this->getProduct()->getPrice()),
                static::COLUMN_CLASS     => 'XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text\Price',
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_ORDERBY  => $step + 100,
            ),
            'sku' => array(
                static::COLUMN_NAME      => static::t('SKU'),
                static::COLUMN_SUBHEADER => static::t('Default') . ': ' . $this->getProduct()->getSku(),
                static::COLUMN_CLASS     => 'XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text\SKU',
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_ORDERBY  => $step + 200,
            ),
            'amount' => array(
                static::COLUMN_NAME      => static::t('Quantity'),
                static::COLUMN_SUBHEADER => static::t('Default') . ': '
                    . ($this->getProduct()->getInventoryEnabled() ? $this->getProduct()->getPublicAmount() : static::t('unlimited')),
                static::COLUMN_CLASS     => 'XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text\Amount',
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_ORDERBY  => $step + 300,
            ),
            'weight' => array(
                static::COLUMN_NAME      => static::t('Weight'),
                static::COLUMN_SUBHEADER => static::t('Default') . ': ' . $this->formatWeight($this->getProduct()->getWeight()),
                static::COLUMN_CLASS     => 'XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text\Weight',
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_ORDERBY  => $step + 400,
            ),
        );
    }

    /**
     * Check - has specified column attention or not
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return boolean
     */
    protected function hasColumnAttention(array $column, \XLite\Model\AEntity $entity = null)
    {
        return parent::hasColumnAttention($column, $entity)
            || ('amount' == $column[static::COLUMN_CODE] && $entity && $entity->getProduct() && $entity->isLowLimitReached());
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\ProductVariants\Model\ProductVariant';
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add variant';
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return $this->isAllowVaraintAdd() ? static::CREATE_INLINE_TOP : null;
    }

    /*
     * Get empty list template
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return 'modules/XC/ProductVariants/items_list/model/product_variant/empty.twig';
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list item as default
     *
     * @return boolean
     */
    protected function isDefault()
    {
        return true;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' product_variants';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * Get AJAX-specific URL parameters
     *
     * @return array
     */
    protected function getAJAXSpecificParams()
    {
        $params = parent::getAJAXSpecificParams();

        $params['product_id']   = $this->getProductId();
        $params['page']         = 'variants';

        return $params;
    }

    /**
     * Get title for 'default' action
     *
     * @return string
     */
    protected function getDefaultActionTitle()
    {
        return static::t('Default variant');
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array();
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->product = $this->getProduct();

        return $result;
    }

    // }}}

    // {{{ Model processing

    /**
     * @inheritdoc
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        if (\XLite\Core\Request::getInstance()->isPost()) {
            $product = $this->getProduct();
            $entity->setProduct($product);
            $product->addVariants($entity);
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    protected function undoCreatedEntity($entity)
    {
        $product = $entity->getProduct() ?: $this->getProduct();

        if ($product) {
            $product->getVariantsCollection()->removeElement($entity);
        }

        parent::undoCreatedEntity($entity);
    }

    /**
     * @inheritdoc
     */
    protected function prevalidateEntities()
    {
        if (parent::prevalidateEntities()) {
            $skus = [];

            /** @var \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity */
            foreach ($this->getPageDataForUpdate() as $entity) {
                if (in_array($entity->getSku(), $skus)) {
                    $this->errorMessages[] = static::t('SKU must be unique');
                    return false;
                } elseif ($entity->getSku() != '') {
                    $skus[] = $entity->getSku();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateNewEntity($entity);

        if ($result) {

            $attrValues = $entity->getValues();

            if ($attrValues) {

                $ids = array();
                $str = array();

                foreach ($attrValues as $av) {
                    $ids[$av->getAttribute()->getId()] = $av->getId();
                    $str[] = sprintf('%s: %s', $av->getAttribute()->getName(), $av->asString());
                }

                // Search for the same variant
                $sameVariants = $this->getProduct()->getVariantByAttributeValuesIds($ids, false);

                if (1 < count($sameVariants)) {
                    $this->errorMessages[] = static::t('Variant with specified attribute values already exists', array('list' => implode(', ', $str)));
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getCreateMessage($count)
    {
        return \XLite\Core\Translation::lbl('X variants have been created', array('count' => $count));
    }

    /**
     * @inheritdoc
     */
    protected function getUpdateMessage()
    {
        return static::t('Variants have been updated successfully');
    }

    /**
     * @inheritdoc
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $product = $entity->getProduct() ?: $this->getProduct();

        if ($product) {
            $product->getVariantsCollection()->removeElement($entity);
        }

        return parent::removeEntity($entity);
    }

    // }}}
}
