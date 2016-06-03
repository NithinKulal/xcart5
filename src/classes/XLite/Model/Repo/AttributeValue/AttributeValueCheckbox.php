<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\AttributeValue;

/**
 * Attribute values repository
 */
class AttributeValueCheckbox extends \XLite\Model\Repo\AttributeValue\Multiple
{
    /**
     * Find attribute value which will be considered as a default if attribute has not specific default value
     *
     * @param array $data Data to search: array('product' => ..., 'attribute' => ...)
     *
     * @return \XLite\Model\AttributeValue\AAttributeValue
     */
    public function findDefaultAttributeValue($data)
    {
        $data['value'] = 0;

        return parent::findDefaultAttributeValue($data);
    }

    /**
     * Postprocess common
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function postprocessCommon(array $data)
    {
        $result = array();

        foreach ($data as $v) {
            if (!isset($result[$v['attrId']])) {
                $result[$v['attrId']] = array();
            }
            $val = $v[0];
            unset($val['id']);
            unset($val['value']);
            $result[$v['attrId']][$v[0]['value']] = $val;
        }

        return $result;
    }

    /**
     * Define QueryBuilder for findOneByValue() method
     *
     * @param \XLite\Model\Product   $product   Product object
     * @param \XLite\Model\Attribute $attribute Attribute object
     * @param mixed                  $value     Value
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindOneByValueQuery($product, $attribute, $value)
    {
        $qb = parent::defineFindOneByValueQuery($product, $attribute, $value);

        $qb->andWhere('av.value = :value')
            ->setParameter('value', $value);

        return $qb;
    }
}
