<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Labels 
 */
class Labels extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_LABELS = 'labels';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Get name of the working directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'labels';
    }

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getLabels();
    }

    /**
     * Alias
     *
     * @return array
     */
    protected function getLabels()
    {
        return $this->getParam(static::PARAM_LABELS);
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
            self::PARAM_LABELS => new \XLite\Model\WidgetParam\TypeCollection('Labels', array()),
        );
    }
}
