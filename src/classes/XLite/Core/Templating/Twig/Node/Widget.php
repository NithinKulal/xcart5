<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\Node;

use Twig_Compiler;
use Twig_Node;
use Twig_Node_Expression;
use Twig_NodeOutputInterface;

class Widget extends Twig_Node implements Twig_NodeOutputInterface
{
    public function __construct(Twig_Node_Expression $class = null, Twig_Node_Expression $params = null, $lineno, $tag = null)
    {
        parent::__construct(array('params' => $params, 'class' => $class), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler->write('$this->renderWidget(');
        if ($this->getNode('class')) {
            $compiler->subcompile($this->getNode('class'));
        } else {
            $compiler->raw('null');
        }

        if ($this->getNode('params') != null) {
            $compiler->raw(', ');
            $compiler->subcompile($this->getNode('params'));
        }

        $compiler->raw(');');
    }
}
