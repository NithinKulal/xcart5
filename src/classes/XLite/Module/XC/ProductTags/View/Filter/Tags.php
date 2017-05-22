<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\Filter;

/**
 * tag widget
 *
 * @Decorator\Depend ("XC\ProductFilter")
 * @ListChild (list="sidebar.filter", zone="customer", weight="400")
 */
class Tags extends \XLite\Module\XC\ProductFilter\View\Filter\AFilter
{
    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductTags/tags';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return tags count
     *
     * @return integer
     */
    protected function getTagsCount()
    {
        /** @var \XLite\Module\XC\ProductTags\Model\Repo\Tag $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag');
        $config = \XLite\Core\Config::getInstance()->XC->ProductFilter;

        if ($config->attributes_filter_by_category) {
            $tags = $this->getCategory()->getTags();
            $result = count($tags);

        } else {
            $result = $repo->findAllTags(true);
        }

        return $result;
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->XC->ProductFilter->enable_tags_filter
            && 0 < $this->getTagsCount();
    }

    /**
     * Get value
     *
     * @return string
     */
    protected function getValue()
    {
        $filterValues = $this->getFilterValues();

        return isset($filterValues['tags'])
            ? $filterValues['tags']
            : '';
    }
}
