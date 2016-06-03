<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine\ORM\Mapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory as DoctrineClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * MetadataLoaderInterface interface allows implementers to supply their own mapping information in addition to the info retrieved by the default metadata driver.
 *
 * loadMetadata is intended to be implemented in the similar way the StaticPHPDriver is designed (see http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function) but in addition it is given an instance of ClassMetadataFactory to perform cross-entity mapping. An example implementation is XLite\Model\Base\Translation.
 */
interface MetadataLoaderInterface
{
    public static function loadMetadata(ClassMetadata $metadata, DoctrineClassMetadataFactory $metadataFactory);
}