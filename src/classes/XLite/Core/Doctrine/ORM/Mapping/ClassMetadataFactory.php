<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine\ORM\Mapping;


/**
 * Custom class metadata factory
 */
class ClassMetadataFactory extends \Doctrine\ORM\Mapping\ClassMetadataFactory
{
    /**
     * {@inheritdoc}
     */
    protected function doLoadMetadata($class, $parent, $rootEntityFound, array $nonSuperclassParents)
    {
        parent::doLoadMetadata($class, $parent, $rootEntityFound, $nonSuperclassParents);

        $className = $class->getName();

        // TODO: check if it's cool to add metadata after parent doLoadMetadata worked

        if (in_array('XLite\Core\Doctrine\ORM\Mapping\MetadataLoaderInterface', class_implements($className))) {
            $className::loadMetadata($class, $this);
        }
    }
}