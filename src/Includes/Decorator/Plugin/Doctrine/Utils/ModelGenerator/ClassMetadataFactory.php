<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Utils\ModelGenerator;

use Doctrine\DBAL\Platforms;
use ReflectionMethod;

/**
 * ClassMetadataFactory that suppresses some metadata validation exceptions in order to be able to get metadata
 * for base entity classes (including decorators).
 *
 * TODO: remove when ModelGenerator will no longer be used.
 */
class ClassMetadataFactory extends \Doctrine\ORM\Mapping\ClassMetadataFactory
{
    /**
     * Forces the factory to load the metadata of all classes known to the underlying
     * mapping driver.
     *
     * @return array The ClassMetadata instances of all mapped classes.
     */
    public function getAllMetadata()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $driver   = $this->getDriver();
        $metadata = array();
        foreach ($driver->getAllClassNames() as $className) {
            try {
                $metadata[] = $this->getMetadataFor($className);
            } catch (\Exception $e) {
                printf("- Skipping %s, because %s\n", $className, $e->getMessage());
            }
        }

        return $metadata;
    }

    /**
     * {@inheritDoc}
     */
    protected function loadMetadata($name)
    {
        $reflectionMethod = new ReflectionMethod(get_parent_class(get_parent_class($this)), 'loadMetadata');

        $reflectionMethod->setAccessible(true);

        $loaded = $reflectionMethod->invoke($this, $name);

        array_map([$this, 'getMetadataFor'], $loaded);

        return $loaded;
    }
}