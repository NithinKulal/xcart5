<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model\Repo;

/**
 * The Profile model repository
 */
abstract class Profile extends \XLite\Model\Repo\Profile implements \XLite\Base\IDecorator
{
    /**
     * Search profile by email
     *
     * @param string $email User's email
     *
     * @return array
     */
    public function findSimilarByEmail($email)
    {
        return $this->defineSimilarByEmailQuery($email)->getResult();
    }

    /**
     * Search profile by customer name
     *
     * @param string $name User's customer name
     *
     * @return array
     */
    public function findSimilarByName($name)
    {
        return $this->defineSimilarByNameQuery($name)->getResult();
    }

    /**
     * Create query builder to search profile by email
     *
     * @param string $email User's email
     *
     * @return string
     */
    protected function defineSimilarByEmailQuery($email)
    {
        return $this->createQueryBuilder()
            ->andWhere('p.login LIKE :email')
            ->andWhere('p.order IS NULL')
            ->setParameter('email', '%' . $email . '%');
    }

    /**
     * Create query builder to search profile by customer name
     *
     * @param string $name User's customer name
     *
     * @return string
     */
    protected function defineSimilarByNameQuery($name)
    {
        $parts = explode(' ', $name, 2);
        if (empty($parts[1])) {
            unset($parts[1]);
        }

        $qb = $this->createQueryBuilder()
            ->linkInner('p.addresses')
            ->linkInner('addresses.addressFields')
            ->linkInner('addressFields.addressField')
            ->andWhere('p.order IS NULL');

        $conditions = array(
            '(addressField.serviceName = \'firstname\' AND addressFields.value LIKE :firstname)',
            '(addressField.serviceName = \'lastname\' AND addressFields.value LIKE :lastname)',
        );

        if (count($parts) == 1) {
            $qb->andWhere(implode(' OR ', $conditions))
                ->setParameter('firstname', '%' . $parts[0] . '%')
                ->setParameter('lastname', '%' . $parts[0] . '%');
        } else {
            $qb->andWhere(implode(' OR ', $conditions))
                ->setParameter('firstname', '%' . $parts[0] . '%')
                ->setParameter('lastname', '%' . $parts[1] . '%');
        }

        return $qb;
    }
}
