<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * AccessLink notification widget
 */
class AccessLink extends \XLite\View\AView
{
    const PARAM_LINK = 'link';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_LINK => new \XLite\Model\WidgetParam\TypeString('Access link', ''),
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'access_link/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'access_link/access_link.twig';
    }

    /**
     * Return access link
     *
     * @return boolean
     */
    protected function getAccessLink()
    {
        return $this->getParam(static::PARAM_LINK);
    }
}