<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Annotations\Decorator;

/**
 * @Annotation
 */
class Before
{
    public $modules = [];

    public function __construct($param)
    {
        $params = is_array($param['value']) ? $param['value'] : [$param['value']];

        foreach ($params as $value) {
            $this->modules[] = $value;
        }
    }
}