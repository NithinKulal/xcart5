<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Templates\Data\Templates;

/**
 * Node
 *
 * @package    XLite
 */
class Node extends \Includes\DataStructure\Cell
{
    /**
     * Get tag value from class comment
     *
     * @param string $name tag name
     *
     * @return string
     */
    public function getTag($name)
    {
        $tags = $this->__get(\Includes\Decorator\ADecorator::N_TAGS);

        return isset($tags[$name = strtolower($name)]) ? $tags[$name] : null;
    }
}
