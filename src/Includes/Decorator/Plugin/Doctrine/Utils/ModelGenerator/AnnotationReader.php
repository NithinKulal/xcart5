<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Utils\ModelGenerator;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\MappedSuperclass;

/**
 * Annotation reader that adds @Entity annotation to any class is not Entity or MappedSuperclass already.
 * It is used to gather mapping metadata of base entity classes (which are not entities) in order to properly generate field accessors in them (ModelGenerator).
 *
 * TODO: remove when ModelGenerator will no longer be used.
 */
class AnnotationReader extends SimpleAnnotationReader
{
    /**
     * {@inheritDoc}
     */
    public function getClassAnnotations(\ReflectionClass $class)
    {
        $annotations = parent::getClassAnnotations($class);

        if (is_subclass_of($class->name, 'XLite\Model\AEntity')) {
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Entity || $annotation instanceof MappedSuperclass) {
                    return $annotations;
                }
            }

            $annotations[] = new Entity();
        }

        return $annotations;
    }
}
