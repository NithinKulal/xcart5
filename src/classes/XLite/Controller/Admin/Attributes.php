<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Attributes controller
 */
class Attributes extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target');

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
            );
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getProductClass()
            ? static::t(
                'Attributes for X product class',
                array(
                    'class' => $this->getProductClass()->getName()
                )
            )
            : static::t('Global attributes');
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
     * Get attribute groups
     *
     * @return array
     */
    public function getAttributeGroups()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->findByProductClass(
            $this->getProductClass()
        );
    }

    /**
     * Get attributes count
     *
     * @return array
     */
    public function getAttributesCount()
    {
        return count(
            \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findBy(
                array('productClass' => $this->getProductClass(), 'product' => null)
            )
        );
    }

}