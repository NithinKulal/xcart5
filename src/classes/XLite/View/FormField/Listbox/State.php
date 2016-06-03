<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Listbox;

/**
 * States listbox widget
 */
class State extends \XLite\View\FormField\Listbox\AListbox
{
    /**
     * Widget param names
     */
    const PARAM_ALL = 'all';


    /**
     * Prepare and set up value of listbox
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (is_object($value) && $value instanceOf \Doctrine\Common\Collections\Collection) {
            $value = $value->toArray();

        } elseif (!is_array($value)) {
            $value = array($value);
        }

        foreach ($value as $k => $v) {
            if (is_object($v) && $v instanceOf \XLite\Model\AEntity) {
                $value[$k] = $v->getCountry()->getCode() . '_' . $v->getCode();
            }
        }

        parent::setValue($value);
    }

    /**
     * Get selector default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = \XLite\Core\Database::getRepo('XLite\Model\State')->findAllStates();
        usort($list, array('\XLite\Model\Zone', 'sortStates'));

        $options = array();

        foreach ($list as $state) {
            $options[$state->getCountry()->getCode() . '_' . $state->getCode()] = $state->getCountry()->getCountry() . ': ' . $state->getState();
        }

        return $options;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' state-listbox';
    }
}
