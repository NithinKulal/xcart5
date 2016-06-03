<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DataSource;

/**
 * Data source Parameter model
 *
 * @Entity
 * @Table  (name="data_source_parameters")
 */
class Parameter extends \XLite\Model\Base\NameValue
{
    /**
     * DataSource (relation)
     *
     * @var \XLite\Model\DataSource
     *
     * @ManyToOne (targetEntity="XLite\Model\DataSource", inversedBy="parameters")
     * @JoinColumn (name="data_source_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $dataSource;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Parameter
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dataSource
     *
     * @param \XLite\Model\DataSource $dataSource
     * @return Parameter
     */
    public function setDataSource(\XLite\Model\DataSource $dataSource = null)
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * Get dataSource
     *
     * @return \XLite\Model\DataSource 
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }
}
