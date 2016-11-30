<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Templating\Twig\TokenParser;

use Twig_TokenParser_Include;
use Twig_Token;
use XLite\Module\XC\ThemeTweaker\Core\Templating\Twig\Node\XCartInclude as XCartIncludeNode;

class XCartInclude extends Twig_TokenParser_Include
{
    public function parse(Twig_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        return new XCartIncludeNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }
}
