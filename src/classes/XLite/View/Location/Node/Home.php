<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Location\Node;

/**
 * Home node
 */
class Home extends \XLite\View\Location\Node
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'location/home.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_NAME]->setValue(static::t('Home'));
        $this->widgetParams[self::PARAM_LINK]->setValue($this->buildURL());
    }

    /**
     * Get SVG image
     *
     * @param string $path Path
     * @param string $interface Interface code OPTIONAL
     *
     * @return string
     */
    protected function getSVGImage($path, $interface = null)
    {
        $content = parent::getSVGImage($path, $interface);

        if ($content) {
            $content = str_replace(
                '<title></title>',
                '<title>' . $this->getName() . '</title>',
                $content
            );
        } else {
            return $this->getName();
        }

        return $content;
    }
}
