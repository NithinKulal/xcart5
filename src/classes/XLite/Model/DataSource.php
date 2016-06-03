<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * DataSource model
 *
 * @Entity
 * @Table  (name="data_sources")
 */
class DataSource extends \XLite\Model\AEntity
{

    /**
     *  Data source model types
     */
    const TYPE_ECWID = 'ecwid';

    /**
     * Unique data source id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Data source type
     * 
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $type;

    /**
     * Data source parameters (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Model\DataSource\Parameter", mappedBy="dataSource", cascade={"all"})
     */
    protected $parameters;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->parameters = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    // {{{ Parameters operations

    /**
     * Get Parameter object identified by its name
     * 
     * @param string $name Parameter name
     *  
     * @return \XLite\Model\DataSource\Parameter
     */
    public function getParameter($name)
    {
        return $this->getParameters()->filter(
            function ($p) use ($name) {
                return $p->getName() == $name;
            }
        )->first();
    }

    /**
     * Get parameter value by its name
     * 
     * @param string $name Parameter name
     *  
     * @return mixed
     */
    public function getParameterValue($name)
    {
        $param = $this->getParameter($name);

        return $param ? $param->getValue() : null;
    }

    /**
     * Sets parameter value identified by its name
     * Creates new if not exist
     * 
     * @param string $name  Parameter name
     * @param mixed  $value Parameter value
     *  
     * @return void
     */
    public function setParameterValue($name, $value)
    {
        $param = $this->getParameter($name);

        if (!$param) {
            $param = new \XLite\Model\DataSource\Parameter();
            $param->setDataSource($this);
            $param->setName($name);

            $this->addParameters($param);
        }

        $param->setValue($value);
    }

    // }}}

    // {{{ Data source

    /**
     * Get data source based on model shop type
     * 
     * @return \XLite\Core\DataSource\ADataSource
     */
    public function detectSource()
    {
        $result = null;

        if (self::TYPE_ECWID == $this->getType()) {
            $result = new \XLite\Core\DataSource\Ecwid($this);
        }

        return $result;
    }

    /**
     * Get concrete model widget class to be used in templates
     * 
     * @return string
     */
    public function getModelWidgetClass()
    {
        $result = null;

        if (self::TYPE_ECWID == $this->getType()) {
            $result = '\XLite\View\Model\DataSource\Ecwid';
        }

        return $result;
    }

    // }}}


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
     * Set type
     *
     * @param string $type
     * @return DataSource
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add parameters
     *
     * @param \XLite\Model\DataSource\Parameter $parameters
     * @return DataSource
     */
    public function addParameters(\XLite\Model\DataSource\Parameter $parameters)
    {
        $this->parameters[] = $parameters;
        return $this;
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}

