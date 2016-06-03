<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ColorSchemes\Core;

/**
 * Layout manager
 */
abstract class Layout extends \XLite\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * Defines the LESS files to be part of the main LESS queue
     *
     * @param string $interface Interface to use: admin or customer values
     *
     * @return array
     */
    public function getLESSResources($interface)
    {
        $result = parent::getLESSResources($interface);
        $colorScheme = \XLite\Module\XC\ColorSchemes\Main::getColorSchemeLess();

        if (\XLite::CUSTOMER_INTERFACE === $interface && !\XLite\Module\XC\ColorSchemes\Main::isDefaultColorScheme()) {
            $result[] = $colorScheme;
        }

        return $result;
    }

    /**
     * Returns skin preview image URL
     *
     * @param \XLite\Model\Module $module Skin module
     * @param string              $color  Color
     * @param string              $type   Layout type
     *
     * @return string
     */
    public function getLayoutPreview($module, $color, $type)
    {
        $skinModule = ($module
            && '' === $color
            && 'XC' === $module->getAuthor()
            && 'ColorSchemes' === $module->getName()
        )
            ? null
            : $module;

        return parent::getLayoutPreview($skinModule, $color, $type);
    }
}
