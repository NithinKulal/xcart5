<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\ItemsList\Model;


class AttributeOption extends \XLite\View\ItemsList\Model\AttributeOption implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     *
     * @param \XLite\Model\AttributeOption $entity
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::removeEntity($entity);

        if ($result && $entity) {
            $this->removeProductVariants($entity);
        }

        return $result;
    }

    /**
     * Remove variants based on attribute
     *
     * @param \XLite\Model\AttributeOption $option
     */
    protected function removeProductVariants($option)
    {
        $qb = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->createQueryBuilder('pv');

        $qb->select("pv.id")
            ->innerJoin(
                'pv.attributeValueS',
                'pvs',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'pvs.attribute_option = :option'
            )
            ->setParameter('option', $option);

        $ids = $qb->getResult();

        if ($ids) {
            $ids = array_map(function ($v) {
                return array_pop($v);
            }, $ids);
        } else {
            return;
        }

        $qb = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->createQueryBuilder('pvt');

        $qb->delete()
            ->where($qb->expr()->in("pvt.id", $ids));

        $qb->execute();
    }
}