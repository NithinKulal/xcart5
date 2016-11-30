<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

/**
 * Attributes properties items list
 */
class AttributeProperty extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'product/attributes/properties.css';

        return $list;
    }


    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name'    => [
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Label',
                static::COLUMN_MAIN    => false,
                static::COLUMN_ORDERBY => 100,
            ],
            'options' => [
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Select\AttributeValue\Select',
                static::COLUMN_MAIN    => true,
                static::COLUMN_ORDERBY => 200,
            ],
        ];
    }

    /**
     * The columns are ordered according the static::COLUMN_ORDERBY values
     *
     * @return array
     */
    protected function prepareColumns()
    {
        $columns = parent::prepareColumns();

        $columns['options'][static::COLUMN_PARAMS]['product'] = $this->getProduct();

        return $columns;
    }


    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Attribute';
    }

    // {{{ Behaviors

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return false;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Defines the position MOVE widget class name
     *
     * @return string
     */
    protected function getMovePositionWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\Attribute\Move';
    }

    /**
     * Defines the position of attribute in the current product
     *
     * @param \XLite\Model\Attribute $attribute
     *
     * @return integer
     */
    protected function getPositionColumnValue(\XLite\Model\Attribute $attribute)
    {
        return $attribute->getPosition($this->getProduct());
    }

    // }}}

    /**
     * Return entities list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return $this->getProduct()->getEditableAttributes();
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return \XLite::getController()->getProduct();
    }

    /**
     * @param array                                       $column
     * @param \XLite\Model\Attribute|\XLite\Model\AEntity $entity
     *
     * @return array
     */
    protected function preprocessFieldParams(array $column, \XLite\Model\AEntity $entity)
    {
        $list = parent::preprocessFieldParams($column, $entity);

        if ($column['code'] === 'options') {
            /** @var \XLite\Model\AttributeValue\AttributeValueSelect[] $options */
            $options       = $entity->getAttributeValue($this->getProduct());
            $list['value'] = $options;
        }

        return $list;
    }

    /**
     * @param array                                       $column
     * @param \XLite\Model\Attribute|\XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isClassColumnVisible($column, $entity);
        if ($column[self::COLUMN_CODE] === 'options') {
            $result = $entity->getType() === \XLite\Model\Attribute::TYPE_SELECT;
        }

        return $result;
    }
}
