<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * The "product" model repository
 */
class Product extends \XLite\Model\Repo\Base\I18n implements \XLite\Base\IREST
{
    /**
     * Allowable search params
     */
    const P_SKU               = 'SKU';
    const P_CATEGORY_ID       = 'categoryId';
    const P_SUBSTRING         = 'substring';
    const P_PRICE             = 'price';
    const P_SEARCH_IN_SUBCATS = 'searchInSubcats';
    const P_INVENTORY         = 'inventory';
    
    const P_INCLUDING         = 'including';
    const P_PRODUCT_CLASS     = 'productClass';
    const P_TAX_CLASS         = 'taxClass';
    const P_EXCL_PRODUCT_ID   = 'excludingProductId';
    const P_ENABLED           = 'enabled';
    const P_ARRIVAL_DATE      = 'arrivalDate';
    const P_ARRIVAL_DATE_RANGE = 'arrivalDateRange';

    const P_BY_TITLE          = 'byTitle';
    const P_BY_DESCR          = 'byDescr';
    const P_BY_SKU            = 'bySKU';

    const INCLUDING_ALL     = 'all';
    const INCLUDING_ANY     = 'any';
    const INCLUDING_PHRASE  = 'phrase';

    const INV_ALL = 'all'; // All
    const INV_LOW = 'low'; // Low amount
    const INV_OUT = 'out'; // Out of stock
    const INV_IN  = 'in';  // In stock

    const TITLE_FIELD       = 'translations.name';
    const BRIEF_DESCR_FIELD = 'translations.briefDescription';
    const DESCR_FIELD       = 'translations.description';
    const SKU_FIELD         = 'p.sku';

