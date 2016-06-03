<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model;

/**
 * Product option group item
 *
 * @Entity
 * @Table  (name="theme_tweaker_template")
 */
class Template extends \XLite\Model\AEntity
{
    /**
     * Option unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Original template
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $template;

    /**
     * Last modified timestamp
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date;

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
     * Set template
     *
     * @param string $template
     * @return Template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Template
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }
}
