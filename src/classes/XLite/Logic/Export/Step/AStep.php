<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export\Step;

/**
 * Abstract export step
 */
abstract class AStep extends \XLite\Logic\ARepoStep
{
    const COLUMN_GETTER    = 'getter';
    const COLUMN_MULTIPLE  = 'multiple';
    const COLUMN_FORMATTER = 'formatter';
    const COLUMN_ID        = 'id';

    const SUBVALUE_DELIMITER = '&&';

    const DEFAULT_CHARSET = 'UTF-8';

    /**
     * File pointer
     *
     * @var   resource
     */
    protected $filePointer;

    /**
     * File path
     *
     * @var   string
     */
    protected $filePath;

    /**
     * Constructor
     *
     * @param \XLite\Logic\Export\Generator $generator Generator
     */
    public function __construct(\XLite\Logic\Export\Generator $generator = null)
    {
        $this->generator = $generator;
        if ($generator) {
            $this->getRepository()->setExportSelection($generator->getOptions()->selection);
            if ($generator->getOptions()->filter) {
                $conditionCell = $generator->getOptions()->filter;
                $this->getRepository()->setExportFilter(\XLite\Core\Session::getInstance()->{$conditionCell});
            }
        }
    }

    /**
     * Stop exporter
     *
     * @return void
     */
    public function stop()
    {
        $this->closeWriter();
    }

    // {{{ SeekableIterator, Countable

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $this->countCache = $this->getRepository()->countForExport();
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Row processing

    /**
     * Run export step
     *
     * @return boolean
     */
    public function run()
    {
        $time = microtime(true);

        $this->generator->setInProgress(true);

        if (0 == $this->position) {
            $this->buildHeader();
        }

        $row = $this->getItems()->current();

        $this->processModel($row[0]);
        $this->generator->setInProgress(false);

        $this->generator->getOptions()->time += round(microtime(true) - $time, 3);

        return true;
    }

    /**
     * Build header
     *
     * @return void
     */
    protected function buildHeader()
    {
        $names = array_keys($this->getColumns());
        $this->write(array_combine($names, $names));
    }

    /**
     * Process model
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        foreach ($this->buildModelRows($model) as $row) {
            $this->write($row);
        }
    }

    /**
     * Build model's rows
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return array
     */
    protected function buildModelRows(\XLite\Model\AEntity $model)
    {
        $rows = array();
        foreach ($this->getModelDatasets($model) as $i => $dataset) {
            $rows[] = $this->buildDatasetRow($dataset, $i);
        }

        return $rows;
    }

    /**
     * Get model datasets
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return array
     */
    protected function getModelDatasets(\XLite\Model\AEntity $model)
    {
        return array(
            array('model' => $model),
        );
    }

    /**
     * Distribute dataset model
     *
     * @param array        $datasets Datasets
     * @param string       $name     Model cell name
     * @param array|object $models   Models collection
     *
     * @return array
     */
    protected function distributeDatasetModel(array $datasets, $name, $models)
    {
        $i = 0;
        foreach ($models as $model) {
            if (!isset($datasets[$i])) {
                $datasets[$i] = array(
                    'model' => $datasets[0]['model'],
                );
            }

            $datasets[$i][$name] = $model;

            $i++;
        }

        return $datasets;
    }

    /**
     * Build dataset row
     *
     * @param array   $dataset Dataset
     * @param integer $i       Row index
     *
     * @return array
     */
    protected function buildDatasetRow(array $dataset, $i)
    {
        return 0 == $i ? $this->buildPrimaryRow($dataset) : $this->buildSecondaryRow($dataset, $i);
    }

    /**
     * Build primary row
     *
     * @param array $dataset Dataset
     *
     * @return array
     */
    protected function buildPrimaryRow(array $dataset)
    {
        $row = array();

        foreach ($this->getColumns() as $name => $info) {
            $row[$name] = $this->{$info[static::COLUMN_GETTER]}($dataset, $name, 0);
            if ($info[static::COLUMN_FORMATTER]) {
                $row[$name] = $this->{$info[static::COLUMN_FORMATTER]}($row[$name], $dataset, $name);

            } else {
                $row[$name] = $this->formatDefault($row[$name], $dataset, $name);
            }
        }

        return $row;
    }

