<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\TokenParser;

use Twig_Error_Syntax;
use Twig_Token;
use Twig_TokenParser;

class WidgetList extends Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $name = $this->parser->getExpressionParser()->parseExpression();

        $params = $stream->nextIf(Twig_Token::NAME_TYPE, 'with')
            ? $this->parser->getExpressionParser()->parseExpression() : null;

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new \XLite\Core\Templating\Twig\Node\WidgetList($name, $params, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'widget_list';
    }
}