<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Model\Repo\Base;

/**
 * Image abstract repository
 *
 * @MappedSuperclass
 */
abstract class Image extends \XLite\Model\Repo\Base\Image implements \XLite\Base\IDecorator
{
    /**
     * Get managed image repositories
     *
     * @return array
     */
    public static function getManagedRepositories()
    {
        return array(
            'XLite\Model\Image\Product\Image',
            'XLite\Model\Image\Category\Image',
            'XLite\Model\Image\Category\Banner',
            'XLite\Model\Image\BannerRotationImage',
        );
    }

    /**
     * @return string
     */
    public function getS3Prefix()
    {
        return \XLite\Model\Base\Image::IMAGES_NAMESPACE . '/' . $this->getStorageName() . '/';
    }

    /**
     * Has one or more entity with specified path
     *
     * @param string                  $path   Path
     * @param \XLite\Model\Base\Image $entity Exclude entity
     *
     * @return \XLite\Model\Base\Image
     */
    public function findOneAmazonS3ImageByPath($path, $entity = null)
    {
        return $this->defineFindOneAmazonS3ImageByPathQuery($path, $entity)->getSingleResult();
    }

    /**
     * Define query for findOneByFull() method
     *
     * @param string                  $path
     * @param \XLite\Model\Base\Image $image
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneAmazonS3ImageByPathQuery($path, $image = null)
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.path = :path AND i.storageType = :stype')
            ->setParameter('path', $path)
            ->setParameter('stype', \XLite\Model\Base\Image::STORAGE_S3);

        if ($image && $image->isPersistent()) {
            $qb->andWhere('i != :image')->setParameter('image', $image);
        }

        return $qb;
    }

    /**
     * Mass update needMigration flag got all images
     *
     * @param boolean $value New value
     *
     * @return void
     */
    public function updateNeedMigration($value)
    {
        $this->createPureQueryBuilder('i')
            ->update($this->_entityName, 'i')
            ->set('i.needMigration', (int)$value)
            ->execute();
    }

    /**
     * Return true if there are at least one S3 image
     *
     * @param boolean $resized Flag: true - take in consideration also resized images
     *
     * @return boolean
     */
    public function hasS3Images($resized = false)
    {
        $result = (bool)$this->findS3Images(1);

        if (!$result && $resized) {
            $result = (bool)$this->findS3ResizedImages(false, 1);
        }

        return $result;
    }

    /**
     * Return true if there are at least one non-S3 image
     *
     * @param boolean $resized       Flag: true - take in consideration also resized images
     * @param boolean $needMigration If true or false - needMigration flag will be considered
     * @param array   $excluded
     *
     * @return boolean
     */
    public function hasNoS3Images($resized = false, $needMigration = null, $excluded = [])
    {
        $result = (bool)$this->findNoS3Images(1, $needMigration, $excluded);

        if (!$result && $resized) {
            $result = (bool)$this->findNoS3ResizedImages(false, 1, $needMigration);
        }

        return $result;
    }

    /**
     * Count S3 images
     *
     * @return integer
     */
    public function countS3Images()
    {
        return $this->defineCountS3ImagesQuery()->getSingleScalarResult();
    }

    /**
     * Count non-S3 images
     *
     * @param boolean $needMigration If true or false - needMigration flag will be considered
     *
     * @return integer
     */
    public function countNoS3Images($needMigration = null)
    {
        return $this->defineCountNoS3ImagesQuery($needMigration)->getSingleScalarResult();
    }

    /**
     * Get number of all images and its resized icons stored in S3 location
     *
     * @return integer
     */
    public function countAllS3Images()
    {
        return $this->countS3Images() + $this->findS3ResizedImages(true);
    }

    /**
     * Get number of all images and its resized icons stored in non-S3 locations
     *
     * @return integer
     */
    public function countAllNoS3Images()
    {
        return $this->countNoS3Images() + $this->findNoS3ResizedImages(true);
    }

    /**
     * Find S3 images
     *
     * @param integer $limit Limit OPTIONAL
     *
     * @return array
     */
    public function findS3Images($limit = null)
    {
        return $this->defineFindS3ImagesQuery($limit)->getResult();
    }

    /**
     * Find non-S3 images
     *
     * @param integer $limit         Limit OPTIONAL
     * @param boolean $needMigration If true or false - needMigration flag will be considered
     * @param array   $excluded
     *
     * @return array
     */
    public function findNoS3Images($limit = null, $needMigration = null, $excluded = [])
    {
        return $this->defineFindNoS3ImagesQuery($limit, $needMigration, $excluded)->getResult();
    }

    /**
     * Find entities with resized images stored in S3 location
     *
     * @param boolean $count Get count of images OPTIONAL
     * @param integer $limit Limit OPTIONAL
     *
     * @return array
     */
    public function findS3ResizedImages($count = false, $limit = null)
    {
        $result = array();
        $resultCount = 0;

        $entities = $this->defineFindS3ResizedImages()->getResult();

        if ($entities) {

            foreach ($entities as $entity) {

                $s3icons = $entity->getS3icons();

                if (is_array($s3icons)) {

                    if ($count) {
                        $resultCount += count($s3icons);

                    } else {

                        foreach ($s3icons as $iconPath => $tmp) {
                            $result[] = array(
                                'item' => $entity,
                                'path' => $iconPath,
                            );

                            if (isset($limit) && $limit <= count($result)) {
                                break;
                            }
                        }

                        if (isset($limit) && $limit <= count($result)) {
                            // Limit has been reached - break operation
                            break;
                        }
                    }
                }
            }
        }

        return $count ? $resultCount : $result;
    }

