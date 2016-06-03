<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Attribute controller
 */
class Attribute extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'id', 'product_class_id');

    /**
     * Product class
     *
     * @var \XLite\Model\ProductClass
     */
    protected $productClass;

    /**
     * Attribute
     *
     * @var \XLite\Model\Attribute
     */
    protected $attribute;


    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && $this->isAJAX()
            && (
                $this->getProductClass()
                || !\XLite\Core\Request::getInstance()->product_class_id
            );
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
     * Get attribute
     *
     * @return \XLite\Model\Attribute
     */
    public function getAttribute()
    {
        if (
            is_null($this->attribute)
            && \XLite\Core\Request::getInstance()->id
        ) {
            $this->attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')
                ->find(intval(\XLite\Core\Request::getInstance()->id));
        }

        return $this->attribute;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $id = intval(\XLite\Core\Request::getInstance()->id);
        $model = $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($id)
            : null;

        return ($model && $model->getId())
            ? static::t('Edit attribute values')
            : static::t('New attribute');
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->getModelObject()->getId()) {
            $this->setSilenceClose();

        } else {
            $this->setInternalRedirect();
        }

        $list = new \XLite\View\ItemsList\Model\AttributeOption;
        $list->processQuick();

        if ($this->getModelForm()->performAction('modify')) {
            \XLite\Core\Event::updateAttribute(array('id' => $this->getModelForm()->getModelObject()->getId()));

            $this->setReturnUrl(
                \XLite\Core\Converter::buildURL(
                    'attribute',
                    '',
                    array(
                        'id'               => $this->getModelForm()->getModelObject()->getId(),
                        'product_class_id' => \XLite\Core\Request::getInstance()->product_class_id,
                        'widget'           => 'XLite\View\Attribute'
                    )
                )
            );
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Attribute';
    }
}
