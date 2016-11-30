<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Templating\Twig\Node;

use Twig_Node_Include;
use Twig_Compiler;

class XCartInclude extends Twig_Node_Include
{
    public function compile(Twig_Compiler $compiler)
    {
        $compiler->write('$fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath(')
            ->subcompile($this->getNode('expr'))
            ->raw(');');

        $compiler->write("list(\$templateWrapperText, \$templateWrapperStart)")
            ->raw(" = \$this->getThis()->startMarker(\$fullPath);\n");

        $compiler->write("if (\$templateWrapperText) {\n")
            ->indent()
            ->raw("echo \$templateWrapperStart;\n")
            ->outdent()
            ->raw("}\n\n");

        parent::compile($compiler);

        $compiler->write("if (\$templateWrapperText) {\n")
            ->indent()
            ->write("echo \$this->getThis()->endMarker(\$fullPath, \$templateWrapperText);\n")
            ->outdent()
            ->write("}\n");
    }
}
