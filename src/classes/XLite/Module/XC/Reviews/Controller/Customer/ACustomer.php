<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Customer;

/**
 * Review modify controller
 *
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Current product cache
     *
     * @var \XLite\Model\Product $product
     */
    protected $product = false;

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if ($this->product === false) {
            $this->product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
        }

        return $this->product;
    }

    /**
     * Return product id of the current page
     *
     * @return integer
     */
    public function getProductId()
    {
        $productId = parent::getProductId();
        if (empty($productId)) {
            $cellName = \XLite\Module\XC\Reviews\View\ItemsList\Model\Customer\Review::getSessionCellName();
            $cell = (array)\XLite\Core\Session::getInstance()->$cellName;

            $productId = isset($cell['product_id']) ? $cell['product_id'] : null;
        }

        return $productId;
    }

    /**
     * Return TRUE if customer already reviewed product
     *
     * @return boolean
     */
    public function isProductReviewedByUser()
    {
        $product = $this->getProduct();
        $result = false;
        if (isset($product)) {
            $result = $product->isReviewedByUser($this->getProfile());
        }
        return $result;
    }

    /**
     * Return TRUE if customer can add review for product
     *
     * @return boolean
     */
    public function isAllowedAddReview()
    {
        $result = (bool) $this->getProfile();

        if ($result && $this->isProductReviewedByUser()) {
            $result = false;
        }

        if ($result
            && $this->isPurchasedCustomerOnlyAbleLeaveFeedback()
            && !$this->isUserPurchasedProduct()
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Return message instead of 'Add review' button if customer is not allowed to add review
     *
     * @return string
     */
    public function getAddReviewMessage()
    {
        $message = null;

        if (!$this->getProfile()) {
            $message = 'Please sign in to add review';
        }

        if (empty($message) && $this->isProductReviewedByUser()) {
            $message = 'You have already reviewed this product';
        }

        if (empty($message) && $this->isPurchasedCustomerOnlyAbleLeaveFeedback()) {
            $message = 'Only customers who purchased this product can leave feedback on this product';
        }

        return static::t($message);
    }

    /**
     * Return TRUE if only customers who purchased this product can leave feedback
     *
     * @return boolean
     */
    public function isPurchasedCustomerOnlyAbleLeaveFeedback()
    {
        $whoCanLeaveFeedback = \XLite\Core\Config::getInstance()->XC->Reviews->whoCanLeaveFeedback;

        return (\XLite\Module\XC\Reviews\Model\Review::PURCHASED_CUSTOMERS == $whoCanLeaveFeedback);
    }

    /**
     * Return true if customer purchased the specified product
     *
     * @param integer $productId Product ID
     *
     * @return boolean
     */
    protected function isUserPurchasedProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\OrderItem')
            ->countItemsPurchasedByCustomer($this->getProductId(), $this->getProfile());
    }

    /**
     * Define if review is added by current user
     *
     * @return boolean
     */
    public function isOwnReview(\XLite\Module\XC\Reviews\Model\Review $entity)
    {
        $result = false;

        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        if ($profile) {
            $result = ($entity->getProfile() == $profile);
        }

        return $result;
    }
}
