<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\Model\Repo;

/**
 * News messages repository
 */
class NewsMessage extends \XLite\Model\Repo\Base\I18n
{

    /**
     * Alternative record identifiers
     *
     * @var   array
     */
    protected $alternativeIdentifier = array(
        array('cleanURL'),
    );

    /**
     * Find product by clean URL
     *
     * @param string $url Clean URL
     *
     * @return \XLite\Module\XC\News\Model\NewsMessage
     */
    public function findOneByCleanURL($url)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.cleanURL = :url')
            ->setParameter('url', $url)
            ->setMaxResults(1)
            ->getSingleResult();
    }

    // {{{ Search

    const SEARCH_NAME    = 'name';
        const SEARCH_ENABLED =  'enabled';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('translations.name = :name')
                ->setParameter('name', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('n.enabled = :true AND n.date < :time')
                ->setParameter('true', true)
                ->setParameter('time', \XLite\Core\Converter::time());
        }
    }

    /**
    * Prepare next and previous news query
    *
    * @param \XLite\Module\XC\News\Model\NewsMessage $model Model to prepare
    *
    * @return \Doctrine\ORM\QueryBuilder
    */
    protected function defineSiblingsByNews($model)
    {
        return $this->createQueryBuilder()
            ->andWhere('n.id != :current')
            ->setParameter('date', $model->getDate())
            ->setParameter('current', $model->getId())
            ->setMaxResults(1);
    }

    /**
    * Prepare next and previous news
    *
    * @param \XLite\Module\XC\News\Model\NewsMessage $model Model to prepare
    *
    * @return array
    */
    public function findSiblingsByNews(\XLite\Module\XC\News\Model\NewsMessage $model)
    {
        $or = new \Doctrine\ORM\Query\Expr\Orx();
        $or->add('n.date < :date');
        $or->add('n.id < :current AND n.date = :date');
        $previous = $this->defineSiblingsByNews($model)
            ->orderBy('n.id', 'desc')
            ->addOrderBy('n.date', 'asc')
            ->andWhere($or)
            ->getSingleResult();

        $or = new \Doctrine\ORM\Query\Expr\Orx();
        $or->add('n.date > :date');
        $or->add('n.id > :current AND n.date = :date');

        $next = $this->defineSiblingsByNews($model)
            ->orderBy('n.id', 'asc')
            ->addOrderBy('n.date', 'desc')
            ->andWhere($or)
            ->getSingleResult();

        return array(
            $previous,
            $next
        );
    }

}