    /**
     * Find entities with resized images stored in non-S3 locations
     *
     * @param boolean $count         Get count of images OPTIONAL
     * @param integer $limit         Limit OPTIONAL
     * @param boolean $needMigration If true or false - needMigration flag will be considered
     *
     * @return array
     */
    public function findNoS3ResizedImages($count = false, $limit = null, $needMigration = null)
    {
        $result = array();
        $resultCount = 0;

        // Get available image sizes for current entity model
        $imageSizes = \XLite\Logic\ImageResize\Generator::getModelImageSizes($this->_entityName);

        if ($imageSizes) {

            foreach ($this->iterateAll() as $data) {

                $entity = $data[0];

                if (!is_null($needMigration) && $entity->isNeedMigration() !== $needMigration) {
                    continue;
                }

                // Get all available resized image paths for all sizes
                $sizes = $this->getAllResizedImageSizes($entity, true);

                if ($count) {
                    $resultCount += count($sizes);

                } else {

                    // Get list of resized image paths non-transferred to S3 location
                    foreach ($sizes as $size) {
                        $result[] = array(
                            'item' => $entity,
                            'size' => $size,
                        );

                        if (isset($limit) && $limit <= count($result)) {
                            break;
                        }
                    }

                    if (isset($limit) && $limit <= count($result)) {
                        // Limit has been reached - break operation
                        break;
                    }
                }
            }
        }

        return $count ? $resultCount : $result;
    }

    /**
     * Get list of all resized image paths
     *
     * @param \XLite\Model\Base\Image $entity       Image entity
     * @param boolean                 $onlyExisting Get list of existing paths only
     *
     * @return array
     */
    public function getAllResizedImageSizes($entity, $onlyExisting = false)
    {
        $result = array();

        $name = basename($entity->getPath());

        $imageSizes = \XLite\Logic\ImageResize\Generator::getModelImageSizes(get_class($entity));

        foreach ($imageSizes as $size) {
            list($width, $height) = $size;
            $size = ($width ?: 'x') . '.' . ($height ?: 'x');
            $path = $entity->getLocalResizedPath($size, $name);
            if (!$onlyExisting || \Includes\Utils\FileManager::isExists($path)) {
                $result[] = $size;
            }
        }

        return $result;
    }

    /**
     * Find the same item as specified but already moved to S3
     *
     * @param \XLite\Model\Base\Image $entity   Image entity
     * @param boolean                 $isS3Type Flag: true - search S3 images; false - search non-S3 images OPTIONAL
     *
     * @return null|\XLite\Model\Base\Image
     */
    public function findSameByPath($entity, $isS3Type = true)
    {
        return $this->defineFindSameByPath($entity, $isS3Type)->getSingleResult();
    }

    /**
     * Define query for findSameByPath() method
     *
     * @param \XLite\Model\Base\Image $entity   Image entity
     * @param boolean                 $isS3Type Flag: true - search S3 images; false - search non-S3 images
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindSameByPath($entity, $isS3Type)
    {
        $sign = $isS3Type ? '=' : '!=';

        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.path = :path')
            ->andWhere('i.storageType ' . $sign . ' :storageType')
            ->andWhere('i.id != :id')
            ->setParameter('path', $entity->getPath())
            ->setParameter('storageType', \XLite\Model\Base\Image::STORAGE_S3)
            ->setParameter('id', $entity->getId());

        return $qb;
    }

    /**
     * Define query for findS3ResizedImages() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindS3ResizedImages()
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.s3icons != :empty')
            ->andWhere('i.s3icons != :emptySerial')
            ->andWhere('i.s3icons != :emptyArray')
            ->setParameter('empty', '')
            ->setParameter('emptySerial', serialize(null))
            ->setParameter('emptyArray', serialize(array()));

        return $qb;
    }

    /**
     * Define query for countS3Images() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountS3ImagesQuery()
    {
        $qb = $this->getImagesCountQueryBuilder();

        $alias = $this->getMainAlias($qb);

        return $qb->andWhere($alias . '.storageType = :type')
            ->setParameter('type', \XLite\Model\Base\Image::STORAGE_S3);
    }

    /**
     * Define query for countNoS3Images() method
     *
     * @param boolean $needMigration If true or false - needMigration flag will be considered
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountNoS3ImagesQuery($needMigration = null)
    {
        $qb = $this->getImagesCountQueryBuilder();

        $alias = $this->getMainAlias($qb);

        $qb->andWhere($alias . '.storageType != :type')
            ->setParameter('type', \XLite\Model\Base\Image::STORAGE_S3);

        if (!is_null($needMigration)) {
            $qb->andWhere($alias . '.needMigration = :needMigration')
                ->setParameter('needMigration', $needMigration);
        }

        return $qb;
    }

    /**
     * Get query builder to count images
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function getImagesCountQueryBuilder()
    {
        $qb = $this->createPureQueryBuilder();

        return $qb->select('COUNT(' . implode(', ', $this->getIdentifiersList($qb)) . ')')
            ->setMaxResults(1);
    }

    /**
     * Define query for findS3Images() method
     *
     * @param integer $limit Limit
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindS3ImagesQuery($limit)
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.storageType = :type')
            ->setParameter('type', \XLite\Model\Base\Image::STORAGE_S3);

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    /**
     * Define query for findNoS3Images() method
     *
     * @param integer $limit         Limit
     * @param boolean $needMigration If true or false - needMigration flag will be considered
     * @param array   $excluded
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindNoS3ImagesQuery($limit, $needMigration = null, $excluded = [])
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.storageType != :type')
            ->setParameter('type', \XLite\Model\Base\Image::STORAGE_S3);

        if (isset($needMigration)) {
            $qb->andWhere('i.needMigration = :needMigration')
                ->setParameter('needMigration', $needMigration);
        }

        if ($excluded) {
            $qb->andWhere($qb->expr()->notIn('i.id', $excluded));
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }
}
