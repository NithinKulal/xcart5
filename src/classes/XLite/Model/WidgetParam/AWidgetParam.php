<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * ____description____
 */
abstract class AWidgetParam extends \XLite\Base\SuperClass
{
    /**
     * Indexes in the "conditions" array
     */
    const ATTR_CONDITION = 'condition';
    const ATTR_MESSAGE   = 'text';
    const ATTR_CONTINUE  = 'continue';

    /**
     * Param type
     *
     * @var string
     */
    protected $type = null;

    /**
     * Param value
     *
     * @var mixed
     */
    protected $value = null;

    /**
     * Param label
     *
     * @var string
     */
    protected $label = null;

    /**
     * Determines if the param will be diaplayed in CMS as widget setting
     *
     * @var mixed
     */
    protected $isSetting = false;

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return array
     */
    abstract protected function getValidaionSchema($value);

    /**
     * Constructor
     *
     * @param mixed $label     Param label (text)
     * @param mixed $value     Default value OPTIONAL
     * @param mixed $isSetting Display this setting in CMS or not OPTIONAL
     *
     * @return void
     */
    public function __construct($label, $value = null, $isSetting = false)
    {
        $this->label     = $label;
        $this->isSetting = $isSetting;

        $this->setValue($value);
    }

    /**
     * Validate passed value
     *
     * @param mixed $value Value to validate
     *
     * @return mixed
     */
    public function validate($value)
    {
        $result = $this->checkConditions($this->getValidaionSchema($value));

        return array(empty($result), $result);
    }

    /**
     * Return protected property
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * Set param value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Append data to param value
     *
     * @param mixed $value Value to append
     *
     * @return void
     */
    public function appendValue($value)
    {
        $this->value += $value;
    }

    /**
     * setVisibility
     *
     * @param boolean $isSetting Visibility flag
     *
     * @return void
     */
    public function setVisibility($isSetting)
    {
        $this->isSetting = $isSetting;
    }


    /**
     * Check passed conditions
     *
     * @param array $conditions Conditions to check
     *
     * @return array
     */
    protected function checkConditions(array $conditions)
    {
        $messages = array();

        foreach ($conditions as $condition) {
            if (true === $condition[self::ATTR_CONDITION]) {
                $messages[] = $condition[self::ATTR_MESSAGE];

                if (!isset($condition[self::ATTR_CONTINUE])) {
                     break;
                }
            }
        }

        return $messages;
    }
}
