<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Model\Image\Page;

/**
 * Page image
 *
 * @Entity
 * @Table  (name="page_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Relation to a page entity
     *
     * @var \XLite\Module\CDev\SimpleCMS\Model\Page
     *
     * @OneToOne   (targetEntity="XLite\Module\CDev\SimpleCMS\Model\Page", inversedBy="image")
     * @JoinColumn (name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page;

    /**
     * Set page
     *
     * @param \XLite\Module\CDev\SimpleCMS\Model\Page $page
     * @return Image
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
