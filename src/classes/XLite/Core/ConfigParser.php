<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Abstract config parser
 */
abstract class ConfigParser extends \XLite\Base
{
    /**
     * Parse both config files
     *
     * @param array|string $names Option names tree OPTIONAL
     *
     * @return array|mixed
     */
    public static function getOptions($names = null)
    {
        return \Includes\Utils\ConfigParser::getOptions($names);
    }
}
