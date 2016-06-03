<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Country
 *
 * @Entity
 * @Table  (name="countries",
 *      indexes={
 *          @Index (name="enabled", columns={"enabled"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Country extends \XLite\Model\Base\I18n
{
    /**
     * Country code (ISO 3166-1 alpha-2)
     *
     * @var string
     *
     * @Id
     * @Column (type="string", options={ "fixed": true }, length=2, unique=true)
     */
    protected $code;

    /**
     * Country code (ISO 3166-1 numeric)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $id;

    /**
     * Country code (ISO 3166-1 alpha-3)
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=3)
     */
    protected $code3 = '';

    /**
     * Enabled falg
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * States (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Model\State", mappedBy="country", cascade={"all"})
     * @OrderBy   ({"state" = "ASC"})
     */
    protected $states;

    /**
     * Regions (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Model\Region", mappedBy="country", cascade={"all"})
     * @OrderBy   ({"weight" = "ASC"})
     */
    protected $regions;

    /**
     * Currency
     *
     * @var \XLite\Model\Currency
     *
     * @ManyToOne (targetEntity="XLite\Model\Currency", inversedBy="countries")
     * @JoinColumn (name="currency_id", referencedColumnName="currency_id", onDelete="CASCADE")
     */
    protected $currency;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->states = new \Doctrine\Common\Collections\ArrayCollection();
        $this->regions = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get count of states
     *
     * @return integer
     */
    public function getStatesCount()
    {
        return count($this->states);
    }

    /**
     * Check if country has states
     *
     * @return boolean
     */
    public function hasStates()
    {
        return 0 < count($this->states);
    }

    /**
     * Check if country has regions
     *
     * @return boolean
     */
    public function hasRegions()
    {
        return 0 < count($this->regions);
    }

    /**
     * Remove zone elements
     *
     * @return void
     * @PreRemove
     */
    public function removeZoneElements()
    {
        $elements = \XLite\Core\Database::getRepo('XLite\Model\ZoneElement')->findBy(
            array(
                'element_type'  => \XLite\Model\ZoneElement::ZONE_ELEMENT_COUNTRY,
                'element_value' => $this->getCode(),
            )
        );

        foreach ($elements as $element) {
            \XLite\Core\Database::getEM()->remove($element);
        }
    }


    /**
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Country
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

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
     * Set code3
     *
     * @param string $code3
     * @return Country
     */
    public function setCode3($code3)
    {
        $this->code3 = $code3;
        return $this;
    }

    /**
     * Get code3
     *
     * @return string 
     */
    public function getCode3()
    {
        return $this->code3;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Country
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add states
     *
     * @param \XLite\Model\State $states
     * @return Country
     */
    public function addStates(\XLite\Model\State $states)
    {
        $this->states[] = $states;
        return $this;
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Add regions
     *
     * @param \XLite\Model\Region $regions
     * @return Country
     */
    public function addRegions(\XLite\Model\Region $regions)
    {
        $this->regions[] = $regions;
        return $this;
    }

    /**
     * Get regions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * Set currency
     *
     * @param \XLite\Model\Currency $currency
     * @return Country
     */
    public function setCurrency(\XLite\Model\Currency $currency = null)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get currency
     *
     * @return \XLite\Model\Currency 
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
