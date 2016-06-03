<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Annotations\Parser;

use Doctrine\Common\Annotations\DocParser;

class AnnotationParser implements AnnotationParserInterface
{
    /**
     * @var DocParser
     */
    protected $docParser;

    public function __construct(DocParser $docParser)
    {
        $this->docParser = $docParser;
    }

    public function parse($docComment)
    {
        return $this->docParser->parse($docComment);
    }
}