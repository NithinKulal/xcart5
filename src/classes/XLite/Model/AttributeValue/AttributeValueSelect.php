<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

/**
 * Attribute value (select)
 *
 * @Entity
 * @Table  (name="attribute_values_select",
 *      indexes={
 *          @Index (name="product_id", columns={"product_id"}),
 *          @Index (name="attribute_id", columns={"attribute_id"}),
 *          @Index (name="attribute_option_id", columns={"attribute_option_id"})
 *      }
 * )
 */
class AttributeValueSelect extends \XLite\Model\AttributeValue\Multiple
{
    /**
     * Attribute option
     *
     * @var \XLite\Model\AttributeOption
     *
     * @ManyToOne  (targetEntity="XLite\Model\AttributeOption")
     * @JoinColumn (name="attribute_option_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute_option;

    /**
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Return attribute value as string
     *
     * @return string
     */
    public function asString()
    {
        /** @see \XLite\Model\AttributeOptionTranslation */
        return $this->getAttributeOption()->getName();
    }

    /**
     * Clone
     *
     * @return static
     */
    public function cloneEntity()
    {
        /** @var static $newEntity */
        $newEntity = parent::cloneEntity();

        if ($this->getAttributeOption()) {
            if ($this->getAttribute()->getProduct()) {
                $attributeOption = $this->getAttributeOption()->cloneEntity();
                \XLite\Core\Database::getEM()->persist($attributeOption);

            } else {
                $attributeOption = $this->getAttributeOption();
            }
            $newEntity->setAttributeOption($attributeOption);
        }

        return $newEntity;
    }

    /**
     * Set attribute_option
     *
     * @param \XLite\Model\AttributeOption $attributeOption
     */
    public function setAttributeOption(\XLite\Model\AttributeOption $attributeOption = null)
    {
        $this->attribute_option = $attributeOption;
    }

    /**
     * Get attribute_option
     *
     * @return \XLite\Model\AttributeOption
     */
    public function getAttributeOption()
    {
        return $this->attribute_option;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
