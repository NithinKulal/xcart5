<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product\Search\Customer;

use XLite\View\CacheableTrait;

/**
 * Simple form for searching products widget
 */
class Simple extends \XLite\View\AView
{
    use CacheableTrait;

    /**
     * Widget params
     */
    const PARAM_POSITION = 'position';

    /**
     * Position
     */
    const POSITION_DEFAULT = 'default';
    const POSITION_RESPONSIVE = 'responsive';

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'product/search/simple_form.css';

        return $list;
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/search/simple_form.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isCheckoutLayout();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_POSITION => new \XLite\Model\WidgetParam\TypeString('Position', static::POSITION_DEFAULT),
        );
    }

    /**
     * Returns id attribute value for substring input field
     *
     * @return string
     */
    protected function getSearchSubstringInputId()
    {
        return $this->getParam(static::PARAM_POSITION) == static::POSITION_DEFAULT
            ? 'substring-default'
            : 'substring-responsive';
    }

    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [$this->getWidgetParams(static::PARAM_POSITION)->value]
        );
    }
}
