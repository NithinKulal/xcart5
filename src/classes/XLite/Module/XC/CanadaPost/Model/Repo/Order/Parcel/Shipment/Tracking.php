<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Repo\Order\Parcel\Shipment;

/**
 * Tracking repository
 */
class Tracking extends \XLite\Model\Repo\ARepo
{
    // {{{ Remove expired tracking data

    /**
     * Find and remove expired tracking details
     *
     * @return void
     */
    public function removeExpired()
    {
        $list = $this->findAllExpiredDetails();

        if (count($list)) {

            foreach ($list as $tracking) {
                \XLite\Core\Database::getEM()->remove($tracking);
            }

            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Database::getEM()->clear();
        }
    }

    /**
     * Find all expired tracking details
     *
     * @return \Doctrine\Common\Collection\ArrayCollection
     */
    public function findAllExpiredDetails()
    {
        return $this->defineAllExpiredDetailsQuery()->getResult();
    }

    /**
     * Define query for removeExpired() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllExpiredDetailsQuery()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.expiry < :time')
            ->setParameter('time', \XLite\Core\Converter::time());
    }

    // }}}
}
