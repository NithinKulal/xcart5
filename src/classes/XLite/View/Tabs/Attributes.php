<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to Attributes pages (Product modify section)
 */
class Attributes extends \XLite\View\Tabs\ATabs
{
    /**
     * Widget parameter names
     */
    const PARAM_PRODUCT = 'product';

    /**
     * @todo: rename 'css_js_performance' to '...'
     *
     * @return array
     */
    protected function defineTabs()
    {
        $list = [
            'custom' => [
                'weight'   => 100,
                'title'    => static::t('Product-Specific'),
                'template' => 'product/attributes/custom.twig',
            ],
            'global' => [
                'weight'   => 200,
                'title'    => static::t('Global'),
                'template' => 'product/attributes/global.twig',
            ],
        ];

        if (1 < count($this->getParam(static::PARAM_PRODUCT)->getEditableAttributes())) {
            $list['properties'] = [
                'weight'   => 300,
                'title'    => static::t('Sort settings'),
                'template' => 'product/attributes/properties.twig',
            ];
        }

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs2.twig';
    }

    /**
     * Returns tab URL
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL(
            'product',
            '',
            [
                'page'       => 'attributes',
                'product_id' => $this->getParam(static::PARAM_PRODUCT)->getProductId(),
                'spage'      => $target,
            ]
        );
    }

    /**
     * Returns the current target
     *
     * @return string
     */
    protected function getCurrentTarget()
    {
        return \XLite\Core\Request::getInstance()->spage ?: 'custom';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product',
                null,
                false,
                'XLite\Model\Product'
            ),
        ];
    }
}
