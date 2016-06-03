<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button\Customer;

/**
 * Edit review button widget
 *
 */
class EditReview extends \XLite\View\Button\APopupButton
{
    /*
     * Widget param names
     */
    const PARAM_REVIEW = 'review';

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Reviews/button/js/edit_review.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/button/edit_review.twig';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_REVIEW => new \XLite\Model\WidgetParam\TypeObject('Review', null, false, '\XLite\Module\XC\Reviews\Model\Review'),
        );
    }

    /**
     * Get review
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    protected function getReview()
    {
        return $this->getParam(self::PARAM_REVIEW);
    }

    /**
     * Get review id
     *
     * @return integer
     */
    protected function getId()
    {
        return $this->getReview()->getId();
    }

    /**
     * Get product id
     *
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getReview()->getProduct()->getProductId();
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target'        => 'review',
            'id'            => $this->getId(),
            'product_id'    => $this->getProductId(),
            'return_target' => \XLite\Core\Request::getInstance()->target,
            'widget'        => '\XLite\Module\XC\Reviews\View\Customer\ModifyReview',
        );
    }

    /**
     * Return CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' edit-review';
    }
}
