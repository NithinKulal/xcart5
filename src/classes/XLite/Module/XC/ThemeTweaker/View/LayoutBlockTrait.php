<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Using class MUST implement LayoutBlockInterface. Used for widget that accepts view list overrides in layout editor mode.
 */
trait LayoutBlockTrait
{
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            array(
                'modules/XC/ThemeTweaker/layout_editor/layout_block_controller.js',
            )
        );
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
            static::PARAM_DISPLAY_GROUP => new \XLite\Model\WidgetParam\TypeString('Widget display group', ''),
        );
    }

    protected function getDisplayGroup()
    {
        return $this->getParam(static::PARAM_DISPLAY_GROUP);
    }
}
