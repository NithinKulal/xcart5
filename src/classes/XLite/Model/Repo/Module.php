<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Module repository
 */
class Module extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    const P_SUBSTRING        = 'substring';
    const P_TAG              = 'tag';
    
    const P_PRICE_FILTER     = 'priceFilter';
    const P_INSTALLED        = 'installed';
    const P_ISSYSTEM         = 'isSystem';
    const P_INACTIVE         = 'inactive';
    const P_CORE_VERSION     = 'coreVersion';
    const P_FROM_MARKETPLACE = 'fromMarketplace';
    const P_IS_LANDING       = 'isLanding';
    const P_MODULEIDS        = 'moduleIds';
    const P_EDITION          = 'edition';
    const P_VENDOR           = 'vendor';
    const P_IS_SKIN          = 'isSkin';

    /**
     * Price criteria
     */
    const PRICE_FREE = 'free';
    const PRICE_PAID = 'paid';

    /**
     * Vendors
     */
    const VENDOR_QUALITEAM = 'Qualiteam';
    const VENDOR_XCART_TEAM = 'X-Cart team';
    const VENDOR_XCART_TEAM_AND_QUALITEAM = 'X-Cart team & Qualiteam';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_INTERNAL;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('author', 'name'),
    );

    /**
     * Skin modules cache
     *
     * @var array
     */
    protected $skinModules;

    /**
     * Current skin module cache
     *
     * @var \XLite\Model\Module
     */
    protected $currentSkinModule;

    /**
     * Run-time cache of module IDs list
     *
     * @var array
     */
    protected $updateModulesCache;

    /**
     * Run-time cache of information about available hotfixes, updates and upgrades for modules
     *
     * @var array
     */
    protected $updateModulesInfoCache;

    protected $mutualModulesRegistry = array();

    /**
     * Common search
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function processQueryBuilder()
    {
        $queryBuilder = parent::processQueryBuilder();

        $this->addGroupByCondition($queryBuilder);

        return $queryBuilder;
    }

    /**
     * Prepare queryBuilder for searchResult() method
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function postprocessSearchResultQueryBuilder($queryBuilder)
    {
        return $queryBuilder;
    }

    /**
     * Search count only routine
     *
     * @return integer
     */
    protected function searchCount()
    {
        $result = $this->searchResult();

        return count($result);
    }

    /**
     * Return conditions parameters that are responsible for substring set of fields.
     *
     * @return array
     */
    protected function getSubstringSearchFields()
    {
        return array(
            $this->getRelevanceTitleField(),
            $this->getRelevanceTextField(),
        );
    }

    /**
     * Return title field name for relevance
     *
     * @return string
     */
    protected function getRelevanceTitleField()
    {
        return 'm.moduleName';
    }

    /**
     * Return text field name for relevance
     *
     * @return string
     */
    protected function getRelevanceTextField()
    {
        return 'm.description';
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
            $value = str_replace($match[0], '', $value);
        }

        return array_merge((array) $result, array_map('trim', explode(' ', $value)));
    }

    /**
     * Prepare query builder to get modules list
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     *
     * @return void
     */
    protected function addGroupByCondition(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy('m.name')
            ->addGroupBy('m.author');
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string|null                $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $searchWords = $this->getSearchWords($value);
        $searchPhrase = implode(' ', $searchWords);
        $cnd = new \Doctrine\ORM\Query\Expr\Orx();

        foreach ($this->getSubstringSearchFields() as $field) {
            foreach ($searchWords as $index => $word) {
                // Collect OR expressions
                $cnd->add($field . ' LIKE :word' . $index);
                $queryBuilder->setParameter('word' . $index, '%' . $word . '%');
            }
        }

        if ($searchPhrase) {
            $queryBuilder->addSelect(
                sprintf(
                    'RELEVANCE(%s, %s, %s) as relevance',
                    \XLite\Core\Database::getEM()->getConnection()->quote(
                        str_replace("'", '', $value)
                    ),
                    $this->getRelevanceTitleField(),
                    $this->getRelevanceTextField()
                )
            );

            $orderBys = $queryBuilder->getDQLPart('orderBy');
            $queryBuilder->resetDQLPart('orderBy');
            $queryBuilder->addOrderBy('relevance', 'desc');
            foreach ($orderBys as $value) {
                $queryBuilder->add('orderBy', $value, true);
            }
        }

        $queryBuilder->andWhere($cnd);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string|null                $value        Condition data
     *
     * @return void
     */
    protected function prepareCndTag(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.tags LIKE :tag')
            ->setParameter('tag', sprintf('%%"%s"%%', $value));
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string|null                $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEdition(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.editions LIKE :edition')
            ->setParameter('edition', sprintf('%%_%s"%%', $value));
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndVendor(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $vendors = $this->getVendors();

        if (isset($vendors[$value])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('m.author', $vendors[$value])
            );
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string|null                $value        Condition data
     *
     * @return void
     */
    protected function prepareCndModuleIds(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value) && count($value) > 0) {
            $keys = \XLite\Core\Database::buildInCondition($queryBuilder, $value);
            $queryBuilder->andWhere(
                sprintf(
                    '%s.%s IN (%s)',
                    $this->getMainAlias($queryBuilder),
                    $this->_class->identifier[0],
                    implode(', ', $keys)
                )
            );
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition
     *
     * @return void
     */
    protected function prepareCndPriceFilter(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (static::PRICE_FREE === $value) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('m.price', 0),
                        $queryBuilder->expr()->eq('m.xcnPlan', 0)
                    ),
                    $queryBuilder->expr()->eq('m.editionState', 1)
                )
            );

        } elseif (static::PRICE_PAID === $value) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->gt('m.price', 0),
                        $queryBuilder->expr()->gt('m.xcnPlan', 0)
                    ),
                    $queryBuilder->expr()->neq('m.editionState', 1)
                )
            );
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition
     *
     * @return void
     */
    protected function prepareCndInstalled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.installed = :installed')
            ->setParameter('installed', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition
     *
     * @return void
     */
    protected function prepareCndIsSystem(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.isSystem = :isSystem')
            ->setParameter('isSystem', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition
     *
     * @return void
     */
    protected function prepareCndIsSkin(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.isSkin = :isSkin')
            ->setParameter('isSkin', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     *
     * @return void
     */
    protected function prepareCndInactive(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $queryBuilder->andWhere('m.enabled = :enabled')
            ->setParameter('enabled', false);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition
     *
     * @return void
     */
    protected function prepareCndCoreVersion(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.majorVersion = :majorVersion')
            ->setParameter('majorVersion', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition
     *
     * @return void
     */
    protected function prepareCndFromMarketplace(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('m.fromMarketplace = :fromMarketplace')
                ->setParameter('fromMarketplace', true);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition
     *
     * @return void
     */
    protected function prepareCndIsLanding(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.isLanding = :isLanding')
            ->setParameter('isLanding', $value);
    }

    // }}}

    // {{{ Markeplace-related routines

    /**
     * One time in session we update list of marketplace modules
     *
     * @param array $data Data received from marketplace
     *
     * @return void
     */
    public function updateMarketplaceModules(array $data)
    {
        // Get the list of non-installed modules from marketplace
        $queryBuilder = $this->createQueryBuilder();
        $this->prepareCndFromMarketplace($queryBuilder, true);
        $this->prepareCndInstalled($queryBuilder, false);

        $modules = $queryBuilder->getResult();

        // Update existing modules
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $key = sprintf(
                    '%s_%s_%s.%s',
                    $module->getAuthor(),
                    $module->getName(),
                    $module->getMajorVersion(),
                    $module->getMinorVersion()
                );

                if (isset($data[$key])) {
                    $this->update($module, $data[$key], false);
                    unset($data[$key]);
                } else {
                    \XLite\Core\Database::getEM()->remove($module);
                }
            }

            $this->flushChanges();
        }

        // Add new modules
        $this->insertInBatch($data, false);
    }

    // }}}

    // {{{ Version-related routines

    protected function getModules()
    {
        if (null === $this->updateModulesCache) {
            $modules = $this->createQueryBuilder()
                ->select('m.moduleID as id, m.installed, reverse.moduleID as reverseModule, hotfix.moduleID as hotfixModule, updated.moduleID as updateModule, upgrade.moduleID as upgradeModule')
                ->leftJoin(
                    'XLite\Model\Module',
                    'reverse',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    $this->getLinkCondition('reverse')
                    . ' AND m.fromMarketplace != reverse.fromMarketplace'
                )
                ->leftJoin(
                    'XLite\Model\Module',
                    'hotfix',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    $this->getLinkCondition('hotfix')
                    . ' AND hotfix.majorVersion = m.majorVersion'
                    . ' AND hotfix.minorVersion = m.minorVersion'
                    . ' AND hotfix.build > m.build'
                    . ' AND hotfix.fromMarketplace = :fromMarkeplace'
                )
                ->leftJoin(
                    'XLite\Model\Module',
                    'updated',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    $this->getLinkCondition('updated')
                    . ' AND updated.majorVersion = m.majorVersion'
                    . ' AND updated.minorVersion > m.minorVersion'
                    . ' AND updated.fromMarketplace = :fromMarkeplace'
                )
                ->leftJoin(
                    'XLite\Model\Module',
                    'upgrade',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    $this->getLinkCondition('upgrade')
                    . ' AND upgrade.majorVersion > m.majorVersion'
                    . ' AND upgrade.fromMarketplace = :fromMarkeplace'
                )
                ->addGroupBy('id')
                ->setParameter('fromMarkeplace', 1);

            $modules = $modules
                ->getQuery()
                ->getResult();

            $this->updateModulesCache = array();
            foreach ($modules as $module) {
                $this->updateModulesCache[$module['id']] = array(
                    'installed' => $module['installed'],
                    'reverse'   => $module['reverseModule'],
                    'hotfix'    => $module['hotfixModule'],
                    'update'    => $module['updateModule'],
                    'upgrade'   => $module['upgradeModule'],
                );
            }
        }

        return $this->updateModulesCache;
    }

    /**
     * Helper method to get main link condition for getModules() method
     *
     * @param string $alias Alias of linked entity
     *
     * @return string
     */
    protected function getLinkCondition($alias)
    {
        return $alias . '.author = m.author AND ' . $alias . '.name = m.name';
    }

    /**
     * Search for modules having an older version
     *
     * @param \XLite\Model\Module $module Module to get info from
     *
     * @return \XLite\Model\Module
     */
    public function getModuleForHotfix(\XLite\Model\Module $module)
    {
        $modules = $this->getModules();

        $hotfix = $modules[$module->getModuleID()]['hotfix'];

        return $hotfix
            ? $this->find($hotfix)
            : null;
    }

    /**
     * Get info about available modules hotfixes, updates and upgrades
     * Return result as an array(
     *   'hotfix' => true|false, 'update' => true|false, 'upgrade' => true|false
     * )
     *
     * @return array
     */
    public function getUpgradeModulesInfoHash()
    {
        if (!isset($this->updateModulesInfoCache)) {

            $fields = array('hotfix', 'update', 'upgrade');

            $result = array_fill_keys($fields, false);

            $modules = $this->getModules();

            foreach ($modules as $id => $m) {

                if ($m['installed']) {

                    $stop = true;

                    foreach ($fields as $field) {
                        $result[$field] = $result[$field] || !empty($m[$field]);
                        $stop = $stop && $result[$field];
                    }

                    if ($stop) {
                        break;
                    }
                }
            }

            $this->updateModulesInfoCache = $result;
        }

        return $this->updateModulesInfoCache;
    }

    /**
     * Search for modules having an older version
     *
     * @param \XLite\Model\Module $module Module to get info from
     *
     * @return \XLite\Model\Module
     */
    public function getModuleForUpdate(\XLite\Model\Module $module)
    {
        $modules = $this->getModules();

        $update = $modules[$module->getModuleID()]['update'];

        return $update
            ? $this->find($update)
            : null;
    }

    /**
     * Search for modules having an elder version
     *
     * @param \XLite\Model\Module $module Module to get info from
     *
     * @return \XLite\Model\Module
     */
    public function getModuleFromMarketplace(\XLite\Model\Module $module)
    {
        $modules = $this->getModules();

        $reverse = $modules[$module->getModuleID()]['reverse'];

        return $module->getFromMarketplace()
            ? $module
            : $this->find($reverse);
    }

    /**
     * Search for installed module
     *
     * @param \XLite\Model\Module $module Module to get info from
     *
     * @return \XLite\Model\Module
     */
    public function getModuleInstalled(\XLite\Model\Module $module)
    {
        $modules = $this->getModules();

        $reverse = $modules[$module->getModuleID()]['reverse'];

        return !$module->getFromMarketplace()
            ? $module
            : $this->find($reverse);
    }

    /**
     * Search module for upgrade
     *
     * @param \XLite\Model\Module $module Currently installed module
     *
     * @return \XLite\Model\Module
     */
    public function getModuleForUpgrade(\XLite\Model\Module $module)
    {
        $modules = $this->getModules();

        $upgrade = $modules[$module->getModuleID()]['upgrade'];

        return $upgrade
            ? $this->find($upgrade)
            : null;
    }

    // {{{ Search for dependencies

    /**
     * Search dependent modules by their class names
     *
     * @param array $classes List of class names
     *
     * @return array
     */
    public function getDependencyModules(array $classes)
    {
        $result = $this->getDependencyModulesCommon($classes, false);

        foreach ($result as $module) {
            unset($classes[$module->getActualName()]);
        }

        if (!empty($classes)) {
            $result = array_merge($result, $this->getDependencyModulesCommon($classes, true));
        }

        return $result;
    }

    /**
     * Common method to search modules by list of class names
     *
     * @param array   $classes         List of class names
     * @param boolean $fromMarketplace Flag OPTIONAL
     *
     * @return array
     */
    protected function getDependencyModulesCommon(array $classes, $fromMarketplace)
    {
        $conditions = array();
        $queryBuilder = $this->createQueryBuilder('m');

        foreach (array_keys($classes) as $idx => $class) {
            list($author, $name) = explode('\\', $class);

            $conditions[] = new \Doctrine\ORM\Query\Expr\Andx(
                array('m.name = :name' . $idx, 'm.author = :author' . $idx)
            );
            $queryBuilder->setParameter('name' . $idx, $name)
                ->setParameter('author' . $idx, $author);
        }

        return $queryBuilder->andWhere(new \Doctrine\ORM\Query\Expr\Orx($conditions))
            ->andWhere('m.fromMarketplace = :fromMarketplace')
            ->setParameter('fromMarketplace', $fromMarketplace)
            ->addGroupBy('m.author', 'm.name')
            ->getResult();
    }

    // }}}

    /**
     * Add all enabled modules to ENABLED registry
     *
     * @return void
     */
    public function addEnabledModulesToRegistry()
    {
        foreach ($this->findBy(array('enabled' => true)) as $module) {
            \XLite\Core\Database::getInstance()->registerModuleToEnabledRegistry(
                $module->getActualName(),
                \Includes\Utils\ModulesManager::getModuleProtectedStructures($module->getAuthor(), $module->getName())
            );
        }
    }

    /**
     * Get registry HASH of enabled modules
     *
     * @return string
     */
    public function calculateEnabledModulesRegistryHash()
    {
        $hash = '';

        foreach ($this->findBy(array('enabled' => true)) as $module) {
            $hash .= $module->getActualName() . $module->getVersion();
        }

        return hash('md4', $hash);
    }

    /**
     * Returns the maximum downloads counter
     *
     * @return integer
     */
    public function getMaximumDownloads()
    {
        $module = $this->findBy(array('fromMarketplace' => true), array('downloads' => 'desc'), 1);

        return $module[0]->getDownloads();
    }

    /**
     * Return marketplace url
     *
     * @param string  $author Author
     * @param string  $module Module name
     *
     * @return string
     */
    public function getMarketplaceUrlByName($author, $module)
    {
        $moduleObject = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(
            array(
                'author'          => $author,
                'name'            => $module,
                'fromMarketplace' => true,
            )
        );

        $url = '';

        if ($moduleObject) {
            $url = $moduleObject->getMarketplaceURL();
        }

        return $url;
    }

    /**
     * Return the page number of marketplace page for specific search
     *
     * @param string  $author Author
     * @param string  $module Module name
     * @param integer $limit  Page limit
     *
     * @return integer
     */
    public function getMarketplacePageId($author, $module, $limit)
    {
        $moduleInfo = $this->findOneBy(
            array(
                'author'          => $author,
                'name'            => $module,
                'fromMarketplace' => true,
            )
        );

        $page = 0;
        if ($moduleInfo) {
            $qb = $this->createPureQueryBuilder('m')
                ->select('m.moduleID')
                ->where('m.fromMarketplace = :true AND m.isSystem = :false')
                ->setParameter('true', true)
                ->setParameter('false', false);

            $this->prepareCndOrderBy(
                $qb,
                array(
                    \XLite\View\ItemsList\Module\AModule::SORT_OPT_ALPHA,
                    \XLite\View\ItemsList\AItemsList::SORT_ORDER_ASC
                )
            );

            // The module list contains several records with all major versions available
            $this->addGroupByCondition($qb);

            $allModules = $qb->getArrayResult();

            $key        = array_search(array('moduleID' => $moduleInfo->getModuleID()), $allModules) + 1;
            $page       = intval($key / $limit);
            $remainder  = $key % $limit;
        }

        return (isset($remainder) && 0 === $remainder) ? $page : $page + 1;
    }

    /**
     * Return the page number of "installed modules" page for specific search
     *
     * @param string  $author Author
     * @param string  $module Module
     * @param integer $limit  Limit
     *
     * @return integer
     */
    public function getInstalledPageId($author, $module, $limit)
    {
        $moduleInfo = $this->findOneBy(
            array(
                'author'    => $author,
                'name'      => $module,
                'installed' => true,
            )
        );

        $page = null;

        if ($moduleInfo) {

            $page = 0;

            $qb = $this->createPureQueryBuilder('m')
                ->select('m.moduleID')
                ->where('m.installed = :true')
                ->andWhere('m.isSystem = :false')
                ->setParameter('true', true)
                ->setParameter('false', false)
                ->orderBy(
                    \XLite\View\ItemsList\Module\AModule::SORT_OPT_ALPHA,
                    \XLite\View\ItemsList\AItemsList::SORT_ORDER_ASC
                );

            $allModules = $qb->getArrayResult();

            if (0 == $limit) {
                // To avoid potential error 'division by zero'
                $limit = 1;
            }

            $key        = array_search(array('moduleID' => $moduleInfo->getModuleID()), $allModules) + 1;
            $page       = intval($key / $limit);
            $remainder  = $key % $limit;
        }

        if (!is_null($page)) {
            $page = (0 === $remainder ? $page : $page + 1);
        }

        return $page;
    }

    /**
     * Find one module by name
     *
     * @param string    $name               Module name
     * @param boolean   $fromMarketplace    Should module be from marketplace OPTIONAL
     * 
     *
     * @return \XLite\Model\Module
     */
    public function findOneByModuleName($name, $fromMarketplace = false)
    {
        list($author, $module) = explode('\\', $name, 2);

        return $this->findOneBy(
            array(
                'author'          => $author,
                'name'            => $module,
                'fromMarketplace' => $fromMarketplace,
            )
        );
    }

    /**
     * Check - module is eEnabled or not
     *
     * @param string $name Module name
     *
     * @return boolean
     */
    public function isModuleEnabled($name)
    {
        $module = $this->findOneByModuleName($name);

        return $module && $module->getEnabled();
    }

    /**
     * Return true is modules from marketplace exist in the database
     *
     * @param boolean $update Flag: update modules from marketplace (if true)
     *
     * @return boolean
     */
    public function hasMarketplaceModules($update = false)
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->{static::P_FROM_MARKETPLACE} = true;

        $modulesCount = $this->search($cnd, true);

        if (0 == $modulesCount && $update) {
            \XLite\Core\Marketplace::getInstance()->getAddonsList(0);
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Database::getEM()->clear();

            $modulesCount = $this->search($cnd, true);
        }

        return 0 < $modulesCount;
    }

    /**
     * Marketplace modules list
     *
     * @param boolean $enabledFlag Edition OPTIONAL
     *
     * @return array
     */
    public function getNonFreeEditionModulesList($enabledFlag = false)
    {
        $result = array();

        $cnd = new \XLite\Core\CommonCell;
        $cnd->{static::P_FROM_MARKETPLACE} = true;

        $modules = $this->search($cnd);
        $freeEdition = \XLite\Core\Marketplace::getInstance()->getFreeLicenseEdition();

        foreach ($modules as $key => $module) {
            $editions = $module->getEditions();

            if (!empty($editions) && !in_array($freeEdition, $editions)) {
                $installedModule = $this->findOneBy(
                    array(
                        'name'            => $module->getName(),
                        'author'          => $module->getAuthor(),
                        'fromMarketplace' => 0,
                        'installed'       => 1,
                    )
                );

                if ($installedModule && (!$enabledFlag || $installedModule->getEnabled())) {
                    $result[$key] = $installedModule;
                }
            }
        }

        return $result;
    }

    /**
     * Marketplace modules list (nonFree and Business)
     *
     * @param boolean $enabledFlag Edition OPTIONAL
     *
     * @return array
     */
    public function getBusinessEditionModulesList()
    {
        $result = array();

        $cnd = new \XLite\Core\CommonCell;
        $cnd->{static::P_FROM_MARKETPLACE} = true;

        $modules = $this->search($cnd);
        $freeEdition = \XLite\Core\Marketplace::getInstance()->getFreeLicenseEdition();

        foreach ($modules as $key => $module) {
            $editions = $module->getEditionNames();

            if (!empty($editions) && (!in_array($freeEdition, $editions))) {
                $installedModule = $this->findOneBy(
                    array(
                        'name'            => $module->getName(),
                        'author'          => $module->getAuthor(),
                        'fromMarketplace' => 0,
                        'installed'       => 1,
                    )
                );

                \XLite\Core\Database::getEM()->detach($module);

                if ($installedModule) {
                    $module->setInstalled(true);
                    $module->setEnabled($installedModule->getEnabled());

                } elseif (!in_array('Business', $editions)
                    || preg_match('/\[DEPRECATED\]$/', $module->getModuleName())
                ) {
                    continue;
                }

                $result[$key] = $module;
            }
        }

        return $result;
    }

    // {{{ findModuleByName()

    /**
     * Find module by module author/name values
     *
     * @param string $moduleName Module author/name (string 'Author\\Name')
     *
     * @return boolean
     */
    public function findModuleByName($moduleName)
    {
        $result = null;

        list($author, $name) = explode('\\', $moduleName);

        $modules = $this->defineFindModuleByNameQuery($author, $name)->getResult();

        if ($modules) {
            $result = current($modules);
        }

        return $result;
    }

    /**
     * Prepare query builder for findModuleByName() method
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindModuleByNameQuery($author, $name)
    {
        return $this->createPureQueryBuilder('m')
            ->where('m.author = :author')
            ->andWhere('m.name = :name')
            ->orderBy('m.installed', 'DESC')
            ->setParameter('author', $author)
            ->setParameter('name', $name)
            ->setMaxResults(1);
    }

    // }}}

    // {{{ getModuleState()

    /**
     * Find module state by module author/name values
     *
     * @param string $module Module author/name (string 'Author\\Name')
     *
     * @return boolean
     */
    public function getModuleState($module)
    {
        list($author, $name) = explode('\\', $module);

        $data = $this->defineGetModuleStateQuery($author, $name)->getArrayResult();

        if (0 < count($data)) {
            $result = $data[0]['enabled'];

        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Prepare query builder for getModuleState() method
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineGetModuleStateQuery($author, $name)
    {
        return $this->createPureQueryBuilder('m')
            ->select('m.enabled')
            ->where('m.author = :author')
            ->andWhere('m.name = :name')
            ->andWhere('m.installed = :true')
            ->orderBy('m.installed', 'DESC')
            ->addOrderBy('m.author', 'ASC')
            ->addOrderBy('m.name', 'ASC')
            ->setParameter('author', $author)
            ->setParameter('name', $name)
            ->setParameter('true', 1)
            ->setMaxResults(1);
    }

    // }}}

    // {{{ Tags

    /**
     * Returns all available tags
     *
     * @return array
     */
    public function getTags()
    {
        $tags = $this->defineGetTags()->getArrayResult();

        $tags = array_unique(
            array_reduce(
                array_map(
                    function ($a) {
                        return isset($a['tags']) ? $a['tags'] : array();
                    },
                    $tags
                ),
                'array_merge',
                array()
            )
        );

        $result = array();
        foreach ($tags as $tag) {
            $localeTag = $this->getLocaleTagName($tag);
            $result[$localeTag] = $tag;
        }

        ksort($result);
        $result = array_values($result);

        return array_combine($result, $result);
    }

    /**
     * Returns query builder for getTags
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineGetTags()
    {
        return $this->createPureQueryBuilder()
            ->select('m.tags')
            ->groupBy('m.tags');
    }

    /**
     * Get translated tag
     *
     * @param string $tag Tag
     *
     * @return string
     */
    protected function getLocaleTagName($tag)
    {
        $label = 'tag-' . $tag;
        $translation = \XLite\Core\Translation::getInstance()->translate($label);

        return ($translation === $label) ? $tag : $translation;
    }

    // }}}

    // {{{ Vendors

    /**
     * Returns vendors
     *
     * @return array
     */
    public function getVendors()
    {
        $vendors = array_reduce(
            $this->defineGetVendors()->getArrayResult(),
            function ($result, $a) {
                list($author, $authorName) = array_values($a);

                $result[$authorName] = isset($result[$authorName])
                    ? array_merge($result[$authorName], array($author))
                    : array($author);

                return $result;
            },
            array()
        );

        return $this->prepareVendors($vendors);
    }

    /**
     * Returns query builder for getVendors
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineGetVendors()
    {
        return $this->createPureQueryBuilder()
            ->select('m.author', 'm.authorName')
            ->groupBy('m.author')
            ->orderBy('m.authorName');
    }

    /**
     * Prepare vendors array
     *
     * @return array
     */
    protected function prepareVendors($vendors)
    {
        $result = array(
            static::VENDOR_XCART_TEAM_AND_QUALITEAM => array(),
        );

        foreach ($vendors as $authorName => $authors) {
            switch ($authorName) {
                case static::VENDOR_XCART_TEAM:
                case static::VENDOR_QUALITEAM:
                    $result[static::VENDOR_XCART_TEAM_AND_QUALITEAM]
                        = array_merge($result[static::VENDOR_XCART_TEAM_AND_QUALITEAM], $authors);
                    break;

                default:
                    $result[$authorName] = $authors;
                    break;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Uninstall module routine

    /**
     * Uninstall module
     *
     * @param \XLite\Model\Module $module    Module object
     * @param array               &$messages Messages list
     *
     * @return boolean
     */
    public function uninstallModule(\XLite\Model\Module $module, &$messages)
    {
        $result = false;

        // Get module pack
        $pack = new \XLite\Core\Pack\Module($module);
        $dirs = $pack->getDirs();

        $nonWritableDirs = array();

        // Check module directories permissions
        foreach ($dirs as $dir) {
            if (\Includes\Utils\FileManager::isExists($dir)
                && !\Includes\Utils\FileManager::isDirWriteable($dir)
            ) {
                $nonWritableDirs[] = \Includes\Utils\FileManager::getRelativePath($dir, LC_DIR_ROOT);
            }
        }

        $params = array(
            'name' => sprintf('%s v%s (%s)', $module->getModuleName(), $module->getVersion(), $module->getAuthorName()),
        );

        if (empty($nonWritableDirs)) {
            $yamlData = array();
            $yamlFiles = \Includes\Utils\ModulesManager::getModuleYAMLFiles($module->getAuthor(), $module->getName());

            foreach ($yamlFiles as $yamlFile) {
                $yamlData[] = \Includes\Utils\FileManager::read($yamlFile);
            }

            if (!$module->checkModuleMainClass()) {
                $classFile = LC_DIR_CLASSES . \Includes\Utils\Converter::getClassFile($module->getMainClass());

                if (\Includes\Utils\FileManager::isFileReadable($classFile)) {
                    require_once $classFile;
                }
            }

            // Call uninstall event method
            $r = $module->callModuleMethod('callUninstallEvent', 111);
            if (111 == $r) {
                \XLite\Logger::getInstance()->log(
                    $module->getActualName() . ': Method callUninstallEvent() was not called'
                );
            }

            \Includes\Utils\ModulesManager::disableModule($module->getActualName());
            \Includes\Utils\ModulesManager::removeModuleFromDisabledStructure($module->getActualName());

            // Get installed module record from database
            $moduleToDelete = $this->getModuleInstalled($module);

            if (!\XLite\Core\Database::getEM()->contains($moduleToDelete)) {
                // Detached entity: reload from database
                $moduleToDelete = \XLite\Core\Database::getEM()->merge($moduleToDelete);
            }

            if ($moduleToDelete) {

                try {
                    // Remove module from DB
                    $this->delete($moduleToDelete);

                } catch (\Exception $e) {
                    $messages[] = $e->getMessage();
                }
            }

            if ($moduleToDelete->getModuleID()) {
                $messages[] = \XLite\Core\Translation::getInstance()->translate('A DB error occurred while uninstalling the module X', $params);

            } else {
                if (!empty($yamlData)) {
                    foreach ($yamlData as $yaml) {
                        \XLite\Core\Database::getInstance()->unloadFixturesFromYaml($yaml);
                    }
                }

                $messages[] = \XLite\Core\Translation::getInstance()->translate('The module X has been uninstalled successfully', $params);

                $result = true;
            }

            // Remove from FS
            foreach ($dirs as $dir) {
                \Includes\Utils\FileManager::unlinkRecursive($dir);
            }

        } else {
            $messages[] = \XLite\Core\Translation::getInstance()->translate(
                'Unable to delete module X files: some dirs have no writable permissions: Y',
                $params + array(
                    'dirs' => implode(', ', $nonWritableDirs),
                )
            );
        }

        return $result;
    }

    // }}}

    // {{{ Skin modules

    public function getSkinModules()
    {
        if (null === $this->skinModules) {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Module::P_INSTALLED} = true;
            $cnd->{\XLite\Model\Repo\Module::P_IS_SKIN} = true;

            $this->skinModules = $this->search($cnd);
        }

        return $this->skinModules;
    }

    /**
     * Returns current skin
     *
     * @return \XLite\Model\Module
     */
    public function getCurrentSkinModule()
    {
        if (null === $this->currentSkinModule) {
            $this->currentSkinModule = array_reduce($this->getSkinModules(), function ($carry, $item) {
                return $carry ?: ($item->getEnabled() ? $item : null);
            }) ?: false;
        }

        return $this->currentSkinModule;
    }

    // }}}

    /**
     * Find the list of unallowed modules used in the store
     *
     * @return array
     */
    public function findUnallowedModules()
    {
        $result = array();

        $modules = $this->defineUnallowedModules()->getResult();

        if ($modules) {

            $isTrialExpired = \XLite::isTrialPeriodExpired();

            if (\XLite::getXCNLicense()) {
                $keyData = \XLite::getXCNLicense()->getKeyData();
                $keyEdition = !empty($keyData['editionName']) ? $keyData['editionName'] : null;

            } elseif ($isTrialExpired) {
                $keyEdition = 'Trial';

            } else {
                $keyEdition = null;
            }

            $allKeys = \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->findAll();

            $keys = array();

            if ($allKeys) {
                foreach ($allKeys as $key) {
                    $keys[$key->getAuthor() . '\\' . $key->getName()] = $key;
                }
            }

            foreach ($modules as $row) {

                $module = $row[0];

                $key = !empty($keys[$module->getActualName()]) ? $keys[$module->getActualName()] : null;

                if ($row['editions']) {
                    $moduleEditions = array_map(
                        function ($v) { return preg_replace('/^\d*_(.+)/', '\\1', $v); },
                        $row['editions']
                    );

                } else {
                    $moduleEditions = array();
                }

                $addModule = (
                    ($keyEdition || $isTrialExpired)
                    && $moduleEditions
                    && 2 == $row['editionState']
                    && !in_array('Free', $moduleEditions)
                    && !in_array($keyEdition, $moduleEditions)
                )
                || (
                    !empty($row['xbProductId'])
                    && $row['price'] > 0
                    && (
                        !$key
                        || !$key->getActive()
                    )
                );

                if ($addModule) {
                    $result[] = array(
                        $module,
                        'key'          => $key ? $key->getKeyValue() : null,
                        'editions'     => $moduleEditions,
                        'editionState' => $row['editionState'],
                        'price'        => $row['price'],
                        'xbProductId'  => $row['xbProductId'],
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Get query builder for findUnallowedModules() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineUnallowedModules()
    {
        // Search for installed modules...
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.fromMarketplace = 0')
            ->andWhere('m.installed = 1')
            ->andWhere('m.enabled = 1');

        // Inner join with modules from marketplace...
        $qb->linkInner('\XLite\Model\Module', 'mm', 'WITH', 'mm.name = m.name AND mm.author = m.author AND mm.fromMarketplace = 1')
            ->addSelect('mm.price price')
            ->addSelect('mm.editions editions')
            ->addSelect('mm.editionState editionState')
            ->addSelect('mm.xbProductId xbProductId');

        // Generate SQL part for modules from marketplace:
        // ...AND (mm.price > 0 OR NOT (mm.editions = '' OR mm.editions = 'a:0:{}')) ...
        $orCnd = $qb->expr()->orX();
        $orCnd->add('mm.editions = :empty');
        $orCnd->add('mm.editions = :emptyArray');

        $orCnd2 = $qb->expr()->orX();
        $orCnd2->add('mm.price > 0');
        $orCnd2->add($qb->expr()->not($orCnd));

        $qb->andWhere($orCnd2)
            ->setParameter('empty', '')
            ->setParameter('emptyArray', 'a:0:{}');

        return $qb;
    }

    /**
     * Get edition ID by its name
     *
     * @param string $name Edition name
     *
     * @return integer
     */
    public function getEditionIdByName($name)
    {
        $result = null;

        $qb = $this->createQueryBuilder('m');
        $this->prepareCndFromMarketplace($qb, 1);
        $this->prepareCndEdition($qb, $name);
        $this->prepareCndLimit($qb, array(0, 1));

        $module = $qb->getSingleResult();

        if ($module) {
            $editionIds = $module->getEditionIds();
            if (isset($editionIds[$name])) {
                $result = $editionIds[$name];
            }
        }

        return $result;
    }
}
