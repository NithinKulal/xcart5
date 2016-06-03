<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Model;

/**
 * Attribute
 *
 */
class Attribute extends \XLite\Model\Attribute implements \XLite\Base\IDecorator
{
    /**
     * Flag if attribute is visible in product filter
     *
     * @var boolean
     *
     * @Column (type="boolean", options={ "default": true })
     */
    protected $visible = true;

    /**
     * Return list of filtered types
     *
     * @return array
     */
    public static function getFilteredTypes()
    {
        return array(
            self::TYPE_CHECKBOX,
            self::TYPE_SELECT,
        );
    }

    /**
     * Return name of filter widget class
     *
     * @return string
     */
    public function getFilterWidgetClass()
    {
        switch ($this->getType()) {
            case self::TYPE_TEXT:
                $class = '';
                break;

            case self::TYPE_CHECKBOX:
                $class = '\XLite\View\FormField\Input\Checkbox\Enabled';
                break;

            case self::TYPE_SELECT:
                $class = '\XLite\Module\XC\ProductFilter\View\FormField\Select\Attribute';
                break;

            default:
                $class = '';
        }

        return $class;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Attribute
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }
}
