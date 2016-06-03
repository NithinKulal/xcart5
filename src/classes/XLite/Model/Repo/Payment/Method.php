<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Payment;

/**
 * Payment method repository
 */
class Method extends \XLite\Model\Repo\Base\I18n implements \XLite\Model\Repo\Base\IModuleLinked
{
    /**
     * Names of fields that are used in search
     */
    const P_ENABLED             = 'enabled';
    const P_MODULE_ENABLED      = 'moduleEnabled';
    const P_ADDED               = 'added';
    const P_ONLY_PURE_OFFLINE   = 'onlyPureOffline';
    const P_ONLY_MODULE_OFFLINE = 'onlyModuleOffline';
    const P_POSITION            = 'position';
    const P_TYPE                = 'type';

     // Use the Force, Luke
    const P_ORDER_BY_FORCE       = 'orderByForce';

    const P_NAME                = 'name';
    const P_COUNTRY             = 'country';
    const P_EX_COUNTRY          = 'exCountry';

    /**
     * Name of the field which is used for default sorting (ordering)
     */
    const FIELD_DEFAULT_POSITION = 'orderby';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('service_name'),
    );

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
        $queryBuilder
            ->leftJoin(
                $alias . '.translations',
                $translationsAlias,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                $translationsAlias . '.code = :lng OR ' . $translationsAlias . '.code = :lng2'
            )
            ->setParameter('lng', $code)
            ->setParameter('lng2', 'en');

        return $queryBuilder;
    }

    /**
     * Update entity
     *
     * @param \XLite\Model\AEntity $entity Entity to update
     * @param array                $data   New values for entity properties
     * @param boolean              $flush  Flag OPTIONAL
     *
     * @return void
     */
    public function update(\XLite\Model\AEntity $entity, array $data = array(), $flush = self::FLUSH_BY_DEFAULT)
    {
        $name = null;
        foreach ($entity->getTranslations() as $translation) {
            if ($translation->getName()) {
                $name = $translation->getName();
                break;
            }
        }

        if ($name) {
            foreach ($entity->getTranslations() as $translation) {
                if (!$translation->getName()) {
                    $translation->setName($name);
                }
            }
        }

        parent::update($entity, $data, $flush);
    }


    // {{{ Module link

    /**
     * Switch module link
     *
     * @param boolean             $enabled Module enabled status
     * @param \XLite\Model\Module $module  Model module
     *
     * @return mixed
     */
    public function switchModuleLink($enabled, \XLite\Model\Module $module)
    {
        return $this->defineQuerySwitchModuleLink($enabled, $module)->execute();
    }

    /**
     * Define query for switchModuleLink() method
     *
     * @param boolean             $enabled Module enabled status
     * @param \XLite\Model\Module $module  Model module
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQuerySwitchModuleLink($enabled, \XLite\Model\Module $module)
    {
        $qb = $this->getQueryBuilder()
            ->update($this->_entityName, 'e')
            ->set('e.moduleEnabled', ':enabled')
            ->where('LOCATE(:class, e.class) > 0')
            ->setParameter('enabled', (bool)$enabled ? 1 : 0)
            ->setParameter('class', $module->getActualName());

        if ($enabled) {
            $qb->set('e.fromMarketplace', ':disabled')
                ->setParameter('disabled', false);
        }

        return $qb;
    }

    // }}}

    /**
     * Prepare certain search condition for module name
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ('' !== $value) {
            $queryBuilder
                ->andWhere('translations.name LIKE :name')
                ->setParameter('name', "%" . $value . "%");
        }
    }

    /**
     * Prepare certain search condition for module name
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndCountry(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            $alias = $this->getMainAlias($queryBuilder);

            $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $alias . '.countries LIKE :country',
                        $alias . '.countries = :emptyArray',
                        $alias . '.countries = :undefinedValue',
                        $alias . '.countries = :emptyValue'
                    )
                )
                ->setParameter('country', '%"' . $value . '"%')
                ->setParameter('emptyArray', 'a:0:{}')
                ->setParameter('undefinedValue', 'N;')
                ->setParameter('emptyValue', '');
        }
    }

    /**
     * Prepare certain search condition for module name
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndExCountry(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            $alias = $this->getMainAlias($queryBuilder);

            $queryBuilder->andWhere(
                    $queryBuilder->expr()->not(
                        $alias . '.exCountries LIKE :country'
                    )
                )
                ->setParameter('country', '%"' . $value . '"%');
        }
    }

    /**
     * Prepare certain search condition for enabled flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.enabled = :enabled_value')
            ->setParameter('enabled_value', $value);
    }

    /**
     * Prepare certain search condition for moduleEnabled flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndModuleEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.moduleEnabled = :module_enabled_value')
            ->setParameter('module_enabled_value', (bool)$value);
    }

    /**
     * Prepare certain search condition for added flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndAdded(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (isset($value)) {
            $queryBuilder
                ->andWhere($this->getMainAlias($queryBuilder) . '.added = :added_value')
                ->setParameter('added_value', $value);
        }
    }

    /**
     * Prepare certain search condition for onlyModuleOffline flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndOnlyPureOffline(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder
                ->andWhere($alias . '.class = :class AND ' . $alias . '.type = :offlineType')
                ->setParameter('class', 'Model\Payment\Processor\Offline')
                ->setParameter('offlineType', \XLite\Model\Payment\Method::TYPE_OFFLINE);
        }
    }

    /**
     * Prepare certain search condition for onlyModuleOffline flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndOnlyModuleOffline(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder
                ->andWhere($alias . '.class != :class AND ' . $alias . '.type = :offlineType')
                ->setParameter('class', 'Model\Payment\Processor\Offline')
                ->setParameter('offlineType', \XLite\Model\Payment\Method::TYPE_OFFLINE);
        }
    }

    /**
     * Prepare certain search condition for position
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndPosition(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        if (!$countOnly) {
            list($sort, $order) = $value;

            $queryBuilder->addOrderBy($this->getMainAlias($queryBuilder) . '.' . $sort, $order);
        }
    }

    /**
     * Prepare certain search condition for position
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            if (is_array($value)) {
                $queryBuilder->andWhere($alias . '.type IN (' . $queryBuilder->getInCondition($value, 'type') . ')');

            } else {
                $queryBuilder->andWhere($alias . '.type = :type')
                    ->setParameter('type', $value);
            }
        }
    }

    /**
     * Prepare certain search condition for position
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndOrderByForce(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        if (!$countOnly) {
            list($sort, $order) = $this->getSortOrderValue($value);

            $queryBuilder->orderBy($sort, $order);
            $this->assignDefaultOrderBy($queryBuilder);
        }
    }

    // }}}

    // {{{ Finders

    /**
     * Find all methods
     *
     * @return \Doctrine\Common\Collection\Collection
     */
    public function findAllMethods()
    {
        return $this->defineAllMethodsQuery()->getResult();
    }

    /**
     * Find all active and ready for checkout payment methods.
     *
     * @return \Doctrine\Common\Collection\Collection
     */
    public function findAllActive()
    {
        return $this->defineAllActiveQuery()->getResult();
    }

    /**
     * Check - has active payment modules or not
     *
     * @return boolean
     */
    public function hasActivePaymentModules()
    {
        return 0 < $this->defineHasActivePaymentModulesQuery()->getSingleScalarResult();
    }

    /**
     * Find offline method (not from modules)
     *
     * @return array
     */
    public function findOffline()
    {
        $list = array();

        foreach ($this->defineFindOfflineQuery()->getResult() as $method) {
            if (!preg_match('/\\\Module\\\/Ss', $method->getClass())) {
                $list[] = $method;
            }
        }

        return $list;
    }

    /**
     * Find offline method (only from modules)
     *
     * @return array
     */
    public function findOfflineModules()
    {
        $list = array();

        foreach ($this->defineFindOfflineQuery()->getResult() as $method) {
            if (preg_match('/\\\Module\\\/Ss', $method->getClass())) {
                $list[] = $method;
            }
        }

        return $list;
    }

    /**
     * Find payment methods by specified type for dialog 'Add payment method'
     *
     * @param string $type Payment method type
     *
     * @return \Doctrine\Common\Collection\Collection
     */
    public function findForAdditionByType($type)
    {
        return $this->defineAdditionByTypeQuery($type)->getResult();
    }

    /**
     * Define query for findAdditionByType()
     *
     * @param string $type Payment method type
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAdditionByTypeQuery($type)
    {
        $qb = $this->createPureQueryBuilder('m');

        $this->prepareCndType($qb, $type, false);
        $this->prepareCndOrderBy($qb, array('m.adminOrderby'), false);

        return $this->addOrderByForAdditionByTypeQuery($qb);
    }

    /**
     * Add ORDER BY for findAdditionByType() query
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addOrderByForAdditionByTypeQuery($qb)
    {
        return $qb->addOrderBy('m.moduleName', 'asc');
    }

    /**
     * Define query for findAllMethods() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllMethodsQuery()
    {
        return $this->createQueryBuilder();
    }

    /**
     * Define query for findAllActive() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllActiveQuery()
    {
        return $this->createQueryBuilder()
            ->andWhere('m.enabled = :true')
            ->andWhere('m.added = :true')
            ->setParameter('true', true);
    }

    /**
     * Define query for hasActivePaymentModules() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineHasActivePaymentModulesQuery()
    {
        return $this->createPureQueryBuilder()
            ->select('COUNT(m.method_id) cns')
            ->andWhere('m.type != :offline')
            ->andWhere('m.moduleEnabled = :moduleEnabled')
            ->setParameter('offline', \XLite\Model\Payment\Method::TYPE_OFFLINE)
            ->setParameter('moduleEnabled', true)
            ->setMaxResults(1);
    }

    /**
     * Define query for findOffline() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOfflineQuery()
    {
        return $this->createPureQueryBuilder()
            ->setParameter('offline', \XLite\Model\Payment\Method::TYPE_OFFLINE);
    }

    // }}}

    // {{{ Update payment methods from marketplace

    /**
     * Update payment methods with data received from the marketplace
     *
     * @param array List of payment methods received from marketplace
     *
     * @return void
     */
    public function updatePaymentMethods($data)
    {
        if (!empty($data) && is_array($data)) {

            $methods = array();

            // Get all payment methods list as an array
            $tmpMethods = $this->createQueryBuilder('m')
                ->select('m')
                ->getQuery()
                ->getArrayResult();

            if ($tmpMethods) {
                // Prepare associative array of existing methods with 'service_name' as a key
                foreach ($tmpMethods as $m) {
                    $methods[$m['service_name']] = $m;
                }
            }

            foreach ($data as $i => $extMethod) {

                if (!empty($extMethod['service_name'])) {

                    $data[$i] = $extMethod;

                    if (isset($methods[$extMethod['service_name']])) {

                        // Method already exists in the database

                        if (!$methods[$extMethod['service_name']]['fromMarketplace']) {
                            $data[$i] = array(
                                'service_name' => $extMethod['service_name'],
                                'countries'    => !empty($extMethod['countries']) ? $extMethod['countries'] : array(),
                                'exCountries'  => !empty($extMethod['exCountries']) ? $extMethod['exCountries'] : array(),
                                'orderby'      => !empty($extMethod['orderby']) ? $extMethod['orderby'] : 0,
                            );
                        }

                    } else {
                        $data[$i]['fromMarketplace'] = 1;
                        $data[$i]['moduleEnabled']   = 0;
                    }

                    if (isset($data[$i]['orderby'])) {
                        $data[$i]['adminOrderby'] = $data[$i]['orderby'];
                        unset($data[$i]['orderby']);
                    }

                } else {
                    // Wrong data row, ignore this
                    unset($data[$i]);
                }
            }

            // Save data as temporary yaml file
            $yaml = \Symfony\Component\Yaml\Yaml::dump(array('XLite\\Model\\Payment\\Method' => $data));

            $yamlFile = LC_DIR_TMP . 'pm.yaml';

            \Includes\Utils\FileManager::write(LC_DIR_TMP . 'pm.yaml', $yaml);

            // Update database from yaml file
            \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
        }
    }

    // }}}
}
