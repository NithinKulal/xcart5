<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Controller\Customer;

/**
 * Page controller
 * todo: set extends \XLite\Controller\Customer\Base\Catalog
 */
class Page extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters list
     *
     * @var array
     */
    protected $params = array('target', 'id');

    /**
     * Alias
     *
     * @return \XLite\Module\CDev\SimpleCMS\Model\Page
     */
    public function getPage()
    {
        return $this->getId()
            ? \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')->find($this->getId())
            : null;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible() && $this->checkAccess()
            ? $this->getPage()->getName()
            : '';
    }

    /**
     * Returns the page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        $model = $this->getModelObject();

        return ($model && $model->getMetaTitle()) ? $model->getMetaTitle() : $this->getTitle();
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->isVisible() && $this->checkAccess()
            ? $this->getPage()->getName()
            : static::t('Page not found');
    }

    /**
     * Return current model id
     *
     * @return integer
     */
    protected function getId()
    {
        return (int) \XLite\Core\Request::getInstance()->id;
    }


    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $page = $this->getPage();

        // todo: rename getTeaser() to getMetaDesc()
        return $page ? $page->getTeaser() : parent::getMetaDescription();
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        $page = $this->getPage();

        // todo: rename getMetaKeywords() to getMetaTags()
        return $page ? $page->getMetaKeywords() : parent::getKeywords();
    }

    /**
     * Return current (or default) page object
     *
     * @return \XLite\Model\Product
     */
    public function getModelObject()
    {
        return $this->getPage();
    }

    /**
     * Check if redirect to clean URL is needed
     *
     * @return boolean
     */
    protected function isRedirectToCleanURLNeeded()
    {
        return parent::isRedirectToCleanURLNeeded()
            || (!\XLite::isCleanURL() && $this->getModelObject()->getCleanURL());
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getPage()
            && $this->getPage()->getEnabled();
    }
}
