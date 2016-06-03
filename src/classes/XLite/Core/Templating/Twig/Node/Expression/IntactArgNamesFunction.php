<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\Node\Expression;

/**
 * Customize default Twig behavior to not to underscorize camel-cased names
 * in widget & widget_list functions arguments
 */
class IntactArgNamesFunction extends \Twig_Node_Expression_Function
{
    protected function normalizeName($name)
    {
        return $name;
    }
}
