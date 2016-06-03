<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * The "address field value" model repository
 */
class AddressFieldValue extends \XLite\Model\Repo\ARepo
{
    /**
     * Find value by address and address field 
     * 
     * @param \XLite\Model\Address      $address Address
     * @param \XLite\Model\AddressField $field   Address field
     *  
     * @return \XLite\Model\AddressFieldValue
     */
    public function findOneByAddressAndField(\XLite\Model\Address $address, \XLite\Model\AddressField $field)
    {
        return $this->defineFindOneByAddressAndFieldQuery($address, $field)->getSingleResult();
    }

    /**
     * Define query for findOneByAddressAndField() method
     *
     * @param \XLite\Model\Address      $address Address
     * @param \XLite\Model\AddressField $field   Address field
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByAddressAndFieldQuery(\XLite\Model\Address $address, \XLite\Model\AddressField $field)
    {
        return $this->createQueryBuilder()
            ->andWhere('a.address = :address AND a.addressField = :field')
            ->setMaxResults(1)
            ->setParameter('address', $address)
            ->setParameter('field', $field);
    }

}
