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
class Pinterest extends \XLite\View\AView
{
    /**
     * Button URL
     */
    const BUTTON_URL = '//pinterest.com/pin/create/button/?';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/button/pinterest.twig';
    }

    /**
     * Get button attributes
     *
     * @return array
     */
    protected function getButtonAttributes()
    {
        return array();
    }

    /**
     * Get button URL (query  part)
     *
     * @return array
     */
    protected function getButtonURL()
    {
        $query = array();
        foreach ($this->getButtonURLQuery() as $name => $value) {
            $query[] = $name . '=' . urlencode($value);
        }

        return static::BUTTON_URL . implode('&amp;', $query);
    }

    /**
     * Get button URL (query  part)
     *
     * @return array
     */
    protected function getButtonURLQuery()
    {
        $image = $this->getModelObject()->getImage();

        return array(
            'url'         => \XLite::getInstance()->getShopURL($this->getURL()),
            'media'       => isset($image) ? $image->getFrontURL() : null,
            'description' => $this->getModelObject()->getName(),
        );
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
}
