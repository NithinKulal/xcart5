<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Annotations;

/**
 * @Annotation
 */
class LC_Dependencies
{
    public $dependencies = [];

    public $incompatibilities = [];

    public function __construct($param)
    {
        $params = is_array($param['value']) ? $param['value'] : [$param['value']];

        foreach ($params as $value) {
            if (strpos($value, '!') === 0) {
                $this->incompatibilities[] = substr($value, 1);
            } else {
                $this->dependencies[] = $value;
            }
        }
    }
}