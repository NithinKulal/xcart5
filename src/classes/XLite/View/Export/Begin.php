<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Export;

/**
 * Begin section
 */
class Begin extends \XLite\View\RequestHandler\ARequestHandler
{
    const PARAM_PRESELECT = 'preselect';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PRESELECT => new \XLite\Model\WidgetParam\TypeString('Preselected class', 'XLite\Logic\Export\Step\Products'),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_PRESELECT;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'export/begin.twig';
    }

    /**
     * Return widget default template
     *
     * @return string[]
     */
    protected function getSections()
    {
        return array(
            'XLite\Logic\Export\Step\Products'   => 'Products',
            'XLite\Logic\Export\Step\Attributes' => 'Classes & Attributes',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox' => 'Product attributes values',
            'XLite\Logic\Export\Step\Orders'     => 'Orders',
            'XLite\Logic\Export\Step\Categories' => 'Categories',
            'XLite\Logic\Export\Step\Users'      => 'Customers',
        );
    }

    /**
     * Check section is selected or not
     *
     * @param string $class Class
     *
     * @return boolean
     */
    protected function isSectionSelected($class)
    {
        return $this->getParam(static::PARAM_PRESELECT) == $class && !$this->isSectionDisabled($class)
            && !$this->isSectionDisabled($class);
    }

    /**
     * Avoid using preselect from session
     *
     * @param string $param Parameter name
     *
     * @return mixed
     */
    protected function getSavedRequestParam($param)
    {
        $result = null;

        if (static::PARAM_PRESELECT != $param) {
            $result = parent::getSavedRequestParam($param);
        }

        return $result;
    }

    /**
     * Check section is disabled or not
     *
     * @param string $class Class
     *
     * @return boolean
     */
    protected function isSectionDisabled($class)
    {
        $found = false;

        $classes = array();

        $classes[] = $class;

        if ('XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox' == $class) {
            $classes[] = 'XLite\Logic\Export\Step\AttributeValues\AttributeValueSelect';
            $classes[] = 'XLite\Logic\Export\Step\AttributeValues\AttributeValueText';
        }

        foreach ($classes as $c) {
            $class = new $c;
            if ($found = (0 < $class->count())) {
                break;
            }
        }

        return !$found;
    }

    /**
     * Check - charset enabledor not
     * 
     * @return boolean
     */
    protected function isCharsetEnabled()
    {
        return \XLite\Core\Iconv::getInstance()->isValid();
    }
}
