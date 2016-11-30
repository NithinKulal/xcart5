<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Social sign-in widget
 */

trait SocialLoginSeparatorTrait {
    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            'separatorText' => new \XLite\Model\WidgetParam\TypeString(
                'Separator text', 'or'),
            'separatorPosition' => new \XLite\Model\WidgetParam\TypeSet(
                'Separator position',
                'after',
                static::getAvailableSeparatorPositions()
            )
        );
    }

    protected function getSeparatorText()
    {
        return static::t($this->getParam('separatorText'));
    }

    protected function getSeparatorPosition()
    {
        return $this->getParam('separatorPosition');
    }

    protected static function getAvailableSeparatorPositions()
    {
        return array(
            'before',
            'after'
        );
    }
}