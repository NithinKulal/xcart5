<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Model\QueryBuilder;

/**
 * Product query builder
 */
abstract class Product extends \XLite\Model\QueryBuilder\Product implements \XLite\Base\IDecorator
{
    /**
     * Assign attribute condition
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param mixed                  $value     Value
     */
    public function assignAttributeCondition(\XLite\Model\Attribute $attribute, $value)
    {
        $result = null;

        $alias = 'av' . $attribute->getId();
        $getConditionFunc = 'getCondition'
            . $attribute->getTypes($attribute->getType(), true);

        $where = $this->{$getConditionFunc}($attribute, $value, $alias);

        if ($where) {
            if (is_array($where)) {
                foreach ($where as $w) {
                    $this->andWhere($w);
                }

            } else {
                $this->andWhere($where);
            }

            $attr = 'attribute' . $attribute->getId();
            $this->leftJoin(
                'p.attributeValue' . $attribute->getType(),
                $alias,
                'WITH',
                $alias . '.attribute = :' . $attr
            );
            $this->setParameter($attr, $attribute);

            if ($attribute::TYPE_SELECT == $attribute->getType()) {
                $this->leftJoin($alias . '.attribute_option', $alias . 'o');
            }
        }
    }

    // {{{ Attribute condition getters

    /**
     * Return condition for text
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param mixed                  $value     Condition data
     * @param string                 $alias     Alias
     *
     * @return string
     */
    protected function getConditionText(\XLite\Model\Attribute $attribute, $value, $alias)
    {
        return '';
    }

    /**
     * Return condition for select
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param mixed                  $value     Condition data
     * @param string                 $alias     Alias
     *
     * @return string
     */
    protected function getConditionSelect(\XLite\Model\Attribute $attribute, $value, $alias)
    {
        $where = '';
        if (
            $value
            && is_array($value)
        ) {
            foreach ($value as $k => $v) {
                if (!is_numeric($v)) {
                    unset($value[$k]);
                }
            }
            if ($value) {
                $where = $alias . 'o.id IN (' . implode(',', $value) . ')';
            }
        }

        return $where;
    }

    /**
     * Return condition for checkbox
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param mixed                  $value     Condition data
     * @param string                 $alias     Alias
     *
     * @return string
     */
    protected function getConditionCheckbox(\XLite\Model\Attribute $attribute, $value, $alias)
    {
        return $value ? $alias . '.value = true' : '';
    }

    // }}}
}
