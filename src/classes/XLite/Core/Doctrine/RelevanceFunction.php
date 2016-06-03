<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine;

/**
 * RELEVANCE(search_words, title_field_name, text_field_name)
 *
 * IF(condition, then, else) MySQL function realisation
 */
class RelevanceFunction extends \Doctrine\ORM\Query\AST\Functions\FunctionNode
{
    /**
     * Relevance search words
     *
     * @var \Doctrine\ORM\Query\AST\Literal
     */
    protected $relevanceSearchWords = null;

    /**
     * Relevance title field
     *
     * @var \Doctrine\ORM\Query\AST\PathExpression
     */
    protected $relevanceTitleField = null;

    /**
     * Relevance text field
     *
     * @var \Doctrine\ORM\Query\AST\PathExpression
     */
    protected $relevanceTextField = null;

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

        $this->relevanceSearchWords = $parser->Literal();
        $parser->match(\Doctrine\ORM\Query\Lexer::T_COMMA);

        $this->relevanceTitleField = $parser->SingleValuedPathExpression();
        $parser->match(\Doctrine\ORM\Query\Lexer::T_COMMA);

        $this->relevanceTextField = $parser->SingleValuedPathExpression();
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
        $sql = '';
        $phrase = trim($this->relevanceSearchWords->dispatch($sqlWalker), '\'');
        $words = explode(' ', $phrase);
        $titleFactor = round((20 / count($words)), 2);
        $textFactor  = round((10 / count($words)), 2);

        $sql .= '(';
        $sql .= sprintf(' IF (%s LIKE \'%%%s%%\', 60, 0)', $this->relevanceTitleField->dispatch($sqlWalker), $phrase);
        $sql .= '+';
        $sql .= sprintf(' IF (%s LIKE \'%%%s%%\', 10, 0)', $this->relevanceTextField->dispatch($sqlWalker), $phrase);
        foreach ($words as $word) {
            $sql .= '+';
            $sql .= sprintf(
                ' IF (%s LIKE \'%%%s%%\', %s, 0)',
                $this->relevanceTitleField->dispatch($sqlWalker),
                $word,
                $titleFactor
            );

            $sql .= '+';
            $sql .= sprintf(
                ' IF (%s LIKE \'%%%s%%\', %s, 0)',
                $this->relevanceTextField->dispatch($sqlWalker),
                $word,
                $textFactor
            );
        }
        $sql .= ')';

        return $sql;
    }
}
