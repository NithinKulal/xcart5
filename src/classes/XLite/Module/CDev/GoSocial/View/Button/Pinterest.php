<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Button;

/**
 * Pinterest button
 *
 * @ListChild (list="buttons.share", weight="400")
 */
class Pinterest extends \XLite\Module\CDev\GoSocial\View\Button\ASocialButton
{
    /**
     * Define button attributes
     *
     * @return array
     */
    protected function defineButtonParams()
    {
        $list = array();

        $image = $this->getModelObject()->getImage();
        $list['data-media'] = isset($image) ? $image->getFrontURL() : null;
        $list['data-description'] = $this->getModelObject()->getName();

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $image = $this->getModelObject()->getImage();

        return parent::isVisible()
            && isset($image)
            && $image->isExists()
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->pinterest_use;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonType()
    {
        return self::BUTTON_CLASS_PINTEREST;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonLabel()
    {
        return static::t('Pin');
    }
}
