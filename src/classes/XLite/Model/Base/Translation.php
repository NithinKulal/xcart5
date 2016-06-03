<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use XLite\Core\Doctrine\ORM\Mapping\MetadataLoaderInterface;

/**
 * Object translation
 *
 * @MappedSuperclass
 */
abstract class Translation extends \XLite\Model\AEntity implements MetadataLoaderInterface
{
    /**
     * Default code
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * Label unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $label_id;

    /**
     * Label language code
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $code = self::DEFAULT_LANGUAGE;

    /**
     * Translation owner. Association metadata will be populated in loadMetadata method
     *
     * @var \XLite\Model\AEntity
     */
    protected $owner;

    /**
     * Return list of class properties which are not translated
     *
     * @return array
     */
    public static function getInternalProperties()
    {
        return array('label_id', 'code');
    }

    /**
     * Return the owner object
     *
     * @return \XLite\Model\AEntity
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Check cache after enity persist or remove
     *
     * @return void
     */
    public function checkCache()
    {
        parent::checkCache();

        // Check translation owner cache
        if ($this->getOwner()) {
            $this->getOwner()->checkCache();
        }
    }

    public static function loadMetadata(ClassMetadata $metadata, ClassMetadataFactory $metadataFactory)
    {
        $className = get_called_class();

        $isEntity = !$metadata->isMappedSuperclass;

        if ($isEntity && !isset($metadata->associationMappings['owner'])) {
            $ownerClassName = substr($className, 0, -strlen('Translation'));

            $ownerId = $metadataFactory->getMetadataFor($ownerClassName)->getIdentifier();

            (new ClassMetadataBuilder($metadata))
                ->createManyToOne('owner', $ownerClassName)
                ->inversedBy('translations')
                ->addJoinColumn('id', $ownerId[0], true, false, 'CASCADE')
                ->build();
        }
    }
}
