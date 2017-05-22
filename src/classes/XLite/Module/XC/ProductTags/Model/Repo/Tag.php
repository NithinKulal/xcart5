<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Model\Repo;

/**
 * Tag repository
 */
class Tag extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_NAME = 'name';

    /**
     * Repository type
     *
     * @var string
     */
    // protected $type = self::TYPE_SECONDARY;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * @var \XLite\Module\XC\ProductTags\Model\Tag[]
     */
    protected $insertedCache = [];

    // {{{ Search

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

    // }}}

    // {{{ findAllTags

    /**
     * Find all tags
     *
     * @param boolean $countOnly Count only OPTIONAL
     *
     * @return array
     */
    public function findAllTags($countOnly = false)
    {
        return !$countOnly
            ? $this->findAll()
            : $this->count();
    }

    // }}}

    // {{{ findOneByName

    /**
     * Find tag by name (any language)
     *
     * @param string $name Name
     *
     * @return \XLite\Module\XC\ProductTags\Model\Tag|null
     */
    public function findOneByName($name)
    {
        return $this->defineOneByNameQuery($name)->getSingleResult();
    }

    /**
     * Define query builder for findOneByName() method
     *
     * @param string $name Name
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineOneByNameQuery($name)
    {
        return $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name);
    }

    // }}}

    // {{{ Search tags by category

    /**
     * Find tags by category
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return \XLite\Module\XC\ProductTags\Model\Tag[]
     */
    public function findCountByCategory($category)
    {
        return $this->defineByCategoryQuery($category)
            ->select('COUNT(DISTINCT t.id)')
            ->getSingleScalarResult();
    }

    /**
     * Find tags by category
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return \XLite\Module\XC\ProductTags\Model\Tag[]
     */
    public function findByCategory($category)
    {
        return $this->defineByCategoryQuery($category)->getOnlyEntities();
    }

    /**
     * Define query builder for findByCategory() method
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineByCategoryQuery($category)
    {
        return $this->createQueryBuilder()
            ->linkLeft('t.products')
            ->linkLeft('products.categoryProducts')
            ->andWhere('categoryProducts.category = :category')
            ->setParameter('category', $category);
    }

    // }}}

    // {{{

    public function createTagByName($tag)
    {
        $tagObject = isset($this->insertedCache[$tag])
            ? $this->insertedCache[$tag]
            : \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag')->findOneByName($tag);

        if (!$tagObject) {
            $tagObject = new \XLite\Module\XC\ProductTags\Model\Tag();
            $tagObject->setName($tag);
            \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag')->insert($tagObject, false);
            $this->insertedCache[$tag] = $tagObject;
        }

        return $tagObject;
    }

    public function getListByIdOrName($tags)
    {
        $ids = array_filter($tags, function ($item) {
            return is_numeric($item);
        });

        $result = $this->findByIds($ids);

        foreach ($tags as $tag) {
            if (is_numeric($tag)) {
                continue;
            }

            $result[] = $this->createTagByName($tag);
        }

        return $result;
    }

    // }}}
}