    const SKU_GENERATION_LIMIT = 50;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('sku'),
    );

    /**
     * Prepare conditions for search
     *
     * @return void
     */
    protected function processConditions()
    {
        $cnd = $this->searchState['currentSearchCnd'];

        if (!\XLite::isAdminZone()
            && !isset($cnd->{static::P_INVENTORY})
            && 'everywhere' !== \XLite\Core\Config::getInstance()->General->show_out_of_stock_products
        ) {
            $cnd->{static::P_INVENTORY} = static::INV_IN;
        }

        parent::processConditions();
    }

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from. OPTIONAL
     * @param string $code    Language code OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy, $code);

        $alias = $alias ?: $queryBuilder->getRootAlias();
        $this->addEnabledCondition($queryBuilder, $alias);
        $this->addDateCondition($queryBuilder, $alias);
        $this->addMembershipCondition($queryBuilder, $alias);

        return $queryBuilder;
    }

    /**
     * Find product by clean URL
     * TODO - to revise
     *
     * @param string $url Clean URL
     *
     * @return \XLite\Model\Product
     */
    public function findOneByCleanURL($url)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.cleanURL = :url')
            ->setParameter('url', $url)
            ->setMaxResults(1)
            ->getSingleResult();
    }

    /**
     * Find unprocessed chunk
     *
     * @param integer $length Chunk length
     *
     * @return array
     */
    public function findUnprocessedChunk($length)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.needProcess = :needProcess')
            ->setParameter('needProcess', true)
            ->setMaxResults($length)
            ->getResult();
    }

    /**
     * Count unprocessed
     *
     * @return integer
     */
    public function countUnprocessed()
    {
        return $this->createPureQueryBuilder('p')
            ->andWhere('p.needProcess = :needProcess')
            ->setParameter('needProcess', true)
            ->count();
    }

    /**
     * Count last updated products
     *
     * @param integer $limit Time limit
     *
     * @return integer
     */
    public function countLastUpdated($limit)
    {
        return (int) $this->defineCountLastUpdatedQuery($limit)->getSingleScalarResult();
    }

    /**
     * Get REST entity names
     *
     * @return array
     */
    public function getRESTNames()
    {
        return array (
            'product',
        );
    }

    /**
     * Get product data as REST
     *
     * @param integer $id Product id
     *
     * @return array|null
     */
    public function getProductREST($id)
    {
        $product = $this->find($id);

        $data = null;

        if ($product) {
            foreach ($this->_class->fieldNames as $name) {
                $mname = 'get' . \XLite\Core\Converter::convertToCamelCase($name);
                // $maname assebmled from 'get' + \XLite\Core\Converter::convertToCamelCase() method
                $data[$name] = $product->$mname();
            }

            $data['name'] = $product->getName();
            $data['description'] = $product->getDescription();
        }

        return $data;
    }

    /**
     * Get import iterator
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getImportIterator()
    {
        return $this->defineImportQuery()->iterate();
    }

    /**
     * Assign enabled condition for extenral query builders
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb    External query builder
     * @param string                                  $alias Product repository alias OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function assignExternalEnabledCondition(\XLite\Model\QueryBuilder\AQueryBuilder $qb, $alias = 'p')
    {

        if (!\XLite::isAdminZone()
            && 'everywhere' !== \XLite\Core\Config::getInstance()->General->show_out_of_stock_products
        ) {
            $this->prepareCndInventory($qb, static::INV_IN);
        }

        $this->addEnabledCondition($qb, $alias);
        $this->addDateCondition($qb, $alias);
        $this->addMembershipCondition($qb, $alias);

        return $qb;
    }

    /**
     * Generate SKU
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return string
     */
    public function generateSKU(\XLite\Model\Product $product)
    {
        $id = $product->getProductId();
        if (11 > strlen((string)$id)) {
            $id = '1' . str_repeat('0', 10 - strlen((string)$id)) . $id;
        }
        $sku = (string)$id;
        $i = 0;

        $qb = $this->defineGenerateSKUQuery();

        while ($i < static::SKU_GENERATION_LIMIT
            && 0 < (int) $qb->setParameter('sku', $sku)->getSingleScalarResult()
        ) {
            $i++;
            $sku = $id . '-' . $i;
        }

        if ($i >= static::SKU_GENERATION_LIMIT) {
            $sku = md5($sku . microtime(true));
        }

        return $sku;
    }

    /**
     * Generate SKU
     *
     * @param string $sku SKU
     *
     * @return string
     */
    public function assembleUniqueSKU($sku)
    {
        $i = 0;
        $qb = $this->defineGenerateSKUQuery();
        $base = $sku;

        while ($i < static::SKU_GENERATION_LIMIT
            && 0 < (int) $qb->setParameter('sku', $sku)->getSingleScalarResult()
        ) {
            $i++;
            $newSku = substr($base . '-clone-' . $i, 0, 32);

            if ($newSku == $sku) {
                $newSku = md5($newSku);
            }
            $sku = $newSku;
        }

        if ($i >= static::SKU_GENERATION_LIMIT) {
            $sku = md5($sku . microtime(true));
        }

        return $sku;
    }

    /**
     * Define query for generateSKU() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function defineGenerateSKUQuery()
    {
        return $this->getQueryBuilder()
            ->from($this->_entityName, 'p')
            ->select('COUNT(p.product_id) cnt')
            ->andWhere('p.sku = :sku');
    }

    /**
     * Returns the "low inventory products" amount
     *
     * @return integer
     */
    public function getLowInventoryProductsAmount()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->search($this->defineLowInventoryProductsAmountCnd(), true);
    }

    /**
     * Define the specific "low inventory products amount" search condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function defineLowInventoryProductsAmountCnd()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Product::P_INVENTORY} = \XLite\Model\Repo\Product::INV_LOW;

        return $cnd;
    }

    /**
     * Define import querty
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineImportQuery()
    {
        return $this->createPureQueryBuilder('p');
    }

    /**
     * Define query for countLastUpdated()
     *
     * @param integer $limit Time limit
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountLastUpdatedQuery($limit)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.product_id) cnt')
            ->andWhere('p.updateDate >= :time')
            ->setParameter('time', $limit)
            ->setMaxResults(1);
    }

    /**
     * List of fields to use in search by substring TODO !REFACTOR!
     *
     * @return array
     */
    protected function getSubstringSearchFields()
    {
        $conditionsBy = $this->getConditionBy();
        $allEmpty = true;

        foreach ($conditionsBy as $conditionBy) {
            if ('Y' === $this->searchState['currentSearchCnd']->{$conditionBy}) {
                $allEmpty = false;
            }
        }

        // if ALL parameters is FALSE then we search by ALL parameters
        if ($allEmpty) {
            foreach ($conditionsBy as $conditionBy) {
                $this->searchState['currentSearchCnd']->{$conditionBy} = 'Y';
            }
        }

        $result = array();

        foreach ($conditionsBy as $conditionBy) {
            $conditionFields = ('Y' === $this->searchState['currentSearchCnd']->{$conditionBy})
                ? $this->{'getSubstringSearchFields' . ucfirst($conditionBy)}()
                : array();

            $result = array_merge($result, $conditionFields);
        }

        return $result;
    }

    /**
     * Excluded search conditions
     *
     * @return array
     */
    protected function getExcludedConditions()
    {
        $excludedConditions = array_merge(
            $this->getConditionBy(),
            array(
                static::P_SEARCH_IN_SUBCATS,
                static::P_INCLUDING
            )
        );

        return array_merge(
            parent::getExcludedConditions(),
            array_fill_keys(
                $excludedConditions,
                static::EXCLUDE_FROM_ANY
            )
        );
    }

    /**
     * Return conditions parameters that are responsible for substring set of fields.
     *
     * @return array
     */
    protected function getConditionBy()
    {
        return array(
            self::P_BY_TITLE,
            self::P_BY_DESCR,
            self::P_BY_SKU,
        );
    }

    /**
     * Return fields set for title search
     *
     * @return array
     */
    protected function getSubstringSearchFieldsByTitle()
    {
        return array(
            self::TITLE_FIELD,
        );
    }

    /**
     * Return fields set for description search
     *
     * @return array
     */
    protected function getSubstringSearchFieldsByDescr()
    {
        return array(
            self::BRIEF_DESCR_FIELD,
            self::DESCR_FIELD,
        );
    }

    /**
     * Return fields set for SKU search
     *
     * @return array
     */
    protected function getSubstringSearchFieldsBySKU()
    {
        return array(
            self::SKU_FIELD,
        );
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSKU(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $value = trim($value);
        if (0 < strlen($value)) {
            $queryBuilder->andWhere('p.sku LIKE :sku')
                ->setParameter('sku', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('p.enabled = :enabled')
            ->setParameter('enabled', (int) (bool) $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCategoryId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_object($value) && $value instanceof \XLite\Model\Category) {
            $value = $value->getCategoryId();
        }

        $queryBuilder->linkLeft('p.categoryProducts', 'cp')
            ->linkLeft('cp.category', 'c');

        if ($value === 'no_category') {
            $queryBuilder->andWhere('p.categoryProducts is empty');

        } elseif (empty($this->searchState['currentSearchCnd']->{self::P_SEARCH_IN_SUBCATS})) {
            $queryBuilder->andWhere('c.category_id = :categoryId')
                ->setParameter('categoryId', $value);

        } elseif (!\XLite\Core\Database::getRepo('XLite\Model\Category')->addSubTreeCondition($queryBuilder, $value)) {
            // TODO - add throw exception
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $including = $this->searchState['currentSearchCnd']->{self::P_INCLUDING};
            $including = in_array($including, $this->getAllowedIncludingValues(), true)
                ? $including
                : self::INCLUDING_PHRASE;

            $this->{'processCndSubstring' . ucfirst($including)} ($queryBuilder, $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndPrice(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value)) {
            $min = empty($value[0]) ? null : trim($value[0]);
            $min = (0 === strlen($min) || !is_numeric($min)) ? null : (float) $min;

            $max = empty($value[1]) ? null : trim($value[1]);
            $max = (0 === strlen($max) || !is_numeric($max)) ? null : (float) $max;

            $this->assignPriceRangeCondition($queryBuilder, $min, $max);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProductClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && is_object($value)) {
            $queryBuilder->andWhere('p.productClass = :productClass')
                ->setParameter('productClass', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndTaxClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && is_object($value)) {
            $queryBuilder->andWhere('p.taxClass = :taxClass')
                ->setParameter('taxClass', $value);
        }
    }

    /**
     * Assign prica range-based search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param float                      $min          Minimum
     * @param float                      $max          Maximum
     *
     * @return void
     */
    protected function assignPriceRangeCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $min, $max)
    {
        if (null !== $min || null !== $max) {
            $field = \XLite::isAdminZone()
                ? 'p.price'
                : $this->getCalculatedField($queryBuilder, 'price');

            if (null !== $min) {
                $queryBuilder->andWhere($field . ' >= :minPrice')
                    ->setParameter('minPrice', (float) $min);
            }

            if (null !== $max) {
                $queryBuilder->andWhere($field . ' <= :maxPrice')
                    ->setParameter('maxPrice', (float) $max);
            }
        }
    }

    /**
     * Returns array of allowed values for 'includes' input variable
     *
     * @return boolean
     */
    protected function getAllowedIncludingValues()
    {
        return array(self::INCLUDING_ALL, self::INCLUDING_ANY, self::INCLUDING_PHRASE);
    }

    /**
     * Prepare certain search condition (EXACT PHRASE method)
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     */
    protected function processCndSubstringPhrase(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $value  = trim($value);
        $cnd = new \Doctrine\ORM\Query\Expr\Orx();

        // EXACT PHRASE method (or if NONE is selected)
        foreach ($this->getSubstringSearchFields() as $field) {
            $cnd->add($field . ' LIKE :substring');
        }

        $queryBuilder->setParameter('substring', '%' . $value . '%');

        $queryBuilder->andWhere($cnd);
    }

    /**
     * Prepare certain search condition (ALL WORDS method)
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     */
    protected function processCndSubstringAll(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (count($this->getSubstringSearchFields()) > 1) {
            $concatField = 'concat_ws(\' \', ' . implode(', ', $this->getSubstringSearchFields()) . ')';
        } elseif (count($this->getSubstringSearchFields()) == 1) {
            $concatField = array_values($this->getSubstringSearchFields())[0];
        } else {
            return;
        }

        $cnd = $queryBuilder->expr()->andX();

        foreach ($this->getSearchWords($value) as $index => $word) {
            $cnd->add($queryBuilder->expr()->like($concatField, ':word' . $index));

            $queryBuilder->setParameter('word' . $index, '%' . $word . '%');
        }

        $queryBuilder->andWhere($cnd); // TODO check https://github.com/doctrine/doctrine2/issues/4164 and use "having"
    }

    /**
     * Prepare certain search condition for substring (ANY WORDS method)
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     */
    protected function processCndSubstringAny(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $searchWords = $this->getSearchWords($value);
        $cnd = new \Doctrine\ORM\Query\Expr\Orx();

        foreach ($this->getSubstringSearchFields() as $field) {
            foreach ($searchWords as $index => $word) {
                // Collect OR expressions
                $cnd->add($field . ' LIKE :word' . $index);

                $queryBuilder->setParameter('word' . $index, '%' . $word . '%');
            }
        }

        $queryBuilder->andWhere($cnd);
    }

    /**
     * Return search words for "All" and "Any" INCLUDING parameter
     *
     * @param string $value Search string
     *
     * @return array
     */
    protected function getSearchWords($value)
    {
        $value  = trim($value);
        $result = array();

        if (preg_match_all('/"([^"]+)"/', $value, $match)) {
            $result = $match[1];
            $value  = str_replace($match[0], '', $value);

        }

        return array_merge((array)$result, array_map('trim', explode(' ', $value)));
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndInventory(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = self::INV_ALL)
    {
        if (in_array($value, array(self::INV_LOW, self::INV_OUT), true)) {
            $queryBuilder->andWhere('p.inventoryEnabled = :enabled')
                ->setParameter('enabled', true);
        }

        if ($value === self::INV_LOW) {
            $this->prepareCndInventoryLow($queryBuilder);

        } elseif ($value === self::INV_OUT) {
            $this->prepareCndInventoryOut($queryBuilder);

        } elseif ($value === self::INV_IN) {
            $this->prepareCndInventoryIn($queryBuilder);
        }
    }

    /**
     * Add inventory condition to search products with qty less than lowLimitAmount value
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     *
     * @return void
     */
    protected function prepareCndInventoryLow(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $queryBuilder->andWhere('p.lowLimitEnabled = :lowLimitEnabled')
            ->setParameter('lowLimitEnabled', true)
            ->andWhere('p.amount <= p.lowLimitAmount');
    }

    /**
     * Add inventory condition to search out-of-stock products
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     *
     * @return void
     */
    protected function prepareCndInventoryOut(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $queryBuilder->andWhere('p.amount <= :zero')
            ->setParameter('zero', 0);
    }

    /**
     * Add inventory condition to search in-stock products
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     *
     * @return void
     */
    protected function prepareCndInventoryIn(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $orCnd = new \Doctrine\ORM\Query\Expr\Orx();
        $orCnd->add('p.inventoryEnabled = :disabled');
        $orCnd->add('p.amount > :zero');

        $queryBuilder->andWhere($orCnd)
            ->setParameter('disabled', false)
            ->setParameter('zero', 0);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndExcludingProductId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value) && 1 < count($value)) {
            $queryBuilder->andWhere('p.product_id NOT IN (' . implode(',', $value) . ')');

        } elseif ($value) {
            $queryBuilder->andWhere('p.product_id != :productId')
                ->setParameter('productId', is_array($value) ? array_pop($value) : $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndArrivalDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value)) {
            $value = array_values($value);
            $start = empty($value[0]) ? null : (int) $value[0];
            $end = empty($value[1]) ? null : (int) $value[1];

            if ($start) {
                $queryBuilder->andWhere('p.arrivalDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('p.arrivalDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndArrivalDateRange(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            list($start, $end) = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);
            if ($start) {
                $queryBuilder->andWhere('p.arrivalDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('p.arrivalDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!$this->isCountSearchMode()) {
            list($sort, $order) = $this->getSortOrderValue($value);

            // FIXME - add aliases for sort modes
            if ('p.price' === $sort && !\XLite::isAdminZone()) {
                $sort = $this->getCalculatedFieldAlias($queryBuilder, 'price');
            } elseif ('translations.name' === $sort) {
                $this->addSortByTranslation($queryBuilder, $sort, $order);
                $sort = 'calculatedName';
            }

            $queryBuilder->addOrderBy($sort, $order);
        }
    }

    /**
     * Add 'sort by name' builder structures
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $sort         Sort field
     * @param string                     $order        Sort direction
     *
     * @return void
     */
    protected function addSortByTranslation(\Doctrine\ORM\QueryBuilder $queryBuilder, $sort, $order)
    {
        // Main alias
        $alias = $this->getMainAlias($queryBuilder);

        // The language code of current user session
        $currentCode = $this->getTranslationCode();

        // Default store language code
        $defaultCode = \XLite::getDefaultLanguage();

        // Add additional join to translations with current language code
        parent::addTranslationJoins($queryBuilder, $alias, 'st', $currentCode);

        if ($currentCode !== $defaultCode) {
            // Add additional join to translations with default language code
            $this->addTranslationJoins($queryBuilder, $alias, 'st2', $defaultCode);

            // Add calculated field to the fields list to use this for sorting out
            $queryBuilder->addSelect('IFNULL(st.name,IFNULL(st2.name,translations.name)) calculatedName');

        } else {
            // Add calculated field to the fields list to use this for sorting out
            $queryBuilder->addSelect('IFNULL(st.name,translations.name) calculatedName');
        }
    }

    /**
     * Adds additional condition to the query for checking if product is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function addEnabledCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if (!\XLite::isAdminZone()) {
            $this->assignEnabledCondition($queryBuilder, $alias);
        }

        return $queryBuilder;
    }

    /**
     * Assign enabled condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param string                     $alias        Alias OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function assignEnabledCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        $alias = $alias ?: $queryBuilder->getRootAlias();
        $queryBuilder->andWhere($alias . '.enabled = :enabled')
            ->setParameter('enabled', true);

        return $queryBuilder;
    }

    /**
     * Define calculated price definition DQL
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $alias        Main alias
     *
     * @return string
     */
    protected function defineCalculatedPriceDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $alias)
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        if ($profile
            && $profile->getMembership()
        ) {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qd',
                'WITH',
                'qd.membership = :qdMembership'
            )->setParameter('qdMembership', $profile->getMembership());

        } else {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qd',
                'WITH',
                'qd.membership is null'
            );
        }

        return 'qd.price';
    }

    /**
     * Define calculated name definition DQL
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $alias        Main alias
     *
     * @return string
     */
    protected function defineCalculatedNameDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $alias)
    {
        return 'translations.name';
    }

    /**
     * Define calculated sku definition DQL
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $alias        Main alias
     *
     * @return string
     */
    protected function defineCalculatedSkuDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $alias)
    {
        return $alias . '.sku';
    }

    /**
     * Adds additional condition to the query for checking if product is up-to-date
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addDateCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if (!\XLite::isAdminZone()) {
            $alias = $alias ?: $queryBuilder->getRootAlias();
            $queryBuilder->andWhere($alias . '.arrivalDate < :now')
                ->setParameter('now', \XLite\Core\Converter::getDayEnd(\XLite\Base\SuperClass::getUserTime()));
        }
    }

    /**
     * Adds additional condition to the query for checking if product has available membership
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addMembershipCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if (!\XLite::isAdminZone()) {
            $alias = $alias ?: $queryBuilder->getRootAlias();
            $membership = \XLite\Core\Auth::getInstance()->getMembershipId();

            $queryBuilder->linkLeft($alias . '.memberships', 'membership');

            if ($membership) {
                $queryBuilder->andWhere('membership.membership_id = :membershipId OR membership.membership_id IS NULL')
                    ->setParameter('membershipId', \XLite\Core\Auth::getInstance()->getMembershipId());

            } else {
                $queryBuilder->andWhere('membership.membership_id IS NULL');
            }
        }
    }

    // {{{ Export routines

    // }}}

    /**
     * Insert single entity
     *
     * @param \XLite\Model\AEntity|array $entity Data to insert OPTIONAL
     *
     * @return \XLite\Model\AEntity|array
     */
    protected function performInsert($entity = null)
    {
        $entity = parent::performInsert($entity);

        if ($entity && !$this->getBlockQuickDataFlag()) {
            $entity->updateQuickData();
        }

        return $entity;
    }

    /**
     * Update single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to use
     * @param array                $data   Data to save OPTIONAL
     *
     * @return void
     */
    protected function performUpdate(\XLite\Model\AEntity $entity, array $data = array())
    {
        parent::performUpdate($entity, $data);

        if (!$this->getBlockQuickDataFlag()) {
            $entity->updateQuickData();
        }
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
        $queryBuilder->leftJoin(
            $alias . '.translations',
            $translationsAlias
        );

        return $queryBuilder;
    }

    // {{{ Quick data

    /**
     * Block quick data
     *
     * @var boolean
     */
    protected $blockQuickData = false;

    /**
     * Set block quick data flag
     *
     * @param boolean $flag Flag
     *
     * @return \XLite\Model\Repo\ARepo
     */
    public function setBlockQuickDataFlag($flag)
    {
        $this->blockQuickData = $flag;

        return $this;
    }

    /**
     * Get block quick data flag
     *
     * @return boolean
     */
    public function getBlockQuickDataFlag()
    {
        return $this->blockQuickData;
    }

    /**
     * Count items for quick data
     *
     * @return integer
     */
    public function countForQuickData()
    {
        return (int) $this->defineCountForQuickDataQuery()->getSingleScalarResult();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForQuickDataQuery()
    {
        $qb = $this->createPureQueryBuilder();

        return $qb->select(
            'COUNT(DISTINCT ' . $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() . ')'
        );
    }

    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getQuickDataIterator($position = 0)
    {
        return $this->defineQuickDataIteratorQueryBuilder($position)
            ->iterate();
    }

    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getMembershipsQuickDataIterator($position = 0)
    {
        return $this->defineQuickDataIteratorQueryBuilder($position)
            ->setMaxResults(\XLite\Core\EventListener\MembershipsQuickData::CHUNK_LENGTH)
            ->iterate();
    }


    /**
     * Define quick data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQuickDataIteratorQueryBuilder($position)
    {
        return $this->createPureQueryBuilder()
            ->setFirstResult($position)
            ->setMaxResults(1000000000);
    }

    // }}}

    // {{{ Sales

    /**
     * Get top sellers depending on certain condition
     *
     * @param \XLite\Core\CommonCell $cnd       Conditions
     * @param boolean                $countOnly Count only flag OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function getTopSellers(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $result = $this->prepareTopSellersCondition($cnd)->getResult();

        return $countOnly ? count($result) : $result;
    }

    /**
     * Prepare top sellers search condition
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function prepareTopSellersCondition(\XLite\Core\CommonCell $cnd)
    {
        /** @var \XLite\Model\QueryBuilder\AQueryBuilder $qb */
        $qb = $this->createQueryBuilder()
            ->addSelect('p.sales as cnt')
            ->andWhere('p.sales > 0')
            ->setMaxResults($cnd->limit)
            ->addGroupBy('p.product_id')
            ->addOrderBy('p.sales', 'desc');

        if ($cnd->availability && $cnd->availability !== \XLite\Controller\Admin\TopSellers::AVAILABILITY_ALL) {
            $this->addTopSellersAvailabilityCondition($qb, $cnd->availability);
        }

        return  $this->assignExternalEnabledCondition($qb, 'p');
    }

    /**
     * Add availability condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb
     * @param string $condition
     */
    protected function addTopSellersAvailabilityCondition($qb, $condition)
    {
        $qb->andWhere('p.enabled = true AND (p.inventoryEnabled = false OR p.amount > 0)');
    }

    /**
     * Has top sellers
     *
     * @return boolean
     */
    public function hasTopSellers()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->limit = 1;

        return 0 < $this->getTopSellers($cnd, true);
    }

    /**
     * Find sales by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder object
     */
    public function findSalesByProduct(\XLite\Model\Product $product)
    {
        $qb = $this->createPureQueryBuilder()
            ->linkInner('p.order_items', 'o')
            ->linkInner('o.order', 'ord')
            ->linkInner('ord.paymentStatus', 'ps')
            ->select('sum(o.amount) as product_amount')
            ->andWhere('o.object = :product')
            ->setParameter('product', $product);

        return (int) $qb->andWhere($qb->expr()->in('ps.code', \XLite\Model\Order\Status\Payment::getPaidStatuses()))
            ->getSingleScalarResult();
    }

    /**
     * Count items for quick data
     *
     * @return integer
     */
    public function countForSales()
    {
        return (int) $this->defineCountForSalesQuery()->getSingleScalarResult();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForSalesQuery()
    {
        $qb = $this->createPureQueryBuilder();

        return $qb->select(
            'COUNT(DISTINCT ' . $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() . ')'
        );
    }

    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getSalesIterator($position = 0)
    {
        return $this->defineSalesIteratorQueryBuilder($position)
            ->iterate();
    }

    /**
     * Define quick data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineSalesIteratorQueryBuilder($position)
    {
        return $this->createPureQueryBuilder()
            ->setFirstResult($position)
            ->setMaxResults(1000000000);
    }

    // }}}
}
