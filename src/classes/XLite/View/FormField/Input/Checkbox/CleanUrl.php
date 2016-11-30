<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;


class CleanUrl extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/checkbox/clean_url.css';

        return $list;
    }

    /**
     * Return forced widget parameters list
     *
     * @return \XLite\Model\WidgetParam\AWidgetParam[]
     */
    protected function getForcedWidgetParams()
    {
        return [
            self::PARAM_ON_LABEL => new \XLite\Model\WidgetParam\TypeString('On label', static::t('Enabled')),
            self::PARAM_OFF_LABEL => new \XLite\Model\WidgetParam\TypeString('Off label', static::t('Disabled')),
            self::PARAM_LABEL => new \XLite\Model\WidgetParam\TypeString('Label', static::t('Enable clean URL')),
            self::PARAM_LABEL_HELP => new \XLite\Model\WidgetParam\TypeString('Label help', static::t('More information about clean urls in X-Cart is available in ', ['url' => $this->getCleanURLArticleURL()])),
        ];
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        foreach ($this->getForcedWidgetParams() as $key => $param) {
            $this->widgetParams[$key] = $param;
        }
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/checkbox/clean_url.twig';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getOrigFieldTemplate()
    {
        return $this->getDir() . LC_DS . parent::getFieldTemplate();
    }
}