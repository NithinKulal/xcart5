<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Category
 */
class Category extends \XLite\Controller\Customer\Base\Catalog
{
    /**
     * Check whether the category title is visible in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return $this->isVisible() && $this->getModelObject()->getShowTitle();
    }

    /**
     * Returns the page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible() ? parent::getTitle() : static::t('Page not found');
    }

    /**
     * getDescription
     *
     * @return string
     */
    public function getDescription()
    {
        $model = $this->getModelObject();

        return $model ? $model->getViewDescription() : null;
    }

    /**
     * Get session cell name for pager widget
     *
     * @return string
     */
    public function getPagerSessionCell()
    {
        return parent::getPagerSessionCell() . ($this->getCategory() ? $this->getCategory()->getCategoryId() : '');
    }

    /**
     * getModelObject
     *
     * @return \XLite\Model\AEntity
     */
    protected function getModelObject()
    {
        return $this->getCategory();
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest()
    {
        if ($this->isNeedToRedirectToMain()) {
            $this->setReturnURL($this->getShopURL());
            $this->setHardRedirect(true);
        }

        parent::handleRequest();
    }

    protected function isNeedToRedirectToMain()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId()
            === $this->getCategory()->getCategoryId();
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && null !== $this->getCategory()
            && $this->getCategory()->isVisible();
    }

    /**
     * Check if redirect to clean URL is needed
     *
     * @return boolean
     */
    protected function isRedirectToCleanURLNeeded()
    {
        return parent::isRedirectToCleanURLNeeded() && $this->getCategory()->getParent()
            || preg_match(
                '/\/cart\.php/Si',
                \Includes\Utils\ArrayManager::getIndex(
                    \XLite\Core\Request::getInstance()->getServerData(), 'REQUEST_URI'
                )
            )
            || preg_match(
                '/target=main/i',
                \Includes\Utils\ArrayManager::getIndex(
                    \XLite\Core\Request::getInstance()->getServerData(), 'QUERY_STRING'
                )
            );
    }
}
