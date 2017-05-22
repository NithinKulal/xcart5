<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View;

/**
 * Product modify widget.
 *
 * @ListChild (list="product.details.page.info", weight="15", zone="customer")
 */
class TagsList extends \XLite\View\AView
{
    /**
     * Cache of product tags.
     */
    protected $tags;

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductTags/product/details/page/info';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/tags.css';

        return $list;
    }

    /**
     * Return widget default template.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/tags_list.twig';
    }

    /**
     * Return current product tags
     *
     * @return array
     */
    protected function getProductTags()
    {
        if (null === $this->tags) {
            $this->tags = array();
            foreach ($this->getProduct()->getTags() as $tag) {
                $this->tags[$tag->getId()] = $tag->getName();
            }
        }

        return $this->tags;
    }

    /**
     * getActionURL
     *
     * @param array $params Params to modify OPTIONAL
     *
     * @return string
     */
    public function getActionURL(array $params = array())
    {
        return $this->buildURL(
            'search',
            null,
            array(
                'action' => 'search',
                'substring' => $params['tag'],
                'including' => 'phrase',
                'by_tag' => 'Y'
            )
        );
    }

    /**
     * Check view visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && 0 < count($this->getProductTags());
    }
}
