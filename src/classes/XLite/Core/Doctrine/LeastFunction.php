<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * LEAST(value1, value2, ...) MySQL function realisation
 */
class LeastFunction extends \Doctrine\ORM\Query\AST\Functions\FunctionNode
{
    private $field = null;
    private $values = array();

    /**
     * Parse function
     *
     * @param \Doctrine\ORM\Query\Parser $parser Parser
     *
     * @return void
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        
        $this->field = $parser->ArithmeticPrimary();
        $lexer = $parser->getLexer();
        
        while (count($this->values) < 1 || $lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS) {
            $parser->match(Lexer::T_COMMA);
            $this->values[] = $parser->ArithmeticPrimary();
        }
        
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Get SQL query part
     *
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker SQL walker
     *
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $query = 'LEAST(';
        $query .= $this->field->dispatch($sqlWalker);
        $query .= ', ';
        
        for ($i = 0; $i < count($this->values); $i++) {
            if ($i > 0) {
                $query .= ', ';
            }
            $query .= $this->values[$i]->dispatch($sqlWalker);
        }
        
        $query .= ')';
        
        return $query;
    }
}