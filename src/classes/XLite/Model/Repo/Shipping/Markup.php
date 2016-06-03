<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Shipping;

/**
 * Shipping method model
 */
class Markup extends \XLite\Model\Repo\ARepo
{
    /**
     * Search parameters
     */
    const P_METHOD_ID     = 'methodId';
    const P_SHIPPING_ZONE = 'shippingZone';
    

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;


    /**
     * Returns shipping markups for order modifier by specified processor
     *
     * @param string                               $processor Processor class name
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier  Shipping order modifier
     *
     * @return array
     */
    public function findMarkupsByProcessor($processor, \XLite\Logic\Order\Modifier\Shipping $modifier)
    {
        $result = array();

        $address = \XLite\Model\Shipping::getInstance()->getDestinationAddress($modifier);

        $customerZones = array();

        if (null !== $address) {
            // Get customer zone sorted out by weight
            $customerZones = \XLite\Core\Database::getRepo('XLite\Model\Zone')
                ->findApplicableZones($address);
        }

        // Iterate through zones and generate markups list
        foreach ($customerZones as $zone) {
            $markups = $this->defineFindMarkupsByProcessorQuery($processor, $modifier, $zone->getZoneId())->getResult();

            foreach ($markups as $markupData) {
                $markup = $markupData[0];

                if ($markup->getShippingMethod() && !isset($result[$markup->getShippingMethod()->getMethodId()])) {
                    $markup->setMarkupValue($markupData['markup_value']);
                    $result[$markup->getShippingMethod()->getMethodId()] = $markup;
                }
            }
        }

        return $result;
    }

    /**
     * findMarkupsByZoneAndMethod
     *
     * @param integer $zoneId   Zone Id OPTIONAL
     * @param integer $methodId Method Id OPTIONAL
     *
     * @return array
     */
    public function findMarkupsByZoneAndMethod($zoneId = null, $methodId = null)
    {
        return $this->defineFindMarkupsByZoneAndMethodQuery($zoneId, $methodId)->getResult();
    }

    /**
     * Get markups by specified set of its id
     *
     * @param array $ids Array of markup Id
     *
     * @return array
     */
    public function findMarkupsByIds($ids)
    {
        return $this->defineFindMarkupsByIdsQuery($ids)->getResult();
    }


    /**
     * Adds markup condition to the query builder object
     *
     * @param \Doctrine\ORM\QueryBuilder           $qb       Query builder object
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier Shipping order modifier
     * @param integer                              $zoneId   Zone Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addMarkupCondition(
        \Doctrine\ORM\QueryBuilder $qb,
        \XLite\Logic\Order\Modifier\Shipping $modifier,
        $zoneId
    ) {
        $prepareSum = array(
            'm.markup_flat',
            '(m.markup_percent * :value / 100)',
            '(m.markup_per_item * :items)',
            '(m.markup_per_weight * :weight)'
        );

        $qb->addSelect(implode(' + ', $prepareSum) . ' as markup_value')
            ->linkInner('m.zone')
            ->andWhere('zone.zone_id = :zoneId')
            ->setParameter('zoneId', $zoneId)
            ->setParameter('weight', $modifier->getWeight())
            ->setParameter('items', $modifier->countItems())
            ->setParameter('value', $modifier->getSubtotal());

        $qb->linkInner('m.shipping_method');

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    'shipping_method.tableType = :WSIType',
                    'm.min_weight <= :weightCondition',
                    'm.max_weight >= :weightCondition',
                    'm.min_total <= :totalCondition',
                    'm.max_total >= :totalCondition',
                    'm.min_items <= :itemsCondition',
                    'm.max_items >= :itemsCondition'
                ),
                $qb->expr()->andX(
                    'shipping_method.tableType = :WType',
                    'm.min_weight <= :weightCondition',
                    'm.max_weight >= :weightCondition'
                ),
                $qb->expr()->andX(
                    'shipping_method.tableType = :SType',
                    'm.min_total <= :totalCondition',
                    'm.max_total >= :totalCondition'
                ),
                $qb->expr()->andX(
                    'shipping_method.tableType = :IType',
                    'm.min_items <= :itemsCondition',
                    'm.max_items >= :itemsCondition'
                )
            )
        );

        $qb->setParameter('totalCondition', $modifier->getSubtotalCondition())
            ->setParameter('weightCondition', $modifier->getWeightCondition())
            ->setParameter('itemsCondition', $modifier->countItemsCondition())
            ->setParameter('WSIType', 'WSI')
            ->setParameter('WType', 'W')
            ->setParameter('SType', 'S')
            ->setParameter('IType', 'I');

        return $qb;
    }

    /**
     * Define query builder object for findMarkupsByProcessor()
     *
     * @param string                               $processor Processor class name
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier  Shipping order modifier
     * @param integer                              $zoneId    Zone Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindMarkupsByProcessorQuery(
        $processor,
        \XLite\Logic\Order\Modifier\Shipping $modifier,
        $zoneId
    ) {
        $qb = $this->createQueryBuilder('m')
            ->addSelect('shipping_method')
            ->linkInner('m.shipping_method')
            ->andWhere('shipping_method.processor = :processor')
            ->andWhere('shipping_method.enabled = 1')
            ->setParameter('processor', $processor);

        return $this->addMarkupCondition($qb, $modifier, $zoneId);
    }

    /**
     * defineFindMarkupsByZoneAndMethodQuery
     *
     * @param integer $zoneId   Zone Id
     * @param integer $methodId Method Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindMarkupsByZoneAndMethodQuery($zoneId, $methodId)
    {
        $qb = $this->createQueryBuilder('m')
            ->addSelect('sm')
            ->innerJoin('m.shipping_method', 'sm');

        if (null !== $zoneId) {
            $qb->innerJoin('m.zone', 'zone')
                ->andWhere('zone.zone_id = :zoneId')
                ->setParameter('zoneId', $zoneId);
        }

        if (null !== $methodId) {
            $qb->innerJoin('m.shipping_method', 'shipping_method')
                ->andWhere('shipping_method.method_id = :methodId')
                ->setParameter('methodId', $methodId);
        }

        return $qb;
    }

    /**
     * defineFindMarkupsByIdsQuery
     *
     * @param array $ids Array of markup id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindMarkupsByIdsQuery($ids)
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->andWhere($qb->expr()->in('m.markup_id', $ids));
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Profile
     *
     * @return void
     */
    protected function prepareCndMethodId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.shipping_method = :method_id')
            ->setParameter('method_id', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Profile
     *
     * @return void
     */
    protected function prepareCndShippingZone(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.zone = :zone_id')
            ->setParameter('zone_id', $value);
    }

    // }}}
}
