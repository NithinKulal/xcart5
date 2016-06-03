<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model;

/**
 * View list decorator
 *
 * @HasLifecycleCallbacks
 */
class ViewList extends \XLite\Model\ViewList implements \XLite\Base\IDecorator
{
    const OVERRIDE_OFF = 0;
    const OVERRIDE_MOVE = 1;
    const OVERRIDE_HIDE = 2;

    /**
     * Class list name
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $list_override = '';

    /**
     * Child weight
     *
     * @var integer
     *
     * @Column (type="integer", length=11)
     */
    protected $weight_override = 0;

    /**
     * Override mode
     *
     * @var boolean
     *
     * @Column (type="integer")
     */
    protected $override_mode = 0;

    /**
     * Check if this view list item will be rendered
     *
     * @return boolean
     */
    public function isDisplayed()
    {
        return \XLite\Core\Request::getInstance()->isInLayoutMode()
            || !$this->isHidden();
    }

    /**
     * Check if this view list item is in hidden mode (not rendered in customer area and rendered invisible in layout editor)
     * @return boolean
     */
    public function isHidden()
    {
        return $this->getOverrideMode() === static::OVERRIDE_HIDE;
    }

    /**
     * Transfer override settings from another view list item
     *
     * @param  \XLite\Model\ViewList $other Value source
     */
    public function mapOverrides(\XLite\Model\ViewList $other)
    {
        if ($other) {
            $this->setListOverride($other->getListOverride());
            $this->setWeightOverride($other->getWeightOverride());
            $this->setOverrideMode($other->getOverrideMode());
        }
    }

    /**
     * Returns name of view list where this item will be actually displayed (takes overrides into account)
     *
     * @return string
     */
    public function getListActual()
    {
        if ($this->isDisplayed()) {
            if ($this->getOverrideMode() > static::OVERRIDE_OFF) {
                return $this->getListOverride();
            } else {
                return $this->getList();
            }
        } else {
            return 'hidden';
        }
    }

    /**
     * Returns view list item weight considering overrides
     *
     * @return integer
     */
    public function getWeightActual()
    {
        return $this->getOverrideMode() ? $this->getWeightOverride() : $this->getWeight();
    }

    /**
     * Set list_override
     *
     * @param string $listOverride
     * @return ViewList
     */
    public function setListOverride($listOverride)
    {
        $this->list_override = $listOverride;
        return $this;
    }

    /**
     * Get list_override
     *
     * @return string 
     */
    public function getListOverride()
    {
        return $this->list_override;
    }

    /**
     * Set weight_override
     *
     * @param integer $weightOverride
     * @return ViewList
     */
    public function setWeightOverride($weightOverride)
    {
        $this->weight_override = $weightOverride;
        return $this;
    }

    /**
     * Get weight_override
     *
     * @return integer 
     */
    public function getWeightOverride()
    {
        return $this->weight_override;
    }

    /**
     * Set override_mode
     *
     * @param integer $overrideMode
     * @return ViewList
     */
    public function setOverrideMode($overrideMode)
    {
        $this->override_mode = $overrideMode;
        return $this;
    }

    /**
     * Get override_mode
     *
     * @return integer 
     */
    public function getOverrideMode()
    {
        return $this->override_mode;
    }
}
