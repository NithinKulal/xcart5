<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Shipping;

/**
 * Shipping method
 */
class Method extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Search parameters
     */
    const P_CARRIER  = 'carrier';
    const P_ADDED    = 'added';
    

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('processor', 'code'),
    );


    /**
     * Find all methods as options list
     *
     * @return array
     */
    public function findAsOptions()
    {
        return $this->defineFindAsOptionsQuery()->getResult();
    }

    /**
     * Returns shipping methods by specified processor Id
     *
     * @param string  $processorId Processor Id
     * @param boolean $enabledOnly Flag: Get only enabled methods (true) or all methods (false) OPTIONAL
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    public function findMethodsByProcessor($processorId, $enabledOnly = true)
    {
        return $this->defineFindMethodsByProcessor($processorId, $enabledOnly)->getResult();
    }

    /**
     * Returns carrier service with greatest position by processor
     *
     * @param string $processorId Processor Id
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function findOneMaxPositionByProcessor($processorId)
    {
        return $this->defineFindOneMaxPositionByProcessorQuery($processorId)->getSingleResult();
    }

    /**
     * Returns shipping method with greatest position
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function findOneCarrierMaxPosition()
    {
        return $this->defineFindOneCarrierMaxPositionQuery()->getSingleResult();
    }

    /**
     * Returns shipping methods by ids
     *
     * @param array $ids Array of method_id values
     *
     * @return array
     */
    public function findMethodsByIds($ids)
    {
        return $this->defineFindMethodsByIds($ids)->getResult();
    }

    /**
     * Create shipping method
     *
     * @param array $data Shipping method data
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function createShippingMethod($data)
    {
        // Array of allowed fields and flag required/optional
        $fields = $this->getAllowedFields();

        $errorFields = array();

        foreach ($fields as $field => $required) {
            if (isset($data[$field])) {
                $fields[$field] = $data[$field];

            } elseif ($required) {
                $errorFields[] = $field;
            }
        }

        if (!empty($errorFields)) {
            throw new \Exception(
                'createShippingMethod() failed: The following required fields are missed: ' .
                implode(', ', $errorFields)
            );
        }

        $method = $this->findMethodToUpdate($fields);

        if ($method) {
            $this->update($method, $fields);

        } else {
            $method = new \XLite\Model\Shipping\Method();
            $method->map($fields);
            $method = $this->insert($method);
        }

        return $method;
    }

    /**
     * Returns allowed fields and flag required/optional
     *
     * @return array
     */
    protected function getAllowedFields()
    {
        return array(
            'processor' => 1,
            'carrier'   => 1,
            'code'      => 1,
            'enabled'   => 0,
            'position'  => 0,
            'name'      => 1,
        );
    }

    /**
     * Search option to update
     *
     * @param array $data Data
     *
     * @return \XLite\Model\Config
     */
    protected function findMethodToUpdate($data)
    {
        return $this->findOneBy(array('processor' => $data['processor'], 'code' => $data['code']));
    }

    /**
     * Adds additional condition to the query for checking if method is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder object
     * @param string                     $alias Entity alias OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addEnabledCondition(\Doctrine\ORM\QueryBuilder $qb, $alias = 'm')
    {
        if (!\XLite::getInstance()->isAdminZone()) {
            $qb->andWhere($alias . '.enabled = 1');
        }

        return $qb;
    }

    /**
     * Define query builder object for findMethodsByProcessor()
     *
     * @param string  $processorId Processor Id
     * @param boolean $enabledOnly Flag: Get only enabled methods (true) or all methods (false)
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindMethodsByProcessor($processorId, $enabledOnly)
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.processor = :processorId')
            ->andWhere('m.carrier != :carrier')
            ->setParameter('processorId', $processorId)
            ->setParameter('carrier', '');

        return $enabledOnly
            ? $this->addEnabledCondition($qb)
            : $qb;
    }

    /**
     * Define query builder object for findOneMaxPositionByProcessor()
     *
     * @param string  $processorId Processor Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindOneMaxPositionByProcessorQuery($processorId)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.processor =:processorId')
            ->setParameter('processorId', $processorId)
            ->addOrderBy('m.position', 'DESC')
            ->setMaxResults(1);
    }

    /**
     * Define query builder object for findOneMaxPositionByProcessor()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindOneCarrierMaxPositionQuery()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.carrier = :carrier')
            ->setParameter('carrier', '')
            ->addOrderBy('m.position', 'DESC')
            ->setMaxResults(1);
    }

    /**
     * Define query builder for findAsOptions() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindAsOptionsQuery()
    {
        return $this->createQueryBuilder('m')
            ->addOrderBy('m.carrier', 'asc')
            ->addOrderBy('m.position', 'asc');
    }

    /**
     * Define query builder object for findMethodsByIds()
     *
     * @param array $ids Array of method_id values
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindMethodsByIds($ids)
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->andWhere($qb->expr()->in('m.method_id', $ids));
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Profile
     *
     * @return void
     */
    protected function prepareCndCarrier(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.carrier = :carrier')
            ->setParameter('carrier', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Profile
     *
     * @return void
     */
    protected function prepareCndAdded(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.added = :added')
            ->setParameter('added', $value);
    }

    // {{{ Online methods

    /**
     * Returns online carriers
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    public function findOnlineCarriers()
    {
        $qb = $this->defineFindOnlineCarriers();

        return $qb->getResult();
    }

    /**
     * Returns online carrier by processor id
     *
     * @param string $processorId Processor id
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function findOnlineCarrier($processorId)
    {
        $qb = $this->defineFindOnlineCarrier($processorId);

        return $qb->getSingleResult();
    }

    /**
     * Returns query builder for online carriers request
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOnlineCarriers()
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere('m.carrier = :carrier')
            ->andWhere('m.processor != :processor')
            ->setParameter('carrier', '')
            ->setParameter('processor', 'offline')
            ->addOrderBy('translations.name');

        return $qb;
    }

    /**
     * Returns query builder for online carriers request
     *
     * @param string $processorId Processor id
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOnlineCarrier($processorId)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere('m.carrier = :carrier')
            ->andWhere('m.processor = :processor')
            ->setParameter('carrier', '')
            ->setParameter('processor', $processorId);

        return $qb;
    }

    // }}}

    // {{{ Update shipping methods from marketplace

    /**
     * Update shipping methods with data received from the marketplace
     *
     * @param array $data List of payment methods received from marketplace
     *
     * @return void
     */
    public function updateShippingMethods($data)
    {
        if (!empty($data) && is_array($data)) {
            $tmpMethods = $this->createQueryBuilder('m')
                ->select('m')
                ->getQuery()
                ->getArrayResult();

            if ($tmpMethods) {
                $methods = array();
                // Prepare associative array of existing methods with 'processor' as a key
                foreach ($tmpMethods as $m) {
                    if ('offline' !== $m['processor'] && '' === $m['carrier']) {
                        $methods[$m['processor']] = $m;
                    }
                }

                foreach ($data as $i => $extMethod) {
                    if (!empty($extMethod['processor'])) {
                        $extMethod['fromMarketplace'] = 1;

                        $data[$i] = $extMethod;

                        if (isset($methods[$extMethod['processor']])) {
                            // Method already exists in the database
                            if (!$methods[$extMethod['processor']]['fromMarketplace']) {
                                // Method is not from marketplace, do not update
                                unset($data[$i]);
                            }
                        }

                    } else {
                        // Wrong data row, ignore this
                        unset($data[$i]);
                    }
                }

                // Save data as temporary yaml file
                $yaml = \Symfony\Component\Yaml\Yaml::dump(array('XLite\\Model\\Shipping\\Method' => $data));
                $yamlFile = LC_DIR_TMP . 'pm.yaml';

                \Includes\Utils\FileManager::write(LC_DIR_TMP . 'pm.yaml', $yaml);

                // Update database from yaml file
                \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
            }
        }
    }

    // }}}
}
