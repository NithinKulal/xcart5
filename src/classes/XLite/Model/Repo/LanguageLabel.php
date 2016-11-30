<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Langauge labels repository
 */
class LanguageLabel extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_SUBSTRING  = 'substring';
    const SEARCH_CODES      = 'codes';
    const ORDER_FIRST_BY_IDS = 'orderFirstByIds';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('name'),
    );

    /**
     * Language codes where to search translation
     *
     * @var array
     */
    protected $searchCodes = array();

    // {{{ Cache routines

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();
        $list['all_by_code'] = array();

        return $list;
    }

    // }}}

    // {{{ Search labels by code

    /**
     * Find labels by language code
     *
     * @param string  $code  Language code OPTIONAL
     * @param boolean $count Flag: return count results if true
     *
     * @return array
     */
    public function findLabelsByCode($code = null, $count = false)
    {
        if (!isset($code)) {
            $code = \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        $data = $this->getFromCache('all_by_code', array('code' => $code));

        if (!isset($data)) {
            $queryBuilder = $this->createQueryBuilder(null, null, $code)
                ->select('l.name');

            if ($this->getTranslationCode() != \XLite::getDefaultLanguage()
                || \XLite\Core\Translation::DEFAULT_LANGUAGE !== \XLite::getDefaultLanguage()
            ) {
                $queryBuilder = $this->bindDefaultTranslation($queryBuilder);
            } else {
                $queryBuilder->addSelect('translations.label');
            }

            $labels = $queryBuilder->getArrayResult();

            $data = [];

            foreach ($labels as $label) {
                $data[$label['name']] = $label['label'];
            }

            ksort($data);

            $this->saveToCache($data, 'all_by_code', array('code' => $code));
        }

        return $count ? count($data) : $data;
    }

    /**
     * Bind default translation
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function bindDefaultTranslation($queryBuilder)
    {
        // Add additional join to translations with default language code
        $this->addDefaultTranslationJoins(
            $queryBuilder,
            $this->getMainAlias($queryBuilder),
            'defaults',
            \XLite::getDefaultLanguage()
        );

        $defaultsQuery = 'defaults.label';

        if (\XLite\Core\Translation::DEFAULT_LANGUAGE !== \XLite::getDefaultLanguage()) {
            // Add additional join to translations with default-default ('en' at the moment) language code
            $this->addDefaultTranslationJoins(
                $queryBuilder,
                $this->getMainAlias($queryBuilder),
                'defaultDefaults',
                'en'
            );
            $defaultsQuery = sprintf(
                '(CASE WHEN %s IS NOT NULL THEN %s ELSE %s END)',
                'defaults.label',
                'defaults.label',
                'defaultDefaults.label'
            );
        }

        $select = sprintf(
            '(CASE WHEN %s IS NOT NULL THEN %s ELSE %s END) as label',
            'translations.label',
            'translations.label',
            $defaultsQuery
        );

        $queryBuilder->addSelect($select);

        return $queryBuilder;
    }

    // }}}

    // {{{ Find labels translated to the specific language

    /**
     * Find labels by language code
     *
     * @param string  $code  Language code OPTIONAL
     * @param boolean $count Flag: return count results if true
     *
     * @return array
     */
    public function findLabelsTranslatedToCode($code)
    {
        $result = array();

        $qb = $this->createQueryBuilder();
        $data = $this->defineLabelsTranslatedToCodeQuery($qb, $code)->getResult();

        if ($data) {
            foreach ($data as $row) {
                $result[$row->getName()] = $row->getTranslation($code)->label;
            }
        }

        return $result;
    }

    /**
     * Define query for 'countByCode()' method
     *
     * @param \Doctrine\ORM\QueryBuilder $qb   Query builder
     * @param string                     $code Code
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineLabelsTranslatedToCodeQuery($qb, $code)
    {
        return $qb->innerJoin('l.translations', 'lt')
            ->andWhere('lt.code = :code')
            ->setParameter('code', $code);
    }

    // }}}

    // {{{ countByName

    /**
     * Count labels by name
     *
     * @param string $name Name
     *
     * @return integer
     */
    public function countByName($name)
    {
        return $this->defineCountByNameQuery($name)->count();
    }

    /**
     * Define query for 'countByName()' method
     *
     * @param string $name Name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCountByNameQuery($name)
    {
        return $this->defineCountQuery()
            ->andWhere('l.name LIKE :name')
            ->setParameter('name', '%' . $name . '%');
    }

    // }}}

    // {{{ countByCode

    /**
     * Count labels by code
     *
     * @param string $code Code
     *
     * @return integer
     */
    public function countByCode($code)
    {
        return $this->defineCountByCodeQuery($code)->count();
    }

    /**
     * Define query for 'countByCode()' method
     *
     * @param string $code Code
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCountByCodeQuery($code)
    {
        return $this->defineCountQuery()
            ->innerJoin('l.translations', 'lt')
            ->andWhere('lt.code = :code')
            ->setParameter('code', $code);
    }

    // }}}

    // {{{ findLikeName

    /**
     * Find lables by name pattern with data frame
     *
     * @param string  $name  Name pattern
     * @param integer $start Start offset OPTIONAL
     * @param integer $limit Frame length OPTIONAL
     *
     * @return array
     */
    public function findLikeName($name, $start = 0, $limit = 0)
    {
        return $this->defineLikeNameQuery($name, $start, $limit)->getResult();
    }

    /**
     * Define query for 'findLikeName()' method
     *
     * @param string  $name  Name
     * @param integer $start Start offset
     * @param integer $limit Frame length
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineLikeNameQuery($name, $start, $limit)
    {
        return $this->createPureQueryBuilder()
            ->bindAndCondition('l.name', $name, 'LIKE')
            ->setFrameResults($start, $limit);
    }

    // }}}

    /**
     * Convert entity to parameters list for 'all_by_code' cache cell
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function convertRecordToParamsAllByCode(\XLite\Model\AEntity $entity)
    {
        return array('*');
    }

    // {{{

    /**
     * Prepare conditions for search
     *
     * @return void
     */
    protected function processConditions()
    {
        $cnd = $this->searchState['currentSearchCnd'];

        if (!empty($cnd->{static::SEARCH_CODES})) {
            $this->searchCodes = $cnd->{static::SEARCH_CODES};
        }

        parent::processConditions();
    }

    /**
     * Excluded search conditions
     *
     * @return array
     */
    protected function getExcludedConditions()
    {
        return array_merge(
            parent::getExcludedConditions(),
            array(
                static::ORDER_FIRST_BY_IDS => array(
                    static::SEARCH_MODE_COUNT
                ),
                static::SEARCH_CODES => static::EXCLUDE_FROM_ANY,
            )
        );
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Profile
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {

            $queryBuilder->leftJoin('l.translations', 'lt');

            $or = new \Doctrine\ORM\Query\Expr\Orx();

            // Use non-standard Doctrine function CAST(expr AS CHAR)
            $or->add('CastChar(l.name) LIKE :substring');

            $and = new \Doctrine\ORM\Query\Expr\Andx();
            $and->add('lt.label LIKE :substring');
            $and->add($queryBuilder->expr()->in('lt.code', $this->searchCodes));

            $or->add($and);

            $queryBuilder->andWhere($or)
                ->setParameter('substring', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Profile
     *
     * @return void
     */
    protected function prepareCndOrderFirstByIds(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        // Use non-standard Doctrine function FIN_IN_SET(needle, haystack)
        $queryBuilder->addSelect('FindInSet(l.label_id, :label_ids) new_index')
            ->setParameter('label_ids', implode(',', $value));

        $queryBuilder->addOrderBy('new_index', 'DESC');
    }

    // }}}

    // {{{ Delete translations

    /**
     * Delete all translations of labels to the specific language
     *
     * @param string $code Language code
     *
     * @return boolean
     */
    public function deleteTranslations($code)
    {
        $qb = $this->createQueryBuilder();
        $data = $this->defineLabelsTranslatedToCodeQuery($qb, $code)->getResult();

        if ($data) {

            $toDelete = array();

            foreach ($data as $row) {
                $toDelete[] = $row->getTranslation($code);
            }

            if (!empty($toDelete)) {
                \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->deleteInBatch($toDelete);
            }
        }

        return true;
    }
}
