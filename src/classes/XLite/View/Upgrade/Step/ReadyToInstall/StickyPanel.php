<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\ReadyToInstall;

/**
 * Form-based sticky panel
 */
class StickyPanel extends \XLite\View\Base\StickyPanel
{
    const PARAM_PARENT_LIST = 'parentList';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PARENT_LIST => new \XLite\Model\WidgetParam\TypeString('Parent view list', ''),
        );
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return $this->getParam(self::PARAM_PARENT_LIST);
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'upgrade/step/ready_to_install/buttons/panel';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();
        $class .= ' form-do-not-change-activation';

        return $class;
    }

}