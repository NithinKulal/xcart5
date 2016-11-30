<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Templating\Twig\Extension;

use Twig_SimpleFunction;
use XLite\Module\XC\ThemeTweaker\Core\Templating\Twig\Functions;
use XLite\Module\XC\ThemeTweaker\Core\Templating\Twig\TokenParser\XCartInclude;

class XCart extends \XLite\Core\Templating\Twig\Extension\XCart implements \XLite\Base\IDecorator
{
    public function getTokenParsers()
    {
        $result = parent::getTokenParsers();
        $result[] = new XCartInclude();

        return $result;
    }

    public function getFunctions()
    {
        $result = parent::getFunctions();

        $functions = new Functions();
        $result[] = new Twig_SimpleFunction('include', [$functions, 'xcart_include'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['all']]);

        return $result;
    }
}
