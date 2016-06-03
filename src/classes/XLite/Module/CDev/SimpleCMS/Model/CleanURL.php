<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Model;

/**
 * CleanURL
 */
class CleanURL extends \XLite\Model\CleanURL implements \XLite\Base\IDecorator
{
    /**
     * Relation to a product entity
     *
     * @var \XLite\Module\CDev\SimpleCMS\Model\Page
     *
     * @ManyToOne  (targetEntity="XLite\Module\CDev\SimpleCMS\Model\Page", inversedBy="cleanURLs")
     * @JoinColumn (name="page_id", referencedColumnName="id")
     */
    protected $page;

    /**
     * Set page
     *
     * @param \XLite\Module\CDev\SimpleCMS\Model\Page $page
     * @return CleanURL
     */
    public function setPage(\XLite\Module\CDev\SimpleCMS\Model\Page $page = null)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get page
     *
     * @return \XLite\Module\CDev\SimpleCMS\Model\Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}
