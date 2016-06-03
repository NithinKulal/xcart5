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
     * Return the current product title
     *
     * @return string
     */
    public function getProductTitle()
    {
        return $this->getProduct()
            ? $this->getProduct()->getName()
            : null;
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
     * Return target product title
     *
     * @return string
     */
    public function getRequestTargetProductTitle()
    {
        $product = \XLite\Core\Database::getRepo('\XLite\Model\Product')->find($this->getRequestTargetProductId());

        return $product
            ? $product->getName()
            : null;
    }

    /**
     * Return current review profile Id
     *
     * @return integer
     */
    public function getProfileId()
    {
        return $this->getProfile()
            ? $this->getProfile()->getProfileId()
            : null;
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

        parent::setReturnUrl($url);
    }

    /**
     * Return product Id from request
     *
     * @return integer
     */
    protected function getRequestProductId()
    {
        $targetProductId = $this->getRequestTargetProductId();

        return $targetProductId ? : \XLite\Core\Request::getInstance()->product_id;
    }

    /**
     * Return product from request
     *
     * @return \XLite\Model\Product
     */
    protected function getRequestProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getRequestProductId());
    }

    /**
     * Create new model
     *
     * @return void
     */
    protected function doActionCreate()
    {
        $data = \XLite\Core\Request::getInstance()->getData();
        $profile = $data['profile'] === '' ? false : $data['profile'];
        $profileText = empty($data['profile_text']) ? false : $data['profile_text'];
        unset($data['profile_text']);

        unset($data['profile']);

        $review = new \XLite\Module\XC\Reviews\Model\Review();
        $review->map($data);
        $review->setStatus(\XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED);
        $review->setIp(inet_pton($_SERVER['REMOTE_ADDR']) ?: 0);

        if ($profile && intval($profile) > 0) {
            $this->updateProfile($review, intval($profile), $profileText);

        } elseif ($profileText) {
            $review->setReviewerName(trim($profileText));
        }

        $product = $this->getRequestProduct()
            ?:
            (\XLite\Core\Database::getRepo('XLite\Model\Product')->find($data['product_title']) ?: $this->findProductBySubstring($data['product_title_text']));

        if ($product) {
            $review->setProduct($product);
            $product->addReviews($review);

            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo(
                static::t('Review has been created')
            );
        } else {
            \XLite\Core\TopMessage::addError(
                static::t(
                    'Review has not been created since product is not found',
                    array('product' => $data['product_title'])
                )
            );
        }

        $this->setReturnUrl();
        $this->setHardRedirect();
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        /** @var \XLite\Module\XC\Reviews\Model\Review $review */
        $review = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->find($this->getId());

        $data = \XLite\Core\Request::getInstance()->getData();
        $profile = empty($data['profile']) ? false : $data['profile'];
        unset($data['profile']);

        $notFilteredData = \XLite\Core\Request::getInstance()->getNonFilteredData();
        $profileText = empty($notFilteredData['profile_text']) ? false : $notFilteredData['profile_text'];
        unset($data['profile_text']);

        $review->map($data);

        if ($profile && intval($profile) > 0) {
            $this->updateProfile($review, intval($profile), $profileText);

        } elseif (!empty($profileText) && !$this->isSameReviewer($profileText, $review)) {
            $review->setReviewerName(trim($profileText));
            $review->setProfile(null);
            $review->setEmail('');
        }

        $product = null;

        if ($data['product_title']) {
            $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($data['product_title']);

        } elseif($data['product_title_text']) {
            $product = $this->findProductBySubstring($data['product_title_text']);

        } else {
            $product = $this->getRequestProduct();
        }

        if ($product) {
            $review->setProduct($product);
            $product->addReviews($review);

            \XLite\Core\Database::getEM()->flush();
        } else {
            \XLite\Core\TopMessage::addError(
                static::t(
                    'Review has not been updated since product is not found',
                    array('product' => $data['product_title'])
                )
            );
        }

        $this->setReturnUrl();
        $this->setHardRedirect();
    }

    /**
     * Return true if $profileText is the same as user in $review
     *
     * @param string                                $profileText Profile text passed from the request
     * @param \XLite\Module\XC\Reviews\Model\Review $review      Review model
     *
     * @return boolean
     */
    protected function isSameReviewer($profileText, $review)
    {
        $profileText = trim($profileText);

        $name = $review->getReviewerName();
        $email = $review->getEmail();

        if ($email) {
            $name = trim(trim($name) . ' <' . $email . '>');
        }

        return $name == $profileText;
    }

    /**
     * Modify model
     *
     * @return void
     */
    protected function doActionModify()
    {
        ($this->getModelForm()->getModelObject()->isPersistent())
            ? $this->doActionUpdate()
            : $this->doActionCreate();
    }

    /**
     * Approve review
     *
     * @return void
     */
    protected function doActionApprove()
    {
        \XLite\Core\Request::getInstance()->status = \XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED;
        $this->doActionUpdate();
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
     * Update the reviewer name, email and profile
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $review    Review model object
     * @param integer                               $profileId Profile identificator
     *
     * @return void
     */
    protected function updateProfile(\XLite\Module\XC\Reviews\Model\Review $review, $profileId, $profileText)
    {
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);

        if ($profile) {
            $review->setProfile($profile);
            if ($name = $profile->getName(false)) {
                $review->setReviewerName($name);

            } elseif (!empty($profileText) && $review->getEmail() != $profile->getLogin()) {
                $review->setReviewerName('');
            }
            $review->setEmail($profile->getLogin());
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

    /**
     * Alias
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    protected function getEntity()
    {
        return $this->getReview();
    }

    /*
     * Find product by substring
     * Function is used if product is not selected in autocomplete selector
     *
     * @param string $substring Product name or SKU
     *
     * @return \XLite\Model\Product
     */
    protected function findProductBySubstring($substring)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Product::P_SUBSTRING} = $substring;
        $cnd->{\XLite\Model\Repo\Product::P_BY_SKU} = 'Y';
        $cnd->{\XLite\Model\Repo\Product::P_BY_TITLE} = 'Y';
        $cnd->{\XLite\Model\Repo\Product::P_BY_DESCR} = null;

        $products = \XLite\Core\Database::getRepo('\XLite\Model\Product')->search($cnd);

        return $products ? $products[0] : null;
    }
}
