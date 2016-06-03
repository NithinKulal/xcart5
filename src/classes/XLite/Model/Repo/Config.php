<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * DB-based configuration registry
 */
class Config extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * List of options which are not allowed
     *
     * @var array
     */
    protected $disabledOptions = array();

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('category', 'name'),
    );

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias    Table alias OPTIONAL
     * @param string  $indexBy The index for the from. OPTIONAL
     * @param string $code     Language code OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        return $this->prepareOptionsAvailabilityCondition(parent::createQueryBuilder($alias, $indexBy, $code));
    }

    /**
     * Get the list of options of the specified category
     *
     * @param string  $category     Category
     * @param boolean $force        Force OPTIONAL
     * @param boolean $doNotProcess Do not process OPTIONAL
     *
     * @return array
     */
    public function getByCategory($category, $force = false, $doNotProcess = false)
    {
        $data = null;

        if (!$force) {
            $data = $this->getFromCache('category', array('category' => $category));
        }

        if (null === $data) {
            $data = $this->findByCategory($category);

            if (!$doNotProcess) {
                $data = $this->processOptions($data);
                $this->saveToCache($data, 'category', array('category' => $category));
            }
        }

        return $data;
    }

    /**
     * Find by category
     *
     * @param string $category Category
     *
     * @return \XLite\Model\Config[]
     */
    public function findByCategory($category)
    {
        return $this->defineFindByCategory($category)->getResult();
    }

    /**
     * Define query builder for findByCategory
     *
     * @param string $category Category
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindByCategory($category)
    {
        return $this->createQueryBuilder()
            ->andWhere('c.category = :category')
            ->setParameter('category', $category)
            ->addOrderBy('c.orderby', 'ASC');
    }

    /**
     * Find all visible settings by category name
     *
     * @param string $category Category name
     *
     * @return array
     */
    public function findByCategoryAndVisible($category)
    {
        $result = $this->getByCategory($category, true, true);

        if ($result) {
            foreach ($result as $k => $v) {
                if (empty($v->type) || !$this->isOptionVisible($v)) {
                    unset($result[$k]);
                }
            }
        }

        return $result ?: array();
    }

    /**
     * Find origin address options
     *
     * @return array
     */
    public function findOriginOptions()
    {
        $result = $this->getByCategory('Company', true, true);

        if ($result) {
            foreach ($result as $k => $v) {
                if (false === strpos($v->getName(), 'origin_')) {
                    unset($result[$k]);
                }
            }
        }

        return $result ?: array();
    }

    /**
     * Return true if option is visible
     *
     * @param \XLite\Model\Config $option Option object
     *
     * @return boolean
     */
    protected function isOptionVisible(\XLite\Model\Config $option)
    {
        $result = true;

        $method = 'isOption' . \XLite\Core\Converter::convertToCamelCase($option->getCategory() . '_' . $option->getName()) . 'Visible';
        if (method_exists($this, $method)) {
            // Call method 'isOption<Category><Name>Visible'
            $result = $this->$method();
        }

        $notOrigin = (false === strpos($option->getName(), 'origin_'));

        return $result && $notOrigin;
    }

    /**
     * Get the list of all options
     *
     * @param boolean $force Do not use cache OPTIONAL
     *
     * @return array
     */
    public function getAllOptions($force = false)
    {
        $data = null;

        if (!$force) {
            $data = $this->getFromCache('all');
        }

        if (null === $data) {
            $data = $this->defineAllOptionsQuery()->getResult();
            $data = $this->detachList($data);
            $data = $this->processOptions($data);
            $this->saveToCache($data, 'all');
        }

        return $data;
    }

    /**
     * Preprocess options and transform its to the hierarchy of \XLite\Core\ConfigCell objects
     *
     * @param array $data Array of options data gathered from the database
     *
     * @return \XLite\Core\ConfigCell
     */
    public function processOptions($data)
    {
        $config = new \XLite\Core\ConfigCell();

        foreach ($data as $option) {
            $category = $option->getCategory();
            $name     = $option->getName();
            $type     = $option->getType();
            $value    = $option->getValue();

            $isModuleConfig = false !== strpos($category, '\\');

            if ($isModuleConfig) {
                // Process module config

                list($author, $module) = explode('\\', $category);

                if (!isset($config->$author)) {
                    $config->$author = new \XLite\Core\ConfigCell();
                }

                if (!isset($config->$author->$module)) {
                    $config->$author->$module = new \XLite\Core\ConfigCell();
                }

            } elseif (!isset($config->$category)) {
                $config->$category = new \XLite\Core\ConfigCell();
            }

            if ('checkbox' === $type) {
                $value = ('Y' == $value || '1' === $value);

            } elseif ('serialized' === $type) {
                $value = unserialize($value);
            }

            if ($this->checkNameAndValue($category, $name, $value)) {
                if ($isModuleConfig) {
                    $config->$author->$module->$name = $value;
                } else {
                    $config->$category->$name = $value;
                }
            }
        }

        // Add human readable default state name for General options
        if (isset($config->General)) {
            $config->General->defaultState = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findById($config->General->default_state, $config->General->default_custom_state);

            // Type cast
            $config->General->minimal_order_amount = (float) $config->General->minimal_order_amount;
            $config->General->maximal_order_amount = (float) $config->General->maximal_order_amount;
        }

        return $config;
    }

    /**
     * Create new option / Update option value
     *
     * @param array $data Option data in the following format
     *
     * @return void
     * @throws \Exception
     */
    public function createOption($data)
    {
        // Array of allowed fields and flag required/optional
        $fields = $this->getAllowedFields();

        $errorFields = array();

        foreach ($fields as $field => $required) {
            if (isset($data[$field])) {
                $fields[$field] = $data[$field];

            } elseif ($required) {
                $errorFields[] = $field;
            }
        }

        if (!empty($errorFields)) {
            throw new \Exception(
                'createOption() failed: The following required fields are missed: '
                . implode(', ', $errorFields)
            );
        }

        if (isset($fields['type']) && !$this->isValidOptionType($fields['type'])) {
            throw new \Exception('createOptions() failed: Wrong option type: ' . $fields['type']);
        }

        $option = $this->findOptionToUpdate($fields);

        // Existing option: unset key fields
        if ($option) {
            $option->setValue($fields['value']);

        } else {
            // Create a new option
            $option = new \XLite\Model\Config();
            $option->map($fields);
            \XLite\Core\Database::getEM()->persist($option);
        }

        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\Config::updateInstance();
    }

    /**
     * Returns allowed fields and flag required/optional
     *
     * @return array
     */
    protected function getAllowedFields()
    {
        return array(
            'category' => 1,
            'name'     => 1,
            'value'    => 1,
            'type'     => 0,
            'orderby'  => 0,
        );
    }

    /**
     * Search option to update
     *
     * @param array $data Data
     *
     * @return \XLite\Model\Config
     */
    protected function findOptionToUpdate($data)
    {
        return $this->findOneBy(array('name' => $data['name'], 'category' => $data['category']));
    }

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all'] = array();
        $list['category'] = array(
            self::ATTRS_CACHE_CELL => array('category')
        );

        return $list;
    }

    /**
     * Remove option from the "black list"
     *
     * @param string $category Option category
     * @param string $name     Option name
     *
     * @return void
     */
    protected function enableOption($category, $name)
    {
        unset($this->disabledOptions[$category][array_search($name, $this->disabledOptions[$category])]);
    }

    /**
     * Add option to the "black list"
     *
     * @param string $category Option category
     * @param string $name     Option name
     *
     * @return void
     */
    protected function disableOption($category, $name)
    {
        if (!isset($this->disabledOptions[$category])) {
            $this->disabledOptions[$category] = array();
        }

        $this->disabledOptions[$category][] = $name;
    }

    /**
     * Return query (and its params) which is used to filter options
     *
     * @return array
     */
    protected function getOptionsAvailabilityCondition()
    {
        $conditions = array();
        $params = array();

        foreach ($this->disabledOptions as $category => $options) {
            $condition = 'c.category = :category' . $category;
            $params['category' . $category] = $category;

            list($keys, $options) = \XLite\Core\Database::prepareArray($options, $category);
            $condition .= ' AND c.name IN (' . implode(',', $keys) . ')';
            $params += $options;

            $conditions[] = 'NOT (' . $condition . ')';
        }

        return array(empty($conditions) ? null : '(' . implode(') AND (', $conditions) . ')', $params);
    }

    /**
     * Add "filter" condition to the query builder
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Current query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareOptionsAvailabilityCondition(\Doctrine\ORM\QueryBuilder $qb)
    {
        list($condition, $params) = $this->getOptionsAvailabilityCondition();

        if (null !== $condition) {
            $qb->andWhere($condition);
            foreach ($params as $name => $value) {
                $qb->setParameter($name, $value);
            }
        }

        return $qb;
    }

    /**
     * Define query builder for getAllOptions()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllOptionsQuery()
    {
        return $this->createQueryBuilder(null, null, \XLite\Base\Superclass::getDefaultLanguage());
    }

    /**
     * Check (and modify) option name and value
     *
     * @param string &$category Option category
     * @param string &$name     Option name
     * @param mixed  &$value    Option value
     *
     * @return boolean
     */
    protected function checkNameAndValue(&$category, &$name, &$value)
    {
        return true;
    }

    /**
     * Check if option type is a valid
     *
     * @param string $optionType Option type
     *
     * @return boolean
     */
    protected function isValidOptionType($optionType)
    {
        $simple = in_array(
            $optionType,
            array(
                '',
                'text',
                'textarea',
                'checkbox',
                'country',
                'state',
                'select',
                'serialized',
                'separator',
                'hidden'
            )
        );

        $optionType = ltrim($optionType, "\\");

        if (!$simple && preg_match('/^XLite\\\(Module\\\.+\\\)?View\\\FormField\\\/S', $optionType)) {
            $simple = true;
        }

        return $simple;
    }
}
