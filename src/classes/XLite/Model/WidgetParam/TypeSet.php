<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * Set
 */
class TypeSet extends \XLite\Model\WidgetParam\AWidgetParam
{
    /**
     * Param type
     *
     * @var string
     */
    protected $type = 'list';

    /**
     * Options
     *
     * @var array
     */
    protected $options = null;

    /**
     * Constructor
     *
     * @param mixed $label     Param label (text)
     * @param mixed $value     Default value OPTIONAL
     * @param mixed $isSetting Display this setting in CMS or not OPTIONAL
     * @param array $options   Options list OPTIONAL
     *
     * @return void
     */
    public function __construct($label, $value = null, $isSetting = false, array $options = array())
    {
        parent::__construct($label, $value, $isSetting);

        // TODO - check if there are more convinient ways to extend this class
        if (!isset($this->options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options 
     * 
     * @param array $options Options
     *  
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get options 
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return void
     */
    protected function getValidaionSchema($value)
    {
        return array(
            array(
                self::ATTR_CONDITION => !isset($this->options[$value]),
                self::ATTR_MESSAGE   => ' unallowed param value - "' . $value . '"',
            ),
        );
    }
}
