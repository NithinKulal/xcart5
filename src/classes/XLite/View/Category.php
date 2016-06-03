<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Category widget
 *
 * @ListChild (list="center", zone="customer")
 */
class Category extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'category';
        $result[] = 'main';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/content/category_description.twig';
    }

    /**
     * Return description with postprocessing WEB LC root constant
     *
     * @return string
     */
    protected function getDescription()
    {
        return $this->getCategory()->getViewDescription();
    }
}