    /**
     * Build secondary row
     *
     * @param array   $dataset Dataset
     * @param integer $i       Index
     *
     * @return array
     */
    protected function buildSecondaryRow(array $dataset, $i)
    {
        $row = array();

        foreach ($this->getColumns() as $name => $info) {
            if ($this->isColumnMultiple($info, $dataset, $name) || $this->isColumnId($info, $dataset, $name)) {
                $row[$name] = $this->{$info[static::COLUMN_GETTER]}($dataset, $name, $i);
                if ($info[static::COLUMN_FORMATTER]) {
                    $row[$name] = $this->{$info[static::COLUMN_FORMATTER]}($row[$name], $dataset, $name);

                } else {
                    $row[$name] = $this->formatDefault($row[$name], $dataset, $name);
                }
            }
        }

        return $row;
    }

    // }}}

    // {{{ Helpers

    /**
     * Get model field value by name
     *
     * @param \XLite\Model\AEntity $model Model
     * @param string               $name  Field name
     *
     * @return mixed
     */
    protected function getColumnValueByName(\XLite\Model\AEntity $model, $name)
    {
        return $model->$name;
    }

    /**
     * Format by default
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatDefault($value, array $dataset, $name)
    {
        switch (gettype($value)) {
            case 'boolean':
                $value = $this->formatBoolean($value);
                break;

            case 'object':
                $value = $this->formatObject($value, $dataset, $name);
                break;

            case 'array':
                $value = $this->formatArray($value, $dataset, $name);
                break;

            default:
        }

        return $value;
    }

    /**
     * Format object
     *
     * @param object $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatObject($value, array $dataset, $name)
    {
        if ($value instanceof \XLite\Model\Membership) {
            $value = $this->formatMembershipModel($value);

        } elseif ($value instanceof \XLite\Model\ProductClass) {
            $value = $this->formatProductClassModel($value);

        } elseif ($value instanceof \XLite\Model\TaxClass) {
            $value = $this->formatTaxClassModel($value);

        } elseif ($value instanceof \XLite\Model\AttributeGroup) {
            $value = $this->formatAttributeGroupModel($value);

        } elseif ($value instanceof \XLite\Model\Base\Image) {
            $value = $this->formatImageModel($value);

        } elseif ($value instanceof \XLite\Model\Base\Storage) {
            $value = $this->formatStorageModel($value);

        } else {
            $value = serialize($value);
        }

        return $value;
    }

    /**
     * Format array
     *
     * @param array  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatArray(array $value, array $dataset, $name)
    {
        return implode('', $value)
            ? implode(static::SUBVALUE_DELIMITER, $value)
            : '';
    }

    /**
     * Format value as timestamp
     *
     * @param integer $date Date
     *
     * @return string
     */
    protected function formatTimestamp($date)
    {
        return $date ? date('r', $date) : '';
    }

    /**
     * Format value as boolean
     *
     * @param boolean $value Value
     *
     * @return string
     */
    protected function formatBoolean($value)
    {
        return $value ? 'Yes' : 'No';
    }

    /**
     * Format membership model
     *
     * @param \XLite\Model\Membership $membership Membership
     *
     * @return string
     */
    protected function formatMembershipModel(\XLite\Model\Membership $membership = null)
    {
        return $membership ? $membership->getName() : '';
    }

    /**
     * Format product class model
     *
     * @param \XLite\Model\ProductClass $class Product class
     *
     * @return string
     */
    protected function formatProductClassModel(\XLite\Model\ProductClass $class = null)
    {
        return $class ? $class->getName() : '';
    }

    /**
     * Format tax class model
     *
     * @param \XLite\Model\TaxClass $class Tax class
     *
     * @return string
     */
    protected function formatTaxClassModel(\XLite\Model\TaxClass $class = null)
    {
        return $class ? $class->getName() : '';
    }

