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
 * CONCAT_WS(separator, value1, value2, ...) MySQL function realisation
 */
class ConcatWSFunction extends \Doctrine\ORM\Query\AST\Functions\FunctionNode
{
    private $values = array();
    private $notEmpty = false;

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

        $this->values[] = $parser->ArithmeticExpression();

        $lexer = $parser->getLexer();

        while (count($this->values) < 3 || $lexer->lookahead['type'] == Lexer::T_COMMA) {
            $parser->match(Lexer::T_COMMA);
            $peek = $lexer->glimpse();
            $this->values[] = $peek['value'] == '('
                ? $parser->FunctionDeclaration()
                : $parser->ArithmeticExpression();
        }

        while ($lexer->lookahead['type'] == Lexer::T_IDENTIFIER) {
            switch (strtolower($lexer->lookahead['value'])) {
                case 'notempty':
                    $parser->match(Lexer::T_IDENTIFIER);
                    $this->notEmpty = true;
                    break;
                default:
                    $parser->match(Lexer::T_CLOSE_PARENTHESIS);
                    break;
            }
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
        $queryBuilder = array('CONCAT_WS(');

        for ($i = 0; $i < count($this->values); $i++) {
            if ($i > 0) {
                $queryBuilder[] = ', ';
            }

            $nodeSql = $sqlWalker->walkArithmeticPrimary($this->values[$i]);

            if ($this->notEmpty) {
                $nodeSql = sprintf("NULLIF(%s, '')", $nodeSql);
            }
            $queryBuilder[] = $nodeSql;
        }

        $queryBuilder[] = ')';

        return implode('', $queryBuilder);
    }
}
