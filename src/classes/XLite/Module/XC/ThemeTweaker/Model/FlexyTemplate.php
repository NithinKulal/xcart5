<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model;

/**
 * Flexy-template fake entity (for items list)
 */
class FlexyTemplate extends \XLite\Model\AEntity
{
    /**
     * Id
     *
     * @var integer
     */
    protected $id = 0;

    /**
     * Flexy-template path
     *
     * @var string
     */
    protected $flexyTemplate;

    /**
     * Twig-template path
     *
     * @var string
     */
    protected $twigTemplate;

    /**
     * Flag: true if flexy-template is converted
     *
     * @var boolean
     */
    protected $converted;

    /**
     * Flag: true if original flexy-template exists (template for substitution)
     *
     * @var boolean
     */
    protected $origExists;

    /**
     * Emulate isPersistent() for fake entity
     *
     * @return boolean
     */
    public function isPersistent()
    {
        return true;
    }

    /**
     * Emulate getUniqueIdentifier() for fake entity
     *
     * @return integer
     */
    public function getUniqueIdentifier()
    {
        return $this->id;
    }

    /**
     * Construct object
     *
     * @param integer $id
     * @param string  $flexyTemplate
     * @param string  $twigTemplate
     * @param boolean $converted
     *
     * @return void
     */
    public function __construct($id, $flexyTemplate = '', $twigTemplate = '', $converted = false, $origExists = false)
    {
        $this->id = $id;
        $this->flexyTemplate = $flexyTemplate;
        $this->twigTemplate = $twigTemplate;
        $this->converted = $converted;
        $this->origExists = $origExists;
    }

    /**
     * Getter for $this->id property
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Getter for $this->flexyTemplate property
     *
     * @return string
     */
    public function getFlexyTemplate()
    {
        return $this->flexyTemplate;
    }

    /**
     * Getter for $this->twigTemplate property
     *
     * @return string
     */
    public function getTwigTemplate()
    {
        return $this->twigTemplate;
    }

    /**
     * Getter for $this->converted property
     *
     * @return boolean
     */
    public function getConverted()
    {
        return $this->converted;
    }
    /**
     * Getter for $this->origExists property
     *
     * @return boolean
     */
    public function isOrigExists()
    {
        return $this->origExists;
    }
}
