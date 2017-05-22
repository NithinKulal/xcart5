<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\FormField\Select\CheckboxList;

/**
 * User type selector
 */
class TagSelector extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductTags/tag_selector';
    }

    /**
     * Get label container class
     *
     * @return string
     */
    protected function getLabelContainerClass()
    {
        return parent::getLabelContainerClass()
            . $this->getCommonClass();
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass()
            . ' checkbox-list type-s'
            . $this->getCommonClass();
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getCommonClass()
    {
        $class = ' collapsible';

        $value = $this->getValue();
        if (!$value || !is_array($value) || !array_filter(array_values($value))) {
            $class .= ' collapsed';
        }

        return $class;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getOptions()
    {
        $config = \XLite\Core\Config::getInstance()->XC->ProductFilter;
        if ($config->attributes_filter_by_category) {
            $list = $this->getCategory()->getTags();

        } else {
            $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag');
            $tags = $repo->findAllTags();
            $list = array();
            foreach ($tags as $tag) {
                $list[$tag->getId()] = $tag->getName();
            }
        }

        return $list;
    }
}
