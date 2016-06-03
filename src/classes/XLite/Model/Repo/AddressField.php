<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * The "address field" model repository
 */
class AddressField extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const CND_ENABLED  = 'enabled';
    const CND_REQUIRED = 'required';
    const CND_WITHOUT_CSTATE = 'withoutCState';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Get all enabled address fields
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function findAllEnabled()
    {
        return $this->search(
            new \XLite\Core\CommonCell(
                array(
                    'enabled' => true,
                    'orderBy'  => array(
                        array('a.id')
                    )
                )
            )
        );
    }

    /**
     * Return address field service name value
     *
     * @param \XLite\Model\AddressField $field
     *
     * @return string
     */
    public function getServiceName(\XLite\Model\AddressField $field)
    {
        return $field->getServiceName();
    }

    /**
     * Get billing address-specified required fields
     *
     * @return array
     */
    public function getBillingRequiredFields()
    {
        return $this->findRequiredFields();
    }

    /**
     * Get shipping address-specified required fields
     *
     * @return array
     */
    public function getShippingRequiredFields()
    {
        return $this->findRequiredFields();
    }

    /**
     * Get all enabled and required address fields
     *
     * @return array
     */
    public function findRequiredFields()
    {
        return array_map(array($this, 'getServiceName'), $this->search(
            new \XLite\Core\CommonCell(array(
                'enabled' => true,
                'required' => true,
            )
        )));
    }

    /**
     * Get all enabled and required address fields
     *
     * @return array
     */
    public function findEnabledFields()
    {
        return array_map(
            array($this, 'getServiceName'),
            $this->search(
                new \XLite\Core\CommonCell(array('enabled' => true,))
            )
        );
    }

    /**
     * Find one by record
     *
     * @param array                $data   Record
     * @param \XLite\Model\AEntity $parent Parent model OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function findOneByRecord(array $data, \XLite\Model\AEntity $parent = null)
    {
        if (isset($data['serviceName'])) {
            $result = $this->findOneByServiceName($data['serviceName']);

        } else {
            $result = parent::findOneByRecord($data, $parent);
        }

        return $result;
    }

    /**
     * Prepare query builder for enabled status search
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.enabled = :enabled_value')
            ->setParameter('enabled_value', $value);
    }

    /**
     * Prepare query builder for required status search
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndRequired(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.required = :required_value')
            ->setParameter('required_value', $value);
    }

    /**
     * Prepare query builder for required status search
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndWithoutCState(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder
                ->andWhere($this->getMainAlias($queryBuilder) . '.serviceName != :cstate')
                ->setParameter('cstate', 'custom_state');
        }
    }

}
