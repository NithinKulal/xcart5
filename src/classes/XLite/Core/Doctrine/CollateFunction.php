<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine;

/**
 * COLLATE MySQL operator realisation (as function COLLATE(field, collate) )
 */
class CollateFunction extends \Doctrine\ORM\Query\AST\Functions\FunctionNode
{
    /**
     * Expression to collate
     *
     * @var string
     */
    protected $expressionToCollate = '';

    /**
     * Collation
     *
     * @var string
     */
    protected $collation = '';

    /**
     * Parse function
     *
     * @param \Doctrine\ORM\Query\Parser $parser Parser
     *
     * @return void
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(\Doctrine\ORM\Query\Lexer::T_IDENTIFIER);
        $parser->match(\Doctrine\ORM\Query\Lexer::T_OPEN_PARENTHESIS);
        $this->expressionToCollate = $parser->StringPrimary();

        $parser->match(\Doctrine\ORM\Query\Lexer::T_COMMA);

        $parser->match(\Doctrine\ORM\Query\Lexer::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $this->collation = $lexer->token['value'];

        $parser->match(\Doctrine\ORM\Query\Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Get SQL query part
     *
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker SQL walker
     *
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return sprintf(
            '%s COLLATE %s',
            $this->expressionToCollate->dispatch($sqlWalker),
            $this->collation
        );
    }
}

