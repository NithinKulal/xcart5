<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;


/**
 * Image-based button
 */
class Image extends \XLite\View\Button\Regular
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/image.twig';
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();
        return array_merge($list, $this->getImageAttributes());
    }

    /**
     * Defines the specific image attributes
     *
     * @return array
     */
    protected function getImageAttributes()
    {
        $list = array();
        $list['type'] = 'image';
        $list['src'] = \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'images/spacer.gif',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL
        );
        if (!isset($list['title'])) {
            $list['title'] = static::t($this->getButtonLabel());
        }
        $list['alt'] = $list['title'];

        return $list;
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return parent::getDefaultJSCode() . ' return false;';
    }
}
