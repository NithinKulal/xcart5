<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Language repository
 */
class Language extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Language import file fields
     */
    const LNG_IMPORT_FIELD_CODE  = 'code';
    const LNG_IMPORT_FIELD_NAME  = 'name';
    const LNG_IMPORT_FIELD_LABEL = 'label';

    /**
     * Limit for processing import rows in batch
     */
    const LNG_IMPORT_BATCH_LIMIT = 1000;

    /**
     * Cache of import result data
     *
     * @var array
     */
    protected static $importData = null;

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
    protected $defaultOrderBy = 'code';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('code'),
    );

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
        if (!isset($code)) {
            $code = \XLite\Base\Superclass::getDefaultLanguage();
        }

        return parent::createQueryBuilder($alias, $indexBy, $code);
    }

    // {{{ defineCacheCells

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();
        $list['all'] = array();
        $list['added'] = array(
            self::ATTRS_CACHE_CELL => array('added'),
        );
        $list['enabled'] = array(
            self::ATTRS_CACHE_CELL => array('enabled'),
        );

        return $list;
    }

    // }}}

    // {{{ findAllLanguages

    /**
     * Find all languages
     *
     * @return array
     */
    public function findAllLanguages()
    {
        $data = $this->getFromCache('all');

        if (!isset($data)) {
            $data = $this->defineAllLanguagesQuery()->getResult();
            $this->saveToCache($data, 'all');
        }

        return $data;
    }

    /**
     * Define query builder for findAllLanguages()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllLanguagesQuery()
    {
        return $this->createQueryBuilder();
    }

    // }}}

    // {{{ findActiveLanguages

    /**
     * Find all active languages
     * NOTE: do not cache this result in a persistent cache
     *
     * @return array
     */
    public function findActiveLanguages()
    {
        return $this->defineByEnabledQuery(true)->getResult();
    }

    /**
     * Find all inactive languages
     *
     * @return array
     */
    public function findInactiveLanguages()
    {
        return $this->defineAddedQuery(false)->getResult();
    }

    /**
     * Define query builder for findActiveLanguages()
     *
     * @param integer $status Status key
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineByEnabledQuery($status)
    {
        return $this->createPureQueryBuilder()
            ->andWhere('l.added = true')
            ->andWhere('l.enabled = :status')
            ->setParameter('status', $status);
    }

    // }}}

    // {{{ findAddedLanguages

    /**
     * Find all added languages
     *
     * @return array
     */
    public function findAddedLanguages()
    {
        return $this->defineAddedQuery(true)->getResult();
    }

    /**
     * Define query builder for findAddedLanguages()
     *
     * @param boolean $status
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAddedQuery($status)
    {
        return $this->createQueryBuilder()
            ->andWhere('l.added = :status')
            ->setParameter('status', $status);
    }

    // }}}

    // {{{ Import language and labels feature

    /**
     * Get allowed import fields
     *
     * @return array
     */
    protected static function getImportLanguageFields()
    {
        return array(
            static::LNG_IMPORT_FIELD_CODE,
            static::LNG_IMPORT_FIELD_NAME,
            static::LNG_IMPORT_FIELD_LABEL,
        );
    }

    /**
     * Parse import CSV file and return array of the parsing result
     *
     * @param string $path Import file path
     *
     * @return array
     */
    public function parseImportFile($path, $forceImport = false)
    {
        set_time_limit(0);

        $startTime = microtime(true);

        if ($forceImport || !isset(static::$importData[$path])) {

            $codes = array();
            $ignored = 0;
            $isUpdated = false;
            $languages = array();
            $rows = array();

            $f = fopen($path, 'r');

            if ($f) {

                $fields = static::getImportLanguageFields();

                while (false !== ($data = fgetcsv($f, 0, ','))) {

                    $row = array();

                    $error = false;

                    // Generate associative array of the row data
                    foreach ($data as $id => $col) {
                        $row[$fields[$id]] = $col;
                    }

                    if (empty($row) || count($row) != count($fields)) {
                        // Increase the counter of ignored rows
                        $error = true;

                    } elseif (2 != strlen($row[static::LNG_IMPORT_FIELD_CODE])) {
                        // Wrong language code format
                        $error = true;

                    } elseif (empty($row[static::LNG_IMPORT_FIELD_NAME])) {
                        // Empty label name
                        $error = true;

                    } else {

                        // Cast language code to the lower case
                        $row[static::LNG_IMPORT_FIELD_CODE] = strtolower($row[static::LNG_IMPORT_FIELD_CODE]);

                        // Check if language with specified code exists

                        if (!isset($languages[$row[static::LNG_IMPORT_FIELD_CODE]])) {

                            $lng = \XLite\Core\Database::getRepo('XLite\Model\Language')
                                ->findOneBy(array('code' => $row[static::LNG_IMPORT_FIELD_CODE]));

                            $languages[$row[static::LNG_IMPORT_FIELD_CODE]] = $lng;

                            if ($forceImport && $lng && !$lng->getAdded()) {
                                // Activate inactive language
                                $lng->setAdded(true);
                            }
                        }
                    }

                    if (!$error && $languages[$row[static::LNG_IMPORT_FIELD_CODE]]) {

                        if (!isset($codes[$row[static::LNG_IMPORT_FIELD_CODE]]['language'])) {
                            $codes[$row[static::LNG_IMPORT_FIELD_CODE]] = array(
                                'language' => $languages[$row[static::LNG_IMPORT_FIELD_CODE]]->getName(),
                                'count'    => 0
                            );
                        }

                        // Count labels by specific language code
                        $codes[$row[static::LNG_IMPORT_FIELD_CODE]]['count'] ++;

                        if ($forceImport) {

                            // Import row data

                            if (static::LNG_IMPORT_BATCH_LIMIT == count($rows)) {
                                $this->updateLanguageLabel($rows);
                                $rows = array();

                            } else {
                                $rows[] = $row;
                            }

                            $isUpdated = true;
                        }

                    } else {
                        // Unknown language code - ignore row
                        $ignored ++;
                    }

                } // while (...

                if (!empty($rows)) {
                    $this->updateLanguageLabel($rows);
                    $isUpdated = true;
                }

                if ($isUpdated) {
                    \XLite\Core\Translation::getInstance()->reset();
                }
            }

            fclose($f);

            $s = microtime(true) - $startTime;
            $si = intval($s);
            $sf = substr((string)$s - $si, 1, 4);

            $elapsed = sprintf("%d:%02d:%02d%s", ($s1=intval($si/3600)), ($s2=intval(($si-$s1*3600)/60)), ($si-$s1*3600-$s2*60), $sf);

            static::$importData[$path] = array(
                'codes'   => $codes,     // Language codes: array('en' => 123, 'zz' => 45, ...)
                'ignored' => $ignored,   // Number of ignored rows
                'elapsed' => $elapsed,
            );
        }

        return static::$importData[$path];
    }

    /**
     * Update label with data from import rows
     *
     * @param array $rows Import data rows
     *
     * @return void
     */
    protected function updateLanguageLabel($rows)
    {
        static $c;

        if (!isset($c)) {
            $c = 0;
        }

        $toDelete = array();
        $toUpdate = array();

        foreach ($rows as $row) {

            $c++;

            $log = array();
            $log['code'] = $row[static::LNG_IMPORT_FIELD_CODE];
            $log['name'] = $row[static::LNG_IMPORT_FIELD_NAME];
            $log['action'] = '';
            $log['counter'] = $c;

            // Find the label
            $label = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')
                ->findOneBy(array('name' => $row[static::LNG_IMPORT_FIELD_NAME]));

            if (!$label && !empty($row[static::LNG_IMPORT_FIELD_LABEL])) {
                $log['action'] = 'create label ';
                $label = new \XLite\Model\LanguageLabel();
                $label->setName($row[static::LNG_IMPORT_FIELD_NAME]);
                \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->insert($label);

            } elseif ($label && empty($row[static::LNG_IMPORT_FIELD_LABEL])) {
                $log['action'] = 'delete label';
                $translation = $label->getTranslation($row[static::LNG_IMPORT_FIELD_CODE]);
                $toDelete[] = $translation;
            }

            if (!empty($row[static::LNG_IMPORT_FIELD_LABEL])) {
                $log['action'] .= 'update label';
                $label->setEditLanguage($row[static::LNG_IMPORT_FIELD_CODE])->setLabel($row[static::LNG_IMPORT_FIELD_LABEL]);
                $toUpdate[] = $label;
            }

            // Uncomment the line below to log all import operations
            // \XLite\Logger::logCustom('lng_import', $log);
        }

        if ($toUpdate) {
            \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->updateInBatch($toUpdate);
        }

        if ($toDelete) {
            \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->deleteInBatch($toDelete);
        }
    }

    // }}}
}
