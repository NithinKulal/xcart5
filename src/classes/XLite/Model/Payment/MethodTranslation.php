<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

/**
 * Payment method multilingual data
 *
 * @Entity
 * @Table (name="payment_method_translations",
 *      indexes={
 *          @Index (name="ci", columns={"code","id"}),
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class MethodTranslation extends \XLite\Model\Base\Translation
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
     * Title (Name of payment method which is displayed for customer on checkout)
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $title = '';

    /**
     * Description
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $description = '';

    /**
     * Admin description
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $adminDescription = '';

    /**
     * Alternative admin description
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $altAdminDescription = '';

    /**
     * Instruction
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $instruction = '';

    /**
     * Title getter
     * If no title is given then the "name" field must be used
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?: $this->getName();
    }

    /**
     * Admin description getter
     * If no admin description is given then the "description" field must be used
     *
     * @return string
     */
    public function getAdminDescription()
    {
        return $this->adminDescription ?: $this->getDescription();
    }

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
     * Set title
     *
     * @param string $title
     * @return MethodTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return MethodTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set adminDescription
     *
     * @param text $adminDescription
     * @return MethodTranslation
     */
    public function setAdminDescription($adminDescription)
    {
        $this->adminDescription = $adminDescription;
        return $this;
    }

    /**
     * Set altAdminDescription
     *
     * @param text $altAdminDescription
     * @return MethodTranslation
     */
    public function setAltAdminDescription($altAdminDescription)
    {
        $this->altAdminDescription = $altAdminDescription;
        return $this;
    }

    /**
     * Get altAdminDescription
     *
     * @return text 
     */
    public function getAltAdminDescription()
    {
        return $this->altAdminDescription;
    }

    /**
     * Set instruction
     *
     * @param text $instruction
     * @return MethodTranslation
     */
    public function setInstruction($instruction)
    {
        $this->instruction = $instruction;
        return $this;
    }

    /**
     * Get instruction
     *
     * @return text 
     */
    public function getInstruction()
    {
        return $this->instruction;
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
