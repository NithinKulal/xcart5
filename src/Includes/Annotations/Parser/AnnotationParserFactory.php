<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Annotations\Parser;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;

class AnnotationParserFactory
{
    /**
     * @return AnnotationParserInterface
     */
    public function create()
    {
        AnnotationRegistry::registerFile(LC_DIR_ROOT . 'vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

        AnnotationRegistry::registerAutoloadNamespace('Includes\Annotations', LC_DIR);

        $docParser = new DocParser();
        $docParser->addNamespace('Doctrine\ORM\Mapping');
        $docParser->addNamespace('Includes\Annotations');
        $docParser->setIgnoreNotImportedAnnotations(true);

        return new LC_DependenciesAnnotationParser($docParser);
    }
}