<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Role 
 *
 * @Entity
 * @Table  (name="role_translations")
 */
class RoleTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Name
     * 
     * @var string
     *
     * @Column (type="string")
     */
    protected $name = '';


    /**
     * Set name
     *
     * @param string $name
     * @return RoleTranslation
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
     * @return RoleTranslation
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
