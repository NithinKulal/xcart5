<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Separator;

/**
 * \XLite\View\FormField\Separator\Regular
 */
class Selectable extends \XLite\View\FormField\Separator\ASeparator
{
    const PARAM_GROUP_NAME = 'groupName';
    const PARAM_SELECTED   = 'selected';

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/selectable.css';

        return $list;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/form_field/separator';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'selectable.twig';
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
            static::PARAM_GROUP_NAME => new \XLite\Model\WidgetParam\TypeString('Group name', $this->getDefaultName()),
            static::PARAM_SELECTED   => new \XLite\Model\WidgetParam\TypeBool('Selected', false),
        );
    }
}
