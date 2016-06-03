<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Form\Model;

/**
 * Modify review form
 *
 */
class Review extends \XLite\View\Form\AForm
{
    /**
     * Widget params names
     */
    const PARAM_ID = 'id';
    const PARAM_TARGET_PRODUCT_ID = 'target_product_id';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Reviews/review/style.css';

        return $list;
    }

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'review';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'modify';
    }

    /**
     * Get default class name
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' validationEngine review');
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = array(
            static::PARAM_ID => \XLite\Core\Request::getInstance()->id,
        );

        if (intval(\XLite\Core\Request::getInstance()->target_product_id) > 0) {
            $params[static::PARAM_TARGET_PRODUCT_ID] = \XLite\Core\Request::getInstance()->target_product_id;
        }

        return $params;
    }
}
