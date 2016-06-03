<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Annotations\Parser;

class LC_DependenciesAnnotationParser extends AnnotationParser
{
    public function parse($docComment)
    {
        // Workaround for '@LC_Dependencies("ASD\Some", "XC\Reviews")' syntax which is incompatible with doctrine/annotations
        // Adds braces around argument list: '@LC_Dependencies ({"ASD\Some", "XC\Reviews"})'
        $docComment = preg_replace('/@LC_Dependencies\s*\(\s*([^{\s].*?[^}\s])\s*\)/', '@LC_Dependencies({\1})', $docComment);

        return parent::parse($docComment);
    }
}