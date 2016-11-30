<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;


class RefundMultiple extends \XLite\View\FormField\Input\Text\Price
{
    const PARAM_LINK = 'link';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_LINK => new \XLite\Model\WidgetParam\TypeString('Link', ''),
        ];
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return parent::getCommentedData() + [
            static::PARAM_LINK => $this->getLink()
        ];
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getLink()
    {
        return $this->getParam(static::PARAM_LINK);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/multiple_refund.twig';
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/input/multiple_refund';
    }

    /**
     * getLabel
     *
     * @return string
     */
    public function getLabel()
    {
        return static::t('Refund');
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/multiple_refund.css';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/multiple_refund.js';

        return $list;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'refund-amount not-affect-recalculate not-significant';

        return $classes;
    }
}