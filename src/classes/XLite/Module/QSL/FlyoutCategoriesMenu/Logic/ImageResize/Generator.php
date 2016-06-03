<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\FlyoutCategoriesMenu\Logic\ImageResize;

/**
 * ImageResize
 */
class Generator extends \XLite\Logic\ImageResize\Generator implements \XLite\Base\IDecorator
{
    /**
     * Returns available image sizes
     *
     * @return array
     */
    public static function defineImageSizes()
    {
        $result = parent::defineImageSizes();
        $result[static::MODEL_CATEGORY]['XXXSThumbnail'] = array(16, 16);

        return $result;
    }
}
