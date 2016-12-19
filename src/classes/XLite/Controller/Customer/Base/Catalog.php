<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer\Base;

/**
 * Catalog
 */
abstract class Catalog extends \XLite\Controller\Customer\ACustomer
{
    /**
     * getModelObject
     *
     * @return \XLite\Model\AEntity
     */
    abstract protected function getModelObject();

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->params[] = 'category_id';
    }

    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        \XLite\Core\Request::getInstance()->category_id = intval($this->getCategoryId());

        parent::handleRequest();
    }

    /**
     * Return current (or default) category object
     *
     * @return \XLite\Model\Category
     */
    public function getCategory()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')->getCategory($this->getCategoryId());
    }

    /**
     * Returns the page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $model = $this->getModelObject();

        return ($model && $model->getName()) ? $model->getName() : parent::getTitle();
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
     * Return the page title parent category part (for the <title> tag)
     *
     * @return string
     */
    public function getTitleParentPart()
    {
        $categoryToGetName = null;

        if (!(in_array($this->getTarget(), ['main', 'category']))) {
            $categoryToGetName = $this->getCategory();
        } elseif ($this->getCategory() && $this->getCategory()->getParent()) {
            $categoryToGetName = $this->getCategory()->getParent();
        }

        return $categoryToGetName && $categoryToGetName->isVisible() && $categoryToGetName->getDepth() !== -1
            ? $categoryToGetName->getName()
            : '';
    }

    /**
     * getDescription
     *
     * @return string
     */
    public function getDescription()
    {
        $model = $this->getModelObject();

        return $model ? $model->getDescription() : null;
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $model = $this->getModelObject();

        if ($model) {
            $result = $model->getMetaDesc() ?: $this->getDescription();

        } else {
            $result = parent::getMetaDescription();
        }

        return $result;
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        $model = $this->getModelObject();

        return $model ? $model->getMetaTags() : parent::getKeywords();
    }


    /**
     * Return path for the current category
     *
     * @return array
     */
    protected function getCategoryPath()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategoryPath($this->getCategoryId());
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (!$this->isAJAX()) {
            \XLite\Core\Session::getInstance()->productListURL = $this->getURL();
        }
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
     * Return link to category page
     *
     * @param \XLite\Model\Category $category Category model object to use
     *
     * @return string
     */
    protected function getCategoryURL(\XLite\Model\Category $category)
    {
        return $this->buildURL('category', '', array('category_id' => $category->getCategoryId()));
    }

    /**
     * Should we show category sibling in location path
     *
     * @param \XLite\Model\Category $category       Node category
     *
     * @return boolean
     */
    protected function isShowCategorySiblings()
    {
        return false;
    }

    /**
     * Category sibling count to show
     *
     * @return integer
     */
    public function getCategorySiblingsLimit()
    {
        return 10;
    }

    /**
     * Prepare subnodes for the location path node
     *
     * @param \XLite\Model\Category $category       Node category
     *
     * @return array
     */
    protected function getCategorySiblings(\XLite\Model\Category $category, $siblingsLimit = null)
    {
        $nodes = array();

        // We need one more item because it is simple way to determine
        // if there is more than items than limit or less
        $limit = $siblingsLimit
            ?: $this->getCategorySiblingsLimit();

        foreach ($category->getSiblingsFramed($limit, true) as $sibling) {
            $nodes[] = \XLite\View\Location\Node::create(
                $sibling->getName(),
                $this->getCategoryURL($sibling)
            );
        }

        return $nodes;
    }

    /**
     * Prepare subnodes for the location path node
     *
     * @param \XLite\Model\Category $category Node category
     *
     * @return array
     */
    protected function getLocationNodeSubnodes(\XLite\Model\Category $category)
    {
        return $this->isShowCategorySiblings($category)
            ? $this->getCategorySiblings($category)
            : [];
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        foreach ($this->getCategoryPath() as $category) {
            if ($category->isVisible()) {
                $this->addLocationNode(
                    $category->getName(),
                    $this->getCategoryURL($category),
                    $this->getLocationNodeSubnodes($category)
                );

            } else {
                break;
            }
        }
    }

    /**
     * Get cart fingerprint exclude keys
     *
     * @return array
     */
    protected function getCartFingerprintExclude()
    {
        return array('shippingMethodsHash', 'paymentMethodsHash', 'shippingMethodId', 'paymentMethodId', 'shippingTotal');
    }
}
