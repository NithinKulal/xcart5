<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine;

/**
 * IF(condition, then, else) MySQL function realisation
 */
class IfFunction extends \Doctrine\ORM\Query\AST\Functions\FunctionNode
{
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

        $this->ifCondition = $parser->ConditionalExpression();
        $parser->match(\Doctrine\ORM\Query\Lexer::T_COMMA);

        try {
            $this->ifThen = $parser->FunctionDeclaration();
        } catch (\Doctrine\ORM\Query\QueryException $e) {
            $this->ifThen = $parser->ScalarExpression();
        }
        $parser->match(\Doctrine\ORM\Query\Lexer::T_COMMA);

        try {
            $this->ifElse = $parser->FunctionDeclaration();
        } catch (\Doctrine\ORM\Query\QueryException $e) {
            $this->ifElse = $parser->ScalarExpression();
        }

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
        return 'IF('
            . $sqlWalker->walkConditionalExpression($this->ifCondition) . ', '
            . $sqlWalker->walkSimpleArithmeticExpression($this->ifThen) . ', '
            . $sqlWalker->walkSimpleArithmeticExpression($this->ifElse) . ')';
    }

}

