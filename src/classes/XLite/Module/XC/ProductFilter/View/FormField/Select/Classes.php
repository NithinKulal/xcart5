<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\FormField\Select;

/**
 * Classes
 *
 */
class Classes extends \XLite\View\FormField\Select\Tags\ATags
{
    /**
     * Return class list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\ProductClass')->findAll() as $class) {
            $list[$class->getId()] = $class->getName();
        }

        return $list;
    }

    /**
     * Return String representation of selected product classes
     *
     * @return string
     */
    protected function getSelectedClassesList()
    {
        $classNames = array();

        foreach ($this->getValue()->toArray() as $class) {
            $classNames[] = $class->getName();
        }

        return implode(', ', $classNames);
    }
}
