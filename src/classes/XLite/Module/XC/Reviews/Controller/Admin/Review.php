<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Admin;

/**
 * Review modify controller
 */
class Review extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return review id from request
     *
     * @return integer
     */
    public function getId()
    {
        return intval(\XLite\Core\Request::getInstance()->id);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->getReview() && $this->getReview()->isPersistent()) {
            $label = $this->getReview()->isApproved() ? 'Edit review' : 'Approve review';
        } else {
            $label = 'Add review';
        }

        return static::t($label);
    }

    /**
     * Return target product Id
     *
     * @return integer
     */
    public function getRequestTargetProductId()
    {
        return intval(\XLite\Core\Request::getInstance()->target_product_id);
    }

    /**
     * Alias
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    public function getReview()
    {
        $result = \XLite\Core\Database::getRepo('\XLite\Module\XC\Reviews\Model\Review')->find($this->getId());

        return $result ? : new \XLite\Module\XC\Reviews\Model\Review();
    }

    /**
     * Return current product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return ($this->getReview())
            ? $this->getReview()->getProduct()
            : null;
    }

    /**
     * Return current product Id
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->getProduct()
            ? $this->getProduct()->getProductId()
            : null;
    }

    /**
     * Set return URL
     *
     * @param string $url Url OPTIONAL
     *
     * @return void
     */
    public function setReturnURL($url = '')
    {
        $targetProductId = $this->getRequestTargetProductId();

        $url = $targetProductId
            ? \XLite\Core\Converter::buildURL(
                'product',
                '',
                array('product_id' => $targetProductId, 'page' => 'product_reviews')
            )
            : \XLite\Core\Converter::buildURL('reviews');

        parent::setReturnURL($url);
    }

    /**
     * Modify model
     *
     * @return void
     */
    protected function doActionModify()
    {
        $this->getModelForm()->performAction('modify');
        $this->setReturnURL();
        $this->setHardRedirect();
    }

    /**
     * Approve review
     *
     * @return void
     */
    protected function doActionApprove()
    {
        $this->getModelForm()->getModelObject()->setStatus(\XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED);
        $this->doActionModify();
    }

    /**
     * doActionDelete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $review = $this->getReview();

        if (isset($review)) {
            \XLite\Core\Database::getEM()->remove($review);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo(
                static::t('Review has been deleted')
            );

            $this->setReturnURL();
            $this->setHardRedirect();
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\XC\Reviews\View\Model\Review';
    }
}
