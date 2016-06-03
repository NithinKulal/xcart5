<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Logic;

/**
 * Sitemap links iterator
 *
 * @Decorator\Depend ("CDev\XMLSitemap")
 */
class SitemapIterator extends \XLite\Module\CDev\XMLSitemap\Logic\SitemapIterator implements \XLite\Base\IDecorator
{
    /**
     * Get current data
     *
     * @return array
     */
    public function current()
    {
        $data = parent::current();

        if (
            $this->position >= parent::count()
            && $this->position < (parent::count() + $this->getPagesLength())
        ) {
            $data = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')
                ->findOneAsSitemapLink($this->position - parent::count(), 1);
            $data = $this->assemblePageData($data);
        }

        return $data;
    }

    /**
     * Get length
     *
     * @return integer
     */
    public function count()
    {
        return parent::count() + $this->getPagesLength();
    }

    /**
     * Get pages length
     *
     * @return integer
     */
    protected function getPagesLength()
    {
        if (!isset($this->pagesLength)) {
            $this->pagesLength = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')
                ->countPagesAsSitemapsLinks();
        }

        return $this->pagesLength;
    }

    /**
     * Assemble page data
     *
     * @param \XLite\Module\CDev\SimpleCMS\Model\Page $page Page
     *
     * @return array
     */
    protected function assemblePageData(\XLite\Module\CDev\SimpleCMS\Model\Page $page)
    {
        return array(
            'loc'        => array('target' => 'page', 'id' => $page->getId()),
            'lastmod'    => \XLite\Core\Converter::time(),
            'changefreq' => \XLite\Core\Config::getInstance()->CDev->XMLSitemap->page_changefreq,
            'priority'   => $this->processPriority(\XLite\Core\Config::getInstance()->CDev->XMLSitemap->page_priority),
        );
    }

}
