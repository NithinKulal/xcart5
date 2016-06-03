<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * ZoneElement model
 *
 * @Entity
 * @Table (name="zone_elements",
 *      indexes={
 *          @Index (name="type_value", columns={"element_type","element_value"}),
 *          @Index (name="id_type", columns={"zone_id","element_type"})
 *      }
 * )
 */
class ZoneElement extends \XLite\Model\AEntity
{
    /*
     * Zone element types
     */
    const ZONE_ELEMENT_COUNTRY = 'C';
    const ZONE_ELEMENT_STATE   = 'S';
    const ZONE_ELEMENT_TOWN    = 'T';
    const ZONE_ELEMENT_ZIPCODE = 'Z';
    const ZONE_ELEMENT_ADDRESS = 'A';

    /**
     * Unique zone element Id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer", length=11, nullable=false)
     */
    protected $element_id;

    /**
     * Zone element value, e.g. 'US', 'US_NY', 'New Y%' etc
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $element_value;

    /**
     * Element type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $element_type;

    /**
     * Zone (relation)
     *
     * @var \XLite\Model\Zone
     *
     * @ManyToOne (targetEntity="XLite\Model\Zone", inversedBy="zone_elements")
     * @JoinColumn (name="zone_id", referencedColumnName="zone_id", onDelete="CASCADE")
     */
    protected $zone;

    /**
     * getElementTypesData
     *
     * @return array
     */
    static public function getElementTypesData()
    {
        return array(
            self::ZONE_ELEMENT_COUNTRY => array(
                'field'      => 'country',   // Address field name
                'weight'     => 0x01,        // Element weight
                'funcSuffix' => 'Countries', // Suffix for functions name: getZone<Suffix>, checkZone<Suffix>
                'required'   => true,        // Required property: if true then entire zone declined if this element does bot match
            ),
            self::ZONE_ELEMENT_STATE   => array(
                'field'      => 'state',
                'weight'     => 0x02,
                'funcSuffix' => 'States',
                'required'   => true,
            ),
            self::ZONE_ELEMENT_ZIPCODE => array(
                'field'      => 'zipcode',
                'weight'     => 0x08,
                'funcSuffix' => 'ZipCodes',
                'required'   => true,
            ),
            self::ZONE_ELEMENT_TOWN    => array(
                'field'      => 'city',
                'weight'     => 0x10,
                'funcSuffix' => 'Cities',
                'required'   => false,
            ),
            self::ZONE_ELEMENT_ADDRESS => array(
                'field'      => 'address',
                'weight'     => 0x20,
                'funcSuffix' =>'Addresses',
                'required'   => false,
            )
        );
    }

    /**
     * Get element_id
     *
     * @return integer 
     */
    public function getElementId()
    {
        return $this->element_id;
    }

    /**
     * Set element_value
     *
     * @param string $elementValue
     * @return ZoneElement
     */
    public function setElementValue($elementValue)
    {
        $this->element_value = $elementValue;
        return $this;
    }

    /**
     * Get element_value
     *
     * @return string 
     */
    public function getElementValue()
    {
        return $this->element_value;
    }

    /**
     * Set element_type
     *
     * @param string $elementType
     * @return ZoneElement
     */
    public function setElementType($elementType)
    {
        $this->element_type = $elementType;
        return $this;
    }

    /**
     * Get element_type
     *
     * @return string 
     */
    public function getElementType()
    {
        return $this->element_type;
    }

    /**
     * Set zone
     *
     * @param \XLite\Model\Zone $zone
     * @return ZoneElement
     */
    public function setZone(\XLite\Model\Zone $zone = null)
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * Get zone
     *
     * @return \XLite\Model\Zone 
     */
    public function getZone()
    {
        return $this->zone;
    }
}
