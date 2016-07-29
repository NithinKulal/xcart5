<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * ImageSettings repository
 */
class ImageSettings extends \XLite\Model\Repo\ARepo
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('code', 'model', 'moduleName'),
    );

    /**
     * Load raw fixture
     *
     * @param \XLite\Model\AEntity    $entity  ImageSettings entity
     * @param array                   $record  Record
     * @param array                   $regular Regular fields info OPTIONAL
     * @param array                   $assocs  Associations info OPTIONAL
     *
     * @return void
     */
    public function loadFixture(
        array $record,
        array $regular = array(),
        array $assocs = array(),
        \XLite\Model\AEntity $parent = null,
        array $parentAssoc = array()
    ) {
        $moduleName = \XLite\Core\Database::getInstance()->getFixturesLoadingOption('moduleName')
            ?: 'default';

        if (!isset($record['moduleName'])) {
            $record['moduleName'] = $moduleName;
        }

        return parent::loadFixture($record, $regular, $assocs, $parent, $parentAssoc);
    }

    /**
     * Find by module name
     *
     * @param  string $moduleName Module name
     *
     * @return array
     */
    public function findByModuleName($moduleName)
    {
        $queryBuilder = $this->createQueryBuilder()
            ->andWhere('i.moduleName = :moduleName')
            ->setParameter('moduleName', $moduleName);

        if ($moduleName === 'default') {
            $queryBuilder
                ->orWhere('i.moduleName = :empty')
                ->setParameter('empty', '');
        }

        return $queryBuilder->getResult();
    }
}