    /**
     * Format attribute group model
     *
     * @param \XLite\Model\AttributeGroup $group Attribute group
     *
     * @return string
     */
    protected function formatAttributeGroupModel(\XLite\Model\AttributeGroup $group = null)
    {
        return $group ? $group->getName() : '';
    }

    /**
     * Format image model
     *
     * @param \XLite\Model\Base\Image $image Image
     *
     * @return string
     */
    protected function formatImageModel(\XLite\Model\Base\Image $image = null)
    {
        return $this->formatStorageModel($image);
    }

    /**
     * Format storage model
     *
     * @param \XLite\Model\Base\Storage $storage       Storage
     * @param boolean                   $copyResources Copy reources flag OPTIONAL
     *
     * @return string
     */
    protected function formatStorageModel(\XLite\Model\Base\Storage $storage = null, $copyResources = null)
    {
        $result = '';
        $copyResources = $copyResources ?: $this->generator->getOptions()->copyResources;

        if ($storage) {

            $result = $copyResources
                ? \Includes\Utils\FileManager::getRelativePath($storage->getStoragePath(), LC_DIR_ROOT)
                : $storage->getFrontURL();
        }

        return $result;
    }

    /**
     * Copy resource
     *
     * @param \XLite\Model\Base\Storage $storage      Storage
     * @param string                    $subdirectory Subdirectory
     *
     * @return boolean
     */
    protected function copyResource(\XLite\Model\Base\Storage $storage, $subdirectory)
    {
        $dir = LC_DIR_VAR . $this->generator->getOptions()->dir . LC_DS . $subdirectory;
        if (!\Includes\Utils\FileManager::isExists($dir)) {
            \Includes\Utils\FileManager::mkdir($dir);
        }

        $name = basename($storage->getPath());

        return \Includes\Utils\FileManager::write($dir . LC_DS . $name, $storage->getBody())
            ? $subdirectory . LC_DS . $name
            : false;
    }

    // }}}

    // {{{ Columns routines

    /**
     * Define columns
     *
     * @return array
     */
    abstract protected function defineColumns();

    /**
     * Get column
     *
     * @param string $name Name
     *
     * @return array
     */
    protected function getColumn($name)
    {
        $columns = $this->getColumns();

        return isset($columns[$name]) ? $columns[$name] : null;
    }

    /**
     * Get columns
     *
     * @return array
     */
    protected function getColumns()
    {
        if (!isset($this->columns)) {
            $this->columns = $this->defineColumns();
            $this->processColumns();
        }

        return $this->columns;
    }

    /**
     * Process columns
     *
     * @return void
     */
    protected function processColumns()
    {
        $i = 0;
        foreach ($this->columns as $name => $column) {
            $uname = ucfirst($name);
            if (empty($column[static::COLUMN_GETTER])) {
                $column[static::COLUMN_GETTER] = 'get' . $uname . 'ColumnValue';
            }

            if (!isset($column[static::COLUMN_MULTIPLE])) {
                $column[static::COLUMN_MULTIPLE] = false;
            }

            if (!isset($column[static::COLUMN_ID])) {
                $column[static::COLUMN_ID] = 0 == $i;
            }

            if (!isset($column[static::COLUMN_FORMATTER])) {
                $method = 'format' . $uname . 'ColumnValue';
                $column[static::COLUMN_FORMATTER] = method_exists($this, $method) ? $method : null;
            }

            if (!method_exists($this, $column['getter'])) {
                throw new \BadMethodCallException(
                    get_called_class() . '::' . $column['getter'] . ' method did not exists'
                );
            }

            $this->columns[$name] = $column;
            $i++;
        }
    }

    /**
     * Check - column is multi-row or not
     *
     * @param array  $info    Column info
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return boolean
     */
    protected function isColumnMultiple(array $info, array $dataset, $name)
    {
        return is_bool($info[static::COLUMN_MULTIPLE])
            ? $info[static::COLUMN_MULTIPLE]
            : false;
    }

