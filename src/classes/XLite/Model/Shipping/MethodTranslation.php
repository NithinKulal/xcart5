<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping;

/**
 * Shipping method multilingual data
 *
 * @Entity
 * @Table (name="shipping_method_translations")
 *      indexes={
 *          @Index (name="ci", columns={"code","id"}),
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class MethodTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Shipping method name
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=false)
     */
    protected $name = '';

    /**
     * Shipping delivery time (for offline methods)
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $deliveryTime = '';

    /**
     * Set name
     *
     * @param string $name
     * @return MethodTranslation
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
     * Set deliveryTime
     *
     * @param string $deliveryTime
     * @return MethodTranslation
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    /**
     * Get deliveryTime
     *
     * @return string 
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
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
     * @return MethodTranslation
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
