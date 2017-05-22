<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Enables caching for a widget
 */
trait SingleOptionAsLabelTrait
{
    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        if ($this->isSingleOption()) {
            return 'select_single_option.twig';
        }

        return parent::getFieldTemplate();
    }

    /**
     * Check if field allowed to process as single option
     *
     * @return boolean
     */
    protected function isSingleOptionAllowed()
    {
        return true;
    }

    /**
     * Returns true if there is only one option available
     *
     * @return boolean
     */
    protected function isSingleOption()
    {
        return $this->isSingleOptionAllowed() && count($this->getOptions()) === 1;
    }

    /**
     * Returns first and only option as the default value
     *
     * @return string
     */
    public function getValue()
    {
        if ($this->isSingleOption()) {
            $options = array_keys($this->getOptions());
            return $options[0];
        }

        return parent::getValue();
    }

    /**
     * Returns label for value
     *
     * @return string
     */
    protected function getValueLabel()
    {
        return isset($this->getOptions()[$this->getValue()])
            ? $this->getOptions()[$this->getValue()]
            : \XLite\Core\Translation::getInstance()->translate('Not selected');
    }

    /**
     * Get select specific attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $attrs = parent::getAttributes();

        if ($this->isSingleOption()) {
            $attrs['value'] = $this->getValue();
            $attrs['class'] .= ' hidden';
        }

        return $attrs;
    }
}
