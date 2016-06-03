<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Attribute multilingual data
 *
 * @Entity
 *
 * @Table (name="attribute_translations",
 *         indexes={
 *              @Index (name="ci", columns={"code","id"}),
 *              @Index (name="id", columns={"id"})
 *         }
 * )
 */
class AttributeTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Unit
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="string", length=255)
     */
    protected $unit = '';


    /**
     * Set name
     *
     * @param string $name
     * @return AttributeTranslation
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
     * Set unit
     *
     * @param string $unit
     * @return AttributeTranslation
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * Get unit
     *
     * @return string 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Get label_id
     *
     * @return integer 
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return AttributeTranslation
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
}