    /**
     * Check - column is ID or not
     *
     * @param array  $info    Column info
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return boolean
     */
    protected function isColumnId(array $info, array $dataset, $name)
    {
        return is_bool($info[static::COLUMN_ID])
            ? $info[static::COLUMN_ID]
            : false;
    }

    // }}}

    // {{{ Writer

    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        $parts = explode('\\', get_called_class());
        $name = array_pop($parts);

        return strtolower($name);
    }

    /**
     * Write
     *
     * @param array $row Row
     *
     * @return integer
     */
    protected function write(array $row)
    {
        $result = fputcsv(
            $this->getFilePointer(),
            $this->convertCharset($this->normalizeRow($row)),
            $this->generator->getOptions()->delimiter,
            $this->generator->getOptions()->enclosure
        );

        if (false === $result) {
            $this->generator->addError(
                static::t('Failed write to file'),
                static::t('Failed write to file X. There may not be enough disc-space. Please check if there is enough disc-space.', array('path' => $this->filePath))
            );
        }

        return $result;
    }

    /**
     * Get file pointer
     *
     * @return resource
     */
    protected function getFilePointer()
    {
        if (!isset($this->filePointer)) {
            $name = $this->getFilename();
            if ('.csv' != substr($name, -4)) {
                $name .= '.csv';
            }
            $name = preg_replace('/(\.[^\.]+)$/', '-' . date('Y-m-d') . '$1', $name);

            $dir = \Includes\Utils\FileManager::getRealPath(LC_DIR_VAR . $this->generator->getOptions()->dir);
            if (is_writable($dir)) {
                if (!\Includes\Utils\FileManager::isExists($dir . LC_DS . '.htaccess')) {
                    // Try to create .htaccess file to protect directory
                    $out = <<<OUT
Options -Indexes

Deny from all

OUT;
                    \Includes\Utils\FileManager::write($dir . LC_DS . '.htaccess', $out);
                }
                $this->filePath = $dir . LC_DS . $name;
                $this->filePointer = @fopen($dir . LC_DS . $name, 'ab');

            } else {
                $this->generator->addError(
                    static::t('Directory does not have permissions to write'),
                    static::t('Directory X does not have permissions to write. Please set necessary permissions to directory X.', array('path' => $dir))
                );
            }
        }

        return $this->filePointer;
    }

    /**
     * Normalize row
     *
     * @param array $row Row
     *
     * @return array
     */
    protected function normalizeRow(array $row)
    {
        $result = array();

        foreach (array_keys($this->getColumns()) as $name) {
            $result[] = isset($row[$name]) ? $this->filterCellValue($name, $row[$name]) : '';
        }

        return $result;
    }

    /**
     * Filter cell value to avoid data corruption in spreadsheet
     *
     * @param string $name  Name
     * @param string $value Value
     *
     * @return string
     */
    protected function filterCellValue($name, $value)
    {
        return strpos($value, '=') === 0
            ? '\'' . $value
            : $value;
    }

    /**
     * Convert charset
     *
     * @param array $row Row
     *
     * @return array
     */
    protected function convertCharset(array $row)
    {
        if (static::DEFAULT_CHARSET != $this->generator->getOptions()->charset
            && \XLite\Core\Iconv::getInstance()->isValid()
        ) {
            $iconv = \XLite\Core\Iconv::getInstance();
            foreach ($row as $k => $v) {
                $row[$k] = $iconv->convert(
                    static::DEFAULT_CHARSET,
                    $this->generator->getOptions()->charset,
                    $v
                );
            }
        }

        return $row;
    }

    /**
     * Close writer (file pointer)
     *
     * @return void
     */
    protected function closeWriter()
    {
        if ($this->filePointer) {
            fclose($this->filePointer);
        }
    }

    // }}}

    // {{{ Data

    /**
     * Get items iterator
     *
     * @param boolean $reset Reset iterator OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected function getItems($reset = false)
    {
        if (!isset($this->items) || $reset) {
            $this->items = $this->getRepository()->getExportIterator($this->position, \XLite\Core\EventListener\Export::CHUNK_LENGTH);
            $this->items->rewind();
        }

        return $this->items;
    }

    // }}}
}
