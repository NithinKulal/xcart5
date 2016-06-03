<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Category selector
 */
class Classes extends \XLite\View\FormField\Select\Multiple
{

    /**
     * getCSSFiles
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/select_classes.css';

        return $list;
    }

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/select_classes.js';

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'select_classes.twig';
    }

    /**
     * Return class list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\ProductClass')->search() as $class) {
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
