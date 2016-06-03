<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Base;

/**
 * Translations-owner abstract reporitory
 */
abstract class I18n extends \XLite\Model\Repo\ARepo
{
    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from.
     * @param string $code    Language code OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        return $this->addLanguageQuery(parent::createQueryBuilder($alias, $indexBy), $alias, $code);
    }

    /**
     * Get translation repository
     *
     * @return \XLite\Model\repo\ARepo
     */
    public function getTranslationRepository()
    {
        return \XLite\Core\Database::getRepo($this->_entityName . 'Translation');
    }

    /**
     * Add language subquery with language code relation
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder      Query builder
     * @param string                     $alias             Main model alias OPTIONAL
     * @param string                     $code              Language code OPTIONAL
     * @param string                     $translationsAlias Translation alias OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addLanguageQuery(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null, $code = null, $translationsAlias = 'translations')
    {
        if (!isset($alias)) {
            $alias = $this->getMainAlias($queryBuilder);
        }

        if (!isset($code)
            && \XLite\Logic\Import\Importer::getLanguageCode()
        ) {
            $code = \XLite\Logic\Import\Importer::getLanguageCode();
        }

        if (!isset($code)) {
            $code = $this->getTranslationCode();
        }

        return $this->addTranslationJoins($queryBuilder, $alias, $translationsAlias, $code);
    }

    /**
     * Add the specific joints with the translation table
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string                     $alias
     * @param string                     $translationsAlias
     * @param string                     $code
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addTranslationJoins($queryBuilder, $alias, $translationsAlias, $code)
    {
        if ($code) {
            $queryBuilder
                ->leftJoin(
                    $alias . '.translations',
                    $translationsAlias,
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    $translationsAlias . '.code = :lng'
                )
                ->setParameter('lng', $code);
        }

        return $queryBuilder;
    }

    /**
     * Add the join with the default translation table
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string                     $alias
     * @param string                     $translationsAlias
     * @param string                     $code
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addDefaultTranslationJoins($queryBuilder, $alias, $translationsAlias, $code)
    {
        if ($code) {
            $queryBuilder
                ->leftJoin(
                    $alias . '.translations',
                    $translationsAlias,
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    $translationsAlias . '.code = :defaultLng' . $code
                )
                ->setParameter('defaultLng' . $code, $code);
        }

        return $queryBuilder;
    }

    /**
     * Return current translation code
     *
     * @return string
     */
    protected function getTranslationCode()
    {
        $code = 'en';

        if (\XLite\Logic\Import\Importer::getLanguageCode()) {
            $code = \XLite\Logic\Import\Importer::getLanguageCode();

        } elseif (!\XLite::isCacheBuilding()
            && \XLite\Core\Session::getInstance()->getLanguage()
        ) {
            $code = \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        return $code;
    }
}
