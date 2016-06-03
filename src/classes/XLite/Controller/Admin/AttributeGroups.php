<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Attribute groups controller
 */
class AttributeGroups extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'product_class_id');

    /**
     * Product class
     *
     * @var \XLite\Model\ProductClass
     */
    protected $productClass;


    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && (
                $this->getProductClass()
                || !\XLite\Core\Request::getInstance()->product_class_id
            ) && $this->isAJAX();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Manage attribute groups');
    }

    /**
     * Get product class
     *
     * @return \XLite\Model\ProductClass
     */
    public function getProductClass()
    {
        if (
            is_null($this->productClass)
            && \XLite\Core\Request::getInstance()->product_class_id
        ) {
            $this->productClass = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')
                ->find(intval(\XLite\Core\Request::getInstance()->product_class_id));
        }

        return $this->productClass;
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        parent::doActionUpdateItemsList();

        if (!$this->isActionError()) {
            $this->setSilenceClose();
            \XLite\Core\Event::updateAttributeGroups();
        }
    }
}
