<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Processor;

/**
 * Abstract import processor
 */
abstract class AProcessor extends \XLite\Base implements \SeekableIterator, \Countable
{

    const MODE_VERIFICATION = 'verification';
    const MODE_IMPORT       = 'import';

    const COLUMN_NAME            = 'name';
    const COLUMN_HEADER_DETECTOR = 'headerDetector';
    const COLUMN_VERIFICATOR     = 'verificator';
    const COLUMN_NORMALIZATOR    = 'normalizator';
    const COLUMN_IMPORTER        = 'importer';
    const COLUMN_IS_KEY          = 'isKey';
    const COLUMN_IS_REQUIRED     = 'isRequired';
    const COLUMN_IS_MULTIPLE     = 'isMultiple';
    const COLUMN_IS_MULTIROW     = 'isMultirow';
    const COLUMN_IS_MULTICOLUMN  = 'isMulticolumn';
    const COLUMN_IS_MULTILINGUAL = 'isMultilingual';
    const COLUMN_IS_TAGS_ALLOWED = 'isTagsAllowed';
    const COLUMN_IS_TRUSTED      = 'isTrusted';
    const COLUMN_PROPERTY        = 'property';
    const COLUMN_LENGTH          = 'fieldLength';

    const MESSAGE_NOTE     = 'note';
    const MESSAGE_DECISION = 'decision';

    const SUBVALUE_DELIMITER = '&&';
    const NULL_VALUE = 'NULL';

    const DEFAULT_CHARSET = 'UTF-8';

    const MAX_PART_FILE_ROWS = 300;

    /**
     * Importer (cache)
     *
     * @var \XLite\Logic\Import\Importer
     */
    protected $importer;

    /**
     * Position
     *
     * @var integer
     */
    protected $position = 0;

    /**
     * Count (cached)
     *
     * @var array
     */
    protected $countCache;

    /**
     * File object (cached)
     *
     * @var \SplFileObject
     */
    protected $file;

    /**
     * Columns (cached)
     *
     * @var array
     */
    protected $columns;

    /**
     * Current mode
     *
     * @var string
     */
    protected $mode = self::MODE_VERIFICATION;

    /**
     * Row start index
     *
     * @var integer
     */
    protected $rowStartIndex = 0;

    /**
     * Subrows of row
     *
     * @var array
     */
    protected $rows = array();

    /**
     * Categories (cached)
     *
     * @var array
     */
    protected $categoriesCache = array();

    /**
     * Current row data cache
     *
     * @var array
     */
    protected $currentRowData = array();

    /**
     * Models local cache
     *
     * @var array
     */
    protected $modelsLocalCache = array();

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
    }

    /**
     * Constructor
     *
     * @param \XLite\Logic\Import\Importer $importer Importer
     */
    public function __construct(\XLite\Logic\Import\Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * Get import file name format
     *
     * @return string
     */
    public function getFileNameFormat()
    {
        $parts = explode('\\', get_called_class());

        return strtolower(array_pop($parts)) . '.csv';
    }

    /**
     * Check valid state of processor
     *
     * @return boolean
     */
    public function isValid()
    {
        return is_object($this->getFile());
    }

    /**
     * Check valid state of processor
     *
     * @return boolean
     */
    public function isEof()
    {
        return !$this->isValid() || $this->file->eof();
    }

    /**
     * Get available entity keys
     *
     * @return array
     */
    public function getAvailableEntityKeys()
    {
        $result = array();

        foreach ($this->getKeyColumns() as $column) {
            if (!empty($column[static::COLUMN_NAME])) {
                $result[] = $column[static::COLUMN_NAME];
            }
        }

        return $result;
    }

    /**
     * Get category by path
     *
     * @param mixed   $path     Path
     * @param boolean $useCache Use cache to get data
     *
     * @return \XLite\Model\Category
     */
    protected function getCategoryByPath($path, $useCache = true)
    {
        $result = null;

        if (!is_array($path)) {
            $path = $path ? array_map('trim', explode('>>>', $path)) : array();
        }

        if ($useCache && isset($this->categoriesCache[implode('/', $path)])) {
            $result = $this->categoriesCache[implode('/', $path)];
        }

        if (!$result) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Category')->findOneByPath($path);

            if ($useCache) {
                $this->categoriesCache[implode('/', $path)] = $result;
            }
        }

        return $result;
    }

    /**
     * Add category by path
     *
     * @param mixed $path Path
     *
     * @return \XLite\Model\Category
     */
    protected function addCategoryByPath($path)
    {
        if (!is_array($path)) {
            $path = array_map('trim', explode('>>>', $path));
        }

        $category = $this->getCategoryByPath($path);

        if (!$category) {
            $category = new \XLite\Model\Category();
            $this->categoriesCache[implode('/', $path)] = $category;
            $category->setName(array_pop($path));
            $category->setParent($this->addCategoryByPath($path));
            \XLite\Core\Database::getRepo('XLite\Model\Category')->insert($category);
        }

        return $category;
    }

    // {{{ Files

    /**
     * Get files
     *
     * @param boolean $getObjects Get objects false
     *
     * @return array
     */
    public function getFiles($getObjects = false)
    {
        $result = array();

        foreach ($this->importer->getCSVList() as $file) {
            if ($this->isImportedFile($file)) {
                $result[] = $getObjects ? $file : $file->getPathname();
            }
        }

        return $result;
    }

    /**
     * Check - specified file is imported by this processor or not
     *
     * @param \SplFileInfo $file File
     *
     * @return boolean
     */
    protected function isImportedFile(\SplFileInfo $file)
    {
        $parts = explode('\\', get_called_class());

        return 0 === strpos($file->getFilename(), strtolower(array_pop($parts)));
    }

    // }}}

    // {{{ Files operation

    /**
     * Get current file object
     *
     * @return \SplFileObject
     */
    protected function getFile()
    {
        if (null === $this->file
            || (is_object($this->file) && $this->file->eof())
            || (is_object($this->file) && $this->file->key() == $this->countCache[$this->file->getPathname()])
        ) {
            $path = null;
            $found = false;
            foreach ($this->getFiles() as $p) {
                if (null === $this->file || $found) {
                    $path = $p;
                    break;
                }
                if (null !== $this->file && $p == $this->file->getPathname()) {
                    $found = true;
                }
            }

            if ($path) {
                $this->file = $this->getRawFile($path);

            } elseif (null === $this->file) {
                $this->file = false;
            }
        }

        return $this->file;
    }

    /**
     * Get raw file object
     *
     * @param string $path File path
     *
     * @return \SplFileObject
     */
    protected function getRawFile($path)
    {
        $sfo = new \SplFileObject($path, 'rb');
        $sfo->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
        $sfo->setCsvControl($this->importer->getOptions()->delimiter, $this->importer->getOptions()->enclosure);

        return $sfo;
    }

    /**
     * Get file relative pPath
     *
     * @return string
     */
    protected function getFileRelativePath()
    {
        $dir = \Includes\Utils\FileManager::getRealPath(LC_DIR_VAR . $this->importer->getOptions()->dir);

        return substr($this->file->getPathname(), strlen($dir) + 1);
    }

    /**
     * Reset files pointer
     *
     * @return void
     */
    protected function resetPointer()
    {
        $this->file = null;
    }

    /**
     * Move files pointer
     *
     * @param integer $position Position
     *
     * @return boolean
     */
    protected function movePointer($position)
    {
        $result = true;

        while (0 !== (int) $position) {
            $file = $this->getFile();
            $key = $file->key();
            if (1 === (int) $position) {
                $file->next();

            } else {
                $file->seek($key + $position);

            }
            if (0 < $key && $key === $file->key()) {
                $result = false;
                break;
            }
            $position -= $file->key() - $key;
        }

        return $result;
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     *
     * @return void
     */
    public function seek($position)
    {
        if ($this->position != $position && $position < $this->count()) {
            if ($position > $this->position) {
                $this->movePointer($position - $this->position);

            } else {
                $this->resetPointer();
                $this->movePointer($position);
            }

            $this->position = $position;
        }
    }

    /**
     * \SeekableIterator::current
     *
     * @return \XLite\Logic\Import\Processor\AProcessor
     */
    public function current()
    {
        return $this;
    }

    /**
     * \SeekableIterator::key
     *
     * @return integer
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * \SeekableIterator::next
     *
     * @return void
     */
    public function next()
    {
        $this->position++;
        $this->importer->getOptions()->position = $this->importer->getOptions()->position + 1;
        $this->movePointer(1);
    }

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * \SeekableIterator::valid
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->getFile()
            && !$this->getFile()->eof()
            && (
                $this->isVerification()
                || !$this->importer->hasErrors()
            );
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        return array_sum($this->getCounts());
    }

    // }}}

    // {{{ Data helpers operation

    /**
     * Get files counts
     *
     * @return array
     */
    public function getCounts()
    {
        if (null === $this->countCache) {
            $this->countCache = array();
            foreach ($this->getFiles() as $path) {
                $sfo = $this->getRawFile($path);
                $this->countCache[$path] = 0;
                foreach ($sfo as $row) {
                    $this->countCache[$path]++;
                }
            }
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Row processing

    /**
     * Process current row
     *
     * @param string $mode Mode
     *
     * @return boolean
     */
    public function processCurrentRow($mode)
    {
        $this->mode = $mode;

        $result = false;

        if ($this->isRowProcessingAllowed()) {
            if (0 === $this->file->key()) {
                $this->initialize();
            }

            $rawRows = $this->collectRawRows();
            $this->rowStartIndex = key($rawRows);
            if ($this->isHeaderRow($rawRows)) {
                $result = $this->processHeaderRow($rawRows);
                \XLite\Core\TmpVars::getInstance()->importHeadersSize = count(current($rawRows));
            } else {
                $data = $this->assembleColumnsData($rawRows);

                if (\XLite\Core\TmpVars::getInstance()->importHeadersSize !== count(current($rawRows))) {
                    $this->addWarning('CMN-INVALID-ROW-LENGTH');
                }

                if (!empty($data)) {
                    $this->currentRowData = $data;

                    if ($this->isVerification()) {
                        $result = $this->verifyData($data);

                    } else {
                        $result = $this->importData($data);
                    }

                } else {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Check - row processing is allowed or not
     *
     * @return boolean
     */
    protected function isRowProcessingAllowed()
    {
        return $this->valid()
            && (
                $this->isVerification()
                || $this->importer->isImportAllowed()
            );
    }

    /**
     * Initialize processor
     *
     * @return void
     */
    protected function initialize()
    {
        foreach ($this->getColumns() as $column) {
            $this->setColumnMetaData($column, 'headers', null);
        }
    }

    // }}}

    // {{{ Rows collecting

    /**
     * Get CSV string from array of fields
     *
     * @param array $fields Fields
     *
     * @return string
     */
    protected function getCSVString($fields)
    {
        $data = array();
        $delimiter = $this->importer->getOptions()->delimiter;
        $enclosure = $this->importer->getOptions()->enclosure;

        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        foreach ($fields as $field) {
            $data[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)
                ? $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure
                : $field;
        }

        return implode($delimiter, $data) . PHP_EOL;
    }

    /**
     * Divide large CSV file into several parts and return list of part files
     *
     * @param \SplFileObject $file File object
     *
     * @return array
     */
    public function divideCSVFile($file)
    {
        $result = array();

        $outputFileFormat = basename($file->getRealPath());

        $f = fopen($file->getRealPath(), 'rb');

        // Part file index
        $fi = 1;

        // Row index
        $i = 0;
        $ip = 0;

        // Delta (sum of rows in all previous part files)
        $delta = 0;

        $content = '';
        $pendingContent = '';
        $columns = $this->getKeyColumns();

        // Subdirectory of tmp dir to write CSV part files
        $subDir = time();

        $header = null;

        while (true) {

            $fields = fgetcsv($f, 0, $this->importer->getOptions()->delimiter, $this->importer->getOptions()->enclosure);

            if (false !== $fields && 1 === count($fields) && null === $fields[0]) {
                // Skip blank lines
                continue;
            }

            if (null === $header) {
                // Initialize header
                $this->processHeaderRow(array($fields));
                $header = $this->getCSVString($fields);
                continue;
            }

            $save = false;

            if (false !== $fields) {
                // Get current row content
                $currentRow = $this->getCSVString($fields);

                $isSubrow = true;
                if (!empty($prevFields)) {
                    // Check if next line is subrow of current
                    $isEmpty = true;
                    foreach ($columns as $column) {
                        $prevKey = $this->getColumnValue($column, $prevFields);
                        $isEmpty = $isEmpty && !(bool) $prevKey;
                        if (!$isEmpty && $prevKey !== $this->getColumnValue($column, $fields)) {
                            $isSubrow = false;
                            break;
                        }
                    }

                    $isSubrow = $isEmpty ? false : $isSubrow;

                } else {
                    $isSubrow = false;
                }

                if (!$isSubrow) {
                    $prevFields = $fields;
                }

                // Prepare content
                if ($isSubrow) {
                    // Current row is a subrow or one of previous rows - add this to $pendingContent
                    $pendingContent .= $currentRow;
                    $ip ++;

                } else {
                    // Current row is a complete row - merge $pendingContent into $content...
                    $content .= $pendingContent;
                    $i += $ip;

                    // ...then initialize $pendingContent with current row value
                    $pendingContent = $currentRow;
                    $ip = 1;
                }

                if ($content && (strlen($content) >= \XLite\Logic\Import\Importer::MAX_FILE_SIZE || $i >= static::MAX_PART_FILE_ROWS)) {
                    $save = true;
                }
            }

            if (false === $fields) {
                $content .= $pendingContent;
                $i += $ip;
            }

            if ($save || false === $fields) {
                $outputFile = LC_DIR_TMP . $subDir . LC_DS
                    . preg_replace('/(\.csv)$/', sprintf('.part-%02d.csv', $fi++), $outputFileFormat);

                if (!\Includes\Utils\FileManager::write($outputFile, $header . $content)) {
                    \XLite\Logger::getInstance()->log('Cannot write file ' . $outputFile);
                    $result = array();
                    break;
                }

                $result[] = array(
                    'file' => $outputFile,
                    'delta' => $delta,
                );

                $delta += $i;
                $i = 0;
                $content = '';
            }

            if (false === $fields) {
                break;
            }

        } // while

        return $result;
    }

    /**
     * Collect raw rows
     *
     * @return array
     */
    protected function collectRawRows()
    {
        $rowFile = $this->getFile();
        $rows = array();

        do {
            $row = $this->getNextRow($rowFile);
            if ($row) {
                $rows[$rowFile->key()] = $row;
            }
            $eof = $rowFile->eof();
            if (!$eof) {
                $rowFile->next();
                $this->position++;
                $this->importer->getOptions()->position = $this->importer->getOptions()->position + 1;
            }

        } while (!$eof && $this->isNextSubrow($rowFile, $rows));

        if (!$eof) {
            $this->rollbackFilePointer($rowFile);
        }

        return $rows;
    }

    /**
     * Get next row
     *
     * @param \SplFileObject $file File
     *
     * @return array
     */
    protected function getNextRow(\SplFileObject $file)
    {
        $row = null;

        do {
            // Reinitialize fileopinter
            $file->seek($file->key());

            $row = $file->current();

            if ($this->isEmptyRow($row)) {
                $row = null;
                $file->next();
                $this->position++;
                $this->importer->getOptions()->position = $this->importer->getOptions()->position + 1;
            }

        } while (!$file->eof() && !$row);

        return $row;
    }

    /**
     * Check - row is empty or not
     *
     * @param array $row Row
     *
     * @return boolean
     */
    protected function isEmptyRow(array $row)
    {
        return !is_array($row) || (1 === count($row) && !(trim($row[0])));
    }

    /**
     * Check - next row is subrow or not
     *
     * @param \SplFileObject $file File
     * @param array          $rows Rows list
     *
     * @return boolean
     */
    protected function isNextSubrow(\SplFileObject $file, array $rows)
    {
        $result = !$file->eof() && !$this->isColumnHeadersEmpty();

        if ($result) {
            $row = $this->getNextRow($file);
            $result = (bool)$row;
            $empty = true;

            if ($result) {
                $first = current($rows);
                foreach ($this->getKeyColumns() as $column) {
                    $empty = $empty && !(bool) $this->getColumnValue($column, $first);
                    if ($this->getColumnValue($column, $first) !== $this->getColumnValue($column, $row)) {
                        $result = false;
                        break;
                    }
                }

                $result = $empty ? false : $result;
            }
        }

        return $result;
    }

    /**
     * Rollback file pointer
     *
     * @param \SplFileObject $file File
     *
     * @return void
     */
    protected function rollbackFilePointer(\SplFileObject $file)
    {
        $file->seek($file->key() - 1);
        $this->position--;
        $this->importer->getOptions()->position = $this->importer->getOptions()->position - 1;
    }

    // }}}

    // {{{ Headers processing

    /**
     * Check - specified row is header row or not
     *
     * @param array $rows Rows
     *
     * @return boolean
     */
    protected function isHeaderRow(array $rows)
    {
        return 1 === count($rows) && $this->isColumnHeadersEmpty();
    }

    /**
     * Process header row
     *
     * @param array $rows Rows
     *
     * @return boolean
     */
    protected function processHeaderRow(array $rows)
    {
        $result = false;

        $row = array_shift($rows);

        // Make trim() for each value or row
        array_walk($row, function (&$value, $key) {
            $value = trim($value);
        });

        foreach ($this->getColumns() as $column) {
            $ids = $this->detectColumnHeader($column, $row);
            if ($ids) {
                $headers = array();
                foreach ($ids as $id) {
                    $headers[$this->normalizeHeaderName($row[$id], $column)] = $id;
                }
                $this->setColumnMetaData($column, 'headers', $headers);
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Normalize header name
     *
     * @param string $name   Raw header name
     * @param array  $column Column info
     *
     * @return string
     */
    protected function normalizeHeaderName($name, array $column)
    {
        return preg_replace('/\s+\d+\s*$/S', '', $name);
    }

    // }}}

    // {{{ Assemble row data

    /**
     * Assemble columns data chunks
     *
     * @param array $rows Rows
     *
     * @return array
     */
    protected function assembleColumnsData(array $rows)
    {
        $data = array();
        foreach ($this->getColumns() as $column) {
            $value = $this->assembleColumnData($column, $rows);
            if (null !== $value) {
                if (!empty($value) && !is_numeric($value)) {
                    $value = $this->getPurifiedValue($column, $value);
                    if (!$this->isVerification()) {
                        $value = $this->checkValueLength($column, $value, true);
                    }
                }

                $data[$column[static::COLUMN_NAME]] = $value;
            }
        }

        return $data;
    }

    /**
     * Assemble column data chunk
     *
     * @param array $column Column info
     * @param array $rows   Rows
     *
     * @return array
     */
    protected function assembleColumnData(array $column, array $rows)
    {
        $headers = $this->getColumnMetaData($column, 'headers');

        $result = isset($headers[$column[static::COLUMN_NAME]]) ? array() : null;

        foreach ($rows as $index => $row) {
            foreach ($this->getColumnValue($column, $row) as $name => $value) {
                if ($this->isColumnMulticolumn($column) || $this->isColumnMultilingual($column)) {
                    if (!isset($result[$name])) {
                        $result[$name] = array();
                    }
                    $cell =& $result[$name];

                } else {
                    $cell =& $result;
                }

                if ($this->isColumnMultirow($column)) {
                    if ($this->isColumnMultiple($column)) {
                        $cell[$index] = $value;

                    } else {
                        $cell[$index] = is_array($value) ? implode('', $value) : $value;
                    }

                } elseif ($this->isColumnMultiple($column)) {
                    $cell = array_merge($cell, $value);

                } else {
                    $cell = $value;
                }
            }
        }

        if (empty($result) && is_array($result) && !$this->isColumnMultiple($column)) {
            $result = '';
        }

        return $result;
    }

    // }}}

    // {{{ Verification

    /**
     * Check - verification step is failed or not
     *
     * @return boolean
     */
    public function isVerificationFailed()
    {
        return $this->importer->hasWarnings() || $this->importer->hasErrors();
    }

    /**
     * Check - current mode is verification or not
     *
     * @return boolean
     */
    protected function isVerification()
    {
        return static::MODE_VERIFICATION === $this->mode;
    }

    /**
     * Verify data chunk
     *
     * @param array $data Data chunk
     *
     * @return boolean
     */
    protected function verifyData(array $data)
    {
        $this->setMetaData('count', ((int) $this->getMetaData('count')) + 1);

        foreach ($this->getColumns() as $column) {
            $this->verifyCell(
                $column,
                isset($data[$column[static::COLUMN_NAME]]) ? $data[$column[static::COLUMN_NAME]] : null
            );
        }

        return $this->isVerification() || !$this->importer->hasErrors();
    }

    /**
     * Verify cell
     *
     * @param array $column Column info
     * @param mixed $value  Value
     *
     * @return void
     */
    protected function verifyCell(array $column, $value)
    {
        $headers = $this->getColumnMetaData($column, 'headers');

        if ($this->isColumnRequired($column) && $this->isColumnValueEmpty($column, $value)) {
            $this->addError('CMN-REQ', array('column' => key($headers)));

        } else {
            if (!empty($column[static::COLUMN_VERIFICATOR])) {
                $verificator = $column[static::COLUMN_VERIFICATOR];
                if (!is_callable($verificator)) {
                    $verificator = array($this, $verificator);
                }
                call_user_func($verificator, $value, $column);
            }

            if (!$this->checkValueLength($column, $value)) {
                $this->addWarning('CMN-FIELD-LENGTH', array('column' => $column, 'value' => ''));
            }
        }
    }

    /**
     * Check value length
     *
     * @param string $value value
     * @param int $limit Maximum length
     *
     * @return boolean
     */
    protected function isValueLengthValid($value, $limit)
    {
        return function_exists('mb_strlen')
            ? ($limit >= mb_strlen($value))
            : ($limit >= strlen($value));
    }

    /**
     * Get value sanitized by the limit
     *
     * @param string $value value
     * @param int $limit Maximum length
     *
     * @return string
     */
    protected function getSanitizedValue($value, $limit)
    {
        return function_exists('mb_substr')
            ? mb_substr($value, 0, $limit)
            : substr($value, 0, $limit);
    }

    /**
     * Check value length and return true if length is correct
     *
     * @param array   $column Column info
     * @param mixed   $value  Value
     * @param boolean $sanitize Sanitize flag
     *
     * @return boolean
     */
    protected function checkValueLength(array $column, $value, $sanitize = false)
    {
        $result = true;

        $limit = (int) $this->getColumnLengthLimit($column);

        if (0 < $limit) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (!is_array($v)) {
                        if (!$this->isValueLengthValid($v, $limit)) {
                            $result = false;
                            if ($sanitize) {
                                $value[$k] = $this->getSanitizedValue($v, $limit);

                            } else {
                                break;
                            }
                        }
                    } else {
                        foreach ($v as $kk => $vv) {
                            if (!$this->isValueLengthValid($vv, $limit)) {
                                $result = false;
                                if ($sanitize) {
                                    $value[$k][$kk] = $this->getSanitizedValue($vv, $limit);
                                } else {
                                    break;
                                }
                            }
                        }

                        if (!$result) {
                            break;
                        }
                    }
                }
            } else {
                $result = $this->isValueLengthValid($value, $limit);
                if (!$result && $sanitize) {
                    $value = $this->getSanitizedValue($value, $limit);
                }
            }
        }

        return $sanitize ? $value : $result;
    }

    /**
     * Get purified value
     *
     * @param array $column Column info
     * @param mixed $value  Value
     *
     * @return mixed
     */
    protected function getPurifiedValue(array $column, $value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->getPurifiedValue($column, $v);
            }

        } else {

            $ignoreWarning = false;

            $orig = $value;

            if (!$this->isColumnTagsAllowed($column)) {
                $value = strip_tags($value);
                $wrnType = 'CMN-TAGS';

            } elseif (!$this->isColumnTrusted($column)) {
                $value = \XLite\Core\HTMLPurifier::purify($value);
                $wrnType = 'CMN-XSS';
                $ignoreWarning = $this->isIgnoreXSSWarnings($column);
            }

            if ($orig != $value && !$ignoreWarning) {
                $this->addWarning($wrnType, array('column' => $column, 'value' => ''));
            }
        }

        return $value;
    }

    /**
     * Return true if XSS warnings should be ignored
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isIgnoreXSSWarnings($column = array())
    {
        return true;
    }

    /**
     * Check - column value is empty or not
     *
     * @param array $column Column info
     * @param mixed $value  Value
     *
     * @return boolean
     */
    protected function isColumnValueEmpty(array $column, $value)
    {
        return 0 === strlen(
            trim(
                is_array($value) ? $this->summarizeCell($value) : $value
            )
        );
    }

    /**
     * Summarize row cell
     *
     * @param array $value Row cell value
     *
     * @return string
     */
    protected function summarizeCell(array $value)
    {
        $result = '';
        foreach ($value as $v) {
            $result .= is_array($v) ? $this->summarizeCell($v) : $v;
        }

        return $result;
    }

    // }}}

    // {{{ Import

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    abstract protected function getRepository();

    /**
     * Mark all images as processed
     *
     * @return void
     */
    public function markAllImagesAsProcessed()
    {
    }

    /**
     * Return true if import run in update-only mode
     *
     * @return boolean
     */
    protected function isUpdateMode()
    {
        return (bool) $this->importer->getOptions()->updateOnly;
    }

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        $model = $this->detectModel($data);

        if ($model) {
            $this->setMetaData('updateCount', ((int) $this->getMetaData('updateCount')) + 1);
        }

        if (!$model && !$this->isUpdateMode()) {
            // Create model
            $model = $this->createModel($data);
            if (null !== $model) {
                $this->setMetaData('addCount', ((int) $this->getMetaData('addCount')) + 1);
                \XLite\Core\Database::getEM()->persist($model);
            }
        }

        if ($model) {
            $result = $this->updateModel($model, $data);
            if ($result) {
                try {
                    \XLite\Core\Database::getEM()->flush();
                } catch (\Exception $e) {
                    \XLite\Logger::getInstance()->registerException($e);
                    $result = false;
                }
            }

        } else {
            // Model does not exist - do not create this, just skip
            $this->importer->getOptions()->warningsAccepted = true;
            $this->setMetaData('failedCount', ((int) $this->getMetaData('failedCount')) + 1);
            $this->addNoEntityFoundMessage($this->getModelKeyFieldValues($data));
            $result = true;
        }

        return $result;
    }

    /**
     * Add warning 'No entity found' to the log
     *
     * @return void
     */
    protected function addNoEntityFoundMessage($data = array())
    {
        $keys = array();

        if ($data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $_keys = array();
                    foreach ($value as $k => $v) {
                        $_keys[] = sprintf("%s: '%s'", $k, $v);
                    }
                    $value = '[' . implode(', ', $_keys) . ']';

                } else {
                    $value = "'" . $value . "'";
                }

                $keys[] = sprintf("%s: %s", $key, $value);
            }
        }

        if ($keys) {
            $this->addWarning('NO-ENTITY-FOUND', array('keys' => implode('; ', $keys)));
        }
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        $conditions = $this->assembleModelConditions($data);

        return $conditions ? $this->getRepository()->findOneByImportConditions($conditions) : null;
    }

    /**
     * Get list of key fields values
     *
     * @param array $data Array of current row's data
     *
     * @return array
     */
    protected function getModelKeyFieldValues(array $data)
    {
        $result = array();
        foreach ($this->getKeyColumns() as $column) {
            if (isset($data[$column[static::COLUMN_NAME]])) {
                $result[$this->getModelPropertyName($column)] = $data[$column[static::COLUMN_NAME]];
            }
        }

        return $result;
    }

    /**
     * Assemble maodel conditions
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function assembleModelConditions(array $data)
    {
        $conditions = array();
        foreach ($this->getKeyColumns() as $column) {
            if (isset($data[$column[static::COLUMN_NAME]])
                && !$this->isEmptyValue($data[$column[static::COLUMN_NAME]])
            ) {
                $conditions[$this->getModelPropertyName($column)] = $this->normalizeModelPlainProperty(
                    $data[$column[static::COLUMN_NAME]],
                    $column
                );
            }
        }

        return $conditions;
    }

    /**
     * Return true if value is empty
     * Wrapper for empty() because this function returns true on string '0'
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function isEmptyValue($value)
    {
        return is_string($value) ? 0 === strlen($value) : empty($value);
    }

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function createModel(array $data)
    {
        return $this->getRepository()->insert(null, false);
    }

    /**
     * Update model
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModel(\XLite\Model\AEntity $model, array $data)
    {
        return $this->updateModelFields($model, $data) && $this->updateModelAssociations($model, $data);
    }

    /**
     * Update model fields
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModelFields(\XLite\Model\AEntity $model, array $data)
    {
        $this->getRepository()->update($model, $this->assembleModelFields($data), false);

        foreach ($this->getColumns() as $name => $column) {
            if (isset($data[$name])
                && $this->isModelPlainProperty($data[$name], $column)
                && !empty($column[static::COLUMN_IMPORTER])
            ) {
                call_user_func(array($this, $column[static::COLUMN_IMPORTER]), $model, $data[$name], $column);
            }
        }

        return true;
    }

    /**
     * Assemble model fields
     *
     * @param array $data Row set Data
     *
     * @return array
     */
    protected function assembleModelFields(array $data)
    {
        $result = array();

        foreach ($this->getColumns() as $name => $column) {
            if (isset($data[$name])
                && $this->isModelPlainProperty($data[$name], $column)
                && empty($column[static::COLUMN_IMPORTER])
            ) {
                $result[$this->getModelPropertyName($column)]
                    = $this->normalizeModelPlainProperty($data[$name], $column);
            }
        }

        return $result;
    }

    /**
     * Check - value is model plain property or not
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isModelPlainProperty($value, array $column)
    {
        return is_scalar($value);
    }

    /**
     * Get model property name by column info
     *
     * @param array $column Column info
     *
     * @return string
     */
    protected function getModelPropertyName(array $column)
    {
        return empty($column[static::COLUMN_PROPERTY])
            ? $column[static::COLUMN_NAME]
            : $column[static::COLUMN_PROPERTY];
    }

    /**
     * Normalize model plain property value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return mixed
     */
    protected function normalizeModelPlainProperty($value, array $column)
    {
        return empty($column[static::COLUMN_NORMALIZATOR])
            ? $value
            : call_user_func(array($this, $column[static::COLUMN_NORMALIZATOR]), $value);
    }

    /**
     * Update model associations
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModelAssociations(\XLite\Model\AEntity $model, array $data)
    {
        $result = true;

        foreach ($this->getColumns() as $name => $column) {
            if (isset($data[$name]) && !$this->isModelPlainProperty($data[$name], $column)) {
                if (!empty($column[static::COLUMN_IMPORTER])) {
                    $subresult = call_user_func(
                        array($this, $column[static::COLUMN_IMPORTER]),
                        $model,
                        $data[$name],
                        $column
                    );

                    if (false === $subresult) {
                        $result = false;
                    }

                } elseif ($this->isColumnMultilingual($column)) {
                    $value = $this->normalizeModelPlainProperty($data[$name], $column);
                    if (is_object($value)) {
                        $this->getRepository()->update(
                            $model,
                            array($this->getModelPropertyName($column) => $this->updateModelTranslations($value, $data[$name])),
                            false
                        );

                    } else {
                        $this->updateModelTranslations($model, $data[$name], $this->getModelPropertyName($column));
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Update model translations
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $value Value
     * @param string               $name  Name OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    protected function updateModelTranslations(\XLite\Model\AEntity $model, array $value, $name = 'name')
    {
        $repo = \XLite\Core\Database::getRepo(get_class($model))->getTranslationRepository();
        foreach ($value as $code => $val) {
            $translation = $model->getTranslation($code);
            if ($translation || !$translation->getLabelId()) {
                $translation = $model->getTranslation($code);
                $repo->insert($translation, false);
                $model->addTranslations($translation);
            }
            $repo->update($translation, array($name => $val), false);
        }

        return $model;
    }

    /**
     * Update cleanURL
     *
     * @param \XLite\Model\AEntity $model Model
     * @param  string              $value Value
     *
     * @return void
     */
    protected function updateCleanURL(\XLite\Model\AEntity $model, $value)
    {
        if (!\XLite\Core\Converter::isEmptyString($value)) {
            $validator = new \XLite\Core\Validator\String\CleanURL(
                false,
                null,
                get_class($model),
                $model->getId()
            );

            try {
                $validator->validate($value);
                $model->setCleanURL($value);

            } catch (\XLite\Core\Validator\Exception $exception) {
            }

        } else {
            $model->setCleanURL(null);
        }
    }

    // }}}

    // {{{ Verify and import helpers

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return array(
            'CMN-REQ'                  => '',
            'CMN-INVALID-ROW-LENGTH'   => 'The number of values in the row does not match the number of column headers for this CSV file',
            'CMN-TAGS'                 => 'Tags are not allowed in the column "{{column}}" and will be stripped',
            'CMN-XSS'                  => 'The value in the column "{{column}}" will be adjusted according to HTML specification to prevent potential XSS attack',
            'CMN-FIELD-LENGTH'         => 'The length of value for the column "{{column}}" exceeds length of field and will be cut',
            'GLOBAL-PRODUCT-FMT'       => 'The product with "{{value}}" SKU does not exist',
            'GLOBAL-MEMBERSHIP-FMT'    => 'The "{{value}}" membership does not exist',
            'GLOBAL-PRODUCT-CLASS-FMT' => 'The "{{value}}" product class does not exist',
            'GLOBAL-TAX-CLASS-FMT'     => 'The "{{value}}" tax class does not exist',
            'GLOBAL-IMAGE-FMT'         => 'The "{{value}}" image does not exist',
            'GLOBAL-CATEGORY-FMT'      => 'The "{{value}}" category does not exist',
            'NO-ENTITY-FOUND'          => 'Item not found ({{keys}})',
        );
    }

    /**
     * Get error texts
     *
     * @return array
     */
    public static function getErrorTexts()
    {
        return array(
            'GLOBAL-PRODUCT-FMT'        => 'New product will be created',
            'GLOBAL-MEMBERSHIP-FMT'     => 'New membership will be created',
            'GLOBAL-PRODUCT-CLASS-FMT'  => 'New product class will be created',
            'GLOBAL-TAX-CLASS-FMT'      => 'New tax class will be created',
            'GLOBAL-CATEGORY-FMT'       => 'New category will be created',
        );
    }

    /**
     * Add warning
     *
     * @param string  $code      Message code
     * @param array   $arguments Message arguments OPTIONAL
     * @param integer $rowOffset Row offset OPTIONAL
     * @param array   $column    Column info OPTIONAL
     * @param mixed   $value     Value OPTINAL
     *
     * @return boolean
     */
    protected function addWarning(
        $code,
        array $arguments = array(),
        $rowOffset = 0,
        array $column = array(),
        $value = null
    ) {
        if ($column) {
            $arguments['column'] = $column;
        }

        if (null !== $value) {
            $arguments['value'] = $value;
        }

        $this->importer->getOptions()->warningsCount += 1;

        return $this->addLog(\XLite\Model\ImportLog::TYPE_WARNING, $code, $arguments, $rowOffset);
    }

    /**
     * Add error
     *
     * @param string  $code      Message code
     * @param array   $arguments Message arguments OPTIONAL
     * @param integer $rowOffset Row offset OPTIONAL
     * @param array   $column    Column info OPTIONAL
     * @param mixed   $value     Value OPTINAL
     *
     * @return boolean
     */
    protected function addError(
        $code,
        array $arguments = array(),
        $rowOffset = 0,
        array $column = array(),
        $value = null
    ) {
        if ($column) {
            $arguments['column'] = $column;
        }

        if (null !== $value) {
            $arguments['value'] = $value;
        }

        $this->importer->getOptions()->errorsCount += 1;

        return $this->addLog(\XLite\Model\ImportLog::TYPE_ERROR, $code, $arguments, $rowOffset);
    }

    /**
     * Add log message
     *
     * @param string  $type      Message type
     * @param string  $code      Message code
     * @param array   $arguments Message arguments OPTIONAL
     * @param integer $rowOffset Row offset OPTIONAL
     *
     * @return boolean
     */
    protected function addLog($type, $code, array $arguments = array(), $rowOffset = 0)
    {
        $messages = static::getMessages();

        if (empty($messages[$code])) {
            throw new \InvalidArgumentException('Log code is unknown');

        } else {
            if (!empty($arguments['column'])) {
                $arguments['header'] = $this->getColumnHeadersAsString($arguments['column']);
                $arguments['column'] = $arguments['column'][static::COLUMN_NAME];
            }

            if (isset($arguments['value'])) {
                // Check and convert value to UTF8 to avoid fatal error when the value will be inserted to the DB
                $arguments['value'] = (bool) preg_match('//u', $arguments['value'])
                    ? $arguments['value']
                    : utf8_encode($arguments['value']);
            }

            if ($rowOffset <= 0) {
                $rowOffset = 1;
            }

            $file = basename($this->getFileRelativePath());
            $startIndex = $this->rowStartIndex;
            $deltaFiles = !empty($this->importer->getOptions()->deltaFiles)
                ? $this->importer->getOptions()->deltaFiles[$file]
                : array();

            if (!empty($deltaFiles)) {
                $startIndex += $deltaFiles['delta'];
                $file = $deltaFiles['file'];
            }

            $log = new \XLite\Model\ImportLog;
            $log->setType($type);
            $log->setCode($code);
            $log->setArguments($arguments);
            $log->setFile($file);
            $log->setRow($startIndex + $rowOffset);
            $log->setProcessor(get_called_class());

            \XLite\Core\Database::getEM()->persist($log);
            \XLite\Core\Database::getEM()->flush($log);

            \XLite\Core\Database::getEM()->detach($log);

            $result = true;
        }

        return $result;
    }

    // }}}

    // {{{ Verification helpers

    /**
     * Verify value as empty
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsEmpty($value)
    {
        return 0 === strlen(
            trim(
                is_array($value) ? $this->summarizeCell($value) : $value
            )
        );
    }

    /**
     * Verify value as null value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsNull($value)
    {
        return is_array($value) && count($value) == 1 ? static::NULL_VALUE === $value[0] : static::NULL_VALUE === $value;
    }

    /**
     * Verify value as email
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsEmail($value)
    {
        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Verify value as unsigned integer
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsUinteger($value)
    {
        return !(bool) preg_match('/\D/S', trim($value));
    }

    /**
     * Verify value as date
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsDate($value)
    {
        return false !== @strtotime($value);
    }

    /**
     * Verify value as set
     *
     * @param mixed $value Value
     * @param array $set   Set
     *
     * @return boolean
     */
    protected function verifyValueAsSet($value, array $set)
    {
        return in_array($value, $set);
    }

    /**
     * Verify value as URL
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsURL($value)
    {
        return (bool)filter_var($value, FILTER_VALIDATE_URL);
    }

    /**
     * Verify value as local URL
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsLocalURL($value)
    {
        $isSameHost = in_array(parse_url($value, PHP_URL_HOST), \XLite\Core\URLManager::getShopDomains(), true);
        $path = $this->getLocalPathFromURL($value);
        $isReadable = \Includes\Utils\FileManager::isFileReadable(LC_DIR_ROOT . $path);
        return $this->verifyValueAsURL($value) && $isSameHost && $isReadable;
    }

    /**
     * Verify value as language code
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsLanguageCode($value)
    {
        return preg_match('/^[a-z]{2}$/Si', $value)
            && 0 < \XLite\Core\Database::getRepo('XLite\Model\Language')->countBy(array('code' => $value));
    }

    /**
     * Verify value as country code
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsCountryCode($value)
    {
        return preg_match('/^[a-z]{2}$/Si', $value)
            && 0 < \XLite\Core\Database::getRepo('XLite\Model\Country')->countBy(array('code' => $value));
    }

    /**
     * Verify value as state id
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsStateId($value)
    {
        return preg_match('/^\d+$/Si', $value)
            && 0 < \XLite\Core\Database::getRepo('XLite\Model\State')->countBy(array('state_id' => $value));
    }

    /**
     * Verify value as boolean
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsBoolean($value)
    {
        return (bool)preg_match('/^(0|1|yes|no|y|n)$/iS', $value);
    }

    /**
     * Verify value as membership
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsMembership($value)
    {
        $value = $this->getDefLangValue($value);

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Membership')->findOneByName($value, false, true);
    }

    /**
     * Verify value as product class
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsProductClass($value)
    {
        $value = $this->getDefLangValue($value);

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\ProductClass')->findOneByName($value, true);
    }

    /**
     * Verify value as tax class
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsTaxClass($value)
    {
        $value = $this->getDefLangValue($value);

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->findOneByName($value, true);
    }

    /**
     * Verify value as product
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsProduct($value)
    {
        return null !== \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBySku($value);
    }

    /**
     * Verify value as float
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsFloat($value)
    {
        return (bool)preg_match('/^[+-]?\d+\.?\d*$/', $value);
    }

    /**
     * Verify value as file
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsFile($value)
    {
        // Do not verify files in verification mode and if 'ignoreFileChecking' option is true
        if (!$this->isVerification() || !$this->importer->getOptions()->ignoreFileChecking) {
            if ($this->verifyValueAsURL($value)) {
                $request = new \XLite\Core\HTTP\Request($value);
                $response = $request->sendRequest();
                $result = ($response && 200 == $response->code);

            } else {
                $result = \Includes\Utils\FileManager::isReadable(LC_DIR_ROOT . $value);
            }

        } else {
            $result = true;
        }

        return $result;
    }

    // }}}

    /**
     * Returns local part of path from local URL
     *
     * @return string
     */
    public function getLocalPathFromURL($path)
    {
        $webdir = \XLite::getInstance()->getOptions(array('host_details', 'web_dir'));
        $webdir = $webdir ? $webdir . '/' : '';

        return ltrim(parse_url($path, PHP_URL_PATH), '/' . $webdir);
    }

    /**
     * Returns image comparing closure
     *
     * @param string $name  Image name
     *
     * @return \Closure
     */
    protected function getImageFilter($name)
    {
        return function ($element) use ($name) {
            $filename = $element->getFileName() ?: basename($element->getPath());
            return $filename === basename($name);
        };
    }

    /**
     * Returns image rejecting closure (Rejects any images in DB which are not present in parameter array)
     *
     * @param array $images  Array of imported images
     *
     * @return \Closure
     */
    protected function getImageRejectFilter($images)
    {
        return function ($element) use ($images) {
            $filename = $element->getFileName() ?: basename($element->getPath());
            return !in_array($filename, array_map(function ($image) {
                return basename($image);
            }, $images), true);
        };
    }

    // {{{ Normalizators

    /**
     * Normalize value as email
     *
     * @param mixed @value Value
     *
     * @return string
     */
    protected function normalizeValueAsEmail($value)
    {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Normalize value as date
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeValueAsDate($value)
    {
        return strtotime($value);
    }

    /**
     * Normalize value as URL
     *
     * @param mixed @value Value
     *
     * @return string
     */
    protected function normalizeValueAsURL($value)
    {
        return filter_var($value, FILTER_SANITIZE_URL);
    }

    /**
     * Normalize value as boolean
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeValueAsBoolean($value)
    {
        return (bool) preg_match('/^1|yes|y$/iS', $value);
    }

    /**
     * Normalize value as string
     *
     * @param mixed @value Value
     *
     * @return string
     */
    protected function normalizeValueAsString($value)
    {
        return (string) $value;
    }

    /**
     * Normalize value as membership
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\Membership
     */
    protected function normalizeValueAsMembership($value)
    {
        $result = null;
        $value = $this->getDefLangValue($value);

        if ($value) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findOneByName($value, false);
            $key = 'XLite\Model\Membership';
            if (!$result) {
                if (!isset($this->modelsLocalCache[$key]) || !isset($this->modelsLocalCache[$key][$value])) {
                    $result = \XLite\Core\Database::getRepo('\XLite\Model\Membership')->insert(
                        array('name' => $value),
                        false
                    );
                    if (!isset($this->modelsLocalCache[$key])) {
                        $this->modelsLocalCache[$key] = array();
                    }
                    $this->modelsLocalCache[$key][$value] = $result;

                    $this->importer->getOptions()->commonData['calculateAllQuickData'] = true;

                } else {
                    $result = $this->modelsLocalCache[$key][$value];

                    $result = \XLite\Core\Database::getEM()->merge($result);
                }
            }
        }

        return $result;
    }

    /**
     * Normalize value as product class
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\ProductClass
     */
    protected function normalizeValueAsProductClass($value)
    {
        $result = null;
        $value = $this->getDefLangValue($value);

        if ($value) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')->findOneByName($value);
            if (!$result) {
                $result = \XLite\Core\Database::getRepo('\XLite\Model\ProductClass')->insert(
                    array('name' => $value)
                );
            }
        }

        return $result;
    }

    /**
     * Normalize value as attribute group
     *
     * @param mixed                     $value        Value
     * @param \XLite\Model\ProductClass $productClass Product class
     *
     * @return \XLite\Model\AttributeGroup
     */
    protected function normalizeValueAsAttributeGroup($value, $productClass)
    {
        $result = null;
        $value = $this->getDefLangValue($value);

        if ($value) {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\AttributeGroup::SEARCH_PRODUCT_CLASS} = $productClass;
            $cnd->{\XLite\Model\Repo\AttributeGroup::SEARCH_NAME} = $value;
            $result = \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->search($cnd);
            if (!$result) {
                $result = \XLite\Core\Database::getRepo('\XLite\Model\AttributeGroup')->insert(
                    array(
                        'name'         => $value,
                        'productClass' => $productClass,
                    )
                );

            } else {
                $result = reset($result);
            }
        }

        return $result;
    }

    /**
     * Normalize value as tax class
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\TaxClass
     */
    protected function normalizeValueAsTaxClass($value)
    {
        $result = null;
        $value = $this->getDefLangValue($value);

        if ($value) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->findOneByName($value);
            if (!$result) {
                $result = \XLite\Core\Database::getRepo('\XLite\Model\TaxClass')->insert(
                    array('name' => $value),
                    false
                );
            }
        }

        return $result;
    }

    /**
     * Normalize value as product
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\Product
     */
    protected function normalizeValueAsProduct($value)
    {
        $result = null;
        if ($value) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBySku($value);
            $processorAllowedToAdd = $this instanceof XLite\Logic\Import\Processor\Products;

            if (!$result && $processorAllowedToAdd) {
                $result = \XLite\Core\Database::getRepo('\XLite\Model\Product')->insert(
                    array('sku' => $value)
                );
            }
        }

        return $result;
    }

    /**
     * Normalize value as float
     *
     * @param mixed @value Value
     *
     * @return float
     */
    protected function normalizeValueAsFloat($value)
    {
        return (float) $value;
    }

    /**
     * Normalize value as unsigned integer
     *
     * @param mixed @value Value
     *
     * @return float
     */
    protected function normalizeValueAsUinteger($value)
    {
        return abs((int) $value);
    }

    /**
     * Normalize value as state
     *
     * @param mixed @value Value
     *
     * @return string|\XLite\Model\State
     */
    protected function normalizeValueAsState($value)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByState($value) ?: $value;
    }

    // }}}

    // {{{ Submodels routines

    /**
     * Assemble submodels dData
     *
     * @param array $data   Data
     * @param array $column Column info
     *
     * @return array
     */
    protected function assembleSubmodelsData(array $data, array $column)
    {
        $items = array();

        foreach ($data as $name => $rows) {
            foreach ($rows as $i => $row) {
                $items[$i][$name] = $row;
            }
        }

        return $items;
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    abstract protected function defineColumns();

    /**
     * Get columns
     *
     * @return array
     */
    protected function getColumns()
    {
        if (null === $this->columns) {
            $this->columns = $this->defineColumns();
            $this->processColumns();
        }

        return $this->columns;
    }

    /**
     * Get column
     *
     * @param string $name Column name
     *
     * @return array
     */
    protected function getColumn($name)
    {
        $list = $this->getColumns();

        return isset($list[$name]) ? $list[$name] : null;
    }

    /**
     * Process columns
     *
     * @return void
     */
    protected function processColumns()
    {
        $result = array();
        $keyFound = false;
        foreach ($this->columns as $name => $column) {
            $column = $this->processColumn($name, $column);
            if (!empty($column[static::COLUMN_IS_KEY])) {
                $keyFound = true;
            }

            $result[$column[static::COLUMN_NAME]] = $column;
        }
        reset($result);

        if (!$keyFound) {
            $result[key($result)][static::COLUMN_IS_KEY] = true;

        }

        $this->columns = $result;
    }

    /**
     * Process column
     *
     * @param string $name   Column name
     * @param array  $column Column raw info
     *
     * @return array
     */
    protected function processColumn($name, array $column)
    {
        $column[static::COLUMN_NAME] = $name;

        $column[static::COLUMN_VERIFICATOR] = $this->prepareColumnHandler($column, static::COLUMN_VERIFICATOR, 'verify{name}');
        $column[static::COLUMN_HEADER_DETECTOR] = $this->prepareColumnHandler($column, static::COLUMN_HEADER_DETECTOR, 'detect{name}Header');
        $column[static::COLUMN_NORMALIZATOR] = $this->prepareColumnHandler($column, static::COLUMN_NORMALIZATOR, 'normalize{name}Value');
        $column[static::COLUMN_IMPORTER] = $this->prepareColumnHandler($column, static::COLUMN_IMPORTER, 'import{name}Column');

        return $column;
    }

    /**
     * Prepare column handler
     *
     * @param array  $column  Column info
     * @param string $name    Column cell name
     * @param string $pattern Handler name pattern
     *
     * @return mixed
     */
    protected function prepareColumnHandler(array $column, $name, $pattern)
    {
        $result = null;

        if (empty($column[$name])) {
            $uname = \XLite\Core\Converter::convertToCamelCase($column[static::COLUMN_NAME]);
            $method = str_replace('{name}', $uname, $pattern);
            if (method_exists($this, $method)) {
                $result = $method;
            }

        } elseif (true === $column[$name]) {
            $uname = \XLite\Core\Converter::convertToCamelCase($column[static::COLUMN_NAME]);
            $result = str_replace('{name}', $uname, $pattern);

        } elseif (is_callable($column[$name])) {
            $result = $column[$name];
        }

        return $result;
    }

    /**
     * Get columns-keys
     *
     * @return array
     */
    protected function getKeyColumns()
    {
        $result = array();
        foreach ($this->getColumns() as $column) {
            if (!empty($column[static::COLUMN_IS_KEY])) {
                $result[] = $column;
            }
        }

        return $result;
    }

    /**
     * Detect column header
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectColumnHeader(array $column, array $row)
    {
        $result = empty($column[static::COLUMN_HEADER_DETECTOR])
            ? $this->detectHeaderByPrefix($column[static::COLUMN_NAME], $row, $this->isColumnMultilingual($column))
            : call_user_func(array($this, $column[static::COLUMN_HEADER_DETECTOR]), $column, $row);

        if (!$this->isColumnMultiple($column)
            && !$this->isColumnMulticolumn($column)
            && !$this->isColumnMultilingual($column)
        ) {
            $result = array_slice($result, 0, 1);
        }

        return $result;
    }

    /**
     * Detect column header by prefix
     *
     * @param string  $prefix               Column header prefix
     * @param array   $row                  Row
     * @param boolean $isColumnMultilingual Is column multilingual or not OPTIONAL
     *
     * @return array
     */
    protected function detectHeaderByPrefix($prefix, array $row, $isColumnMultilingual = false)
    {
        return $this->detectHeaderByPattern(preg_quote($prefix, '/'), $row, $isColumnMultilingual);
    }

    /**
     * Detect column header by pattern
     *
     * @param string  $pattern              Reg.exp. pattern
     * @param array   $row                  Row
     * @param boolean $isColumnMultilingual Is column multilingual or not OPTIONAL
     *
     * @return array
     */
    protected function detectHeaderByPattern($pattern, array $row, $isColumnMultilingual = false)
    {
        $result = array();

        foreach ($row as $i => $head) {
            if ($this->isColumnHeaderEqual($pattern, $head, $isColumnMultilingual)) {
                $result[] = $i;
            }
        }

        return $result;
    }

    /**
     * Check - column header is equal with pattern or not
     *
     * @param string  $pattern              Reg.exp. pattern
     * @param string  $cell                 Header cell
     * @param boolean $isColumnMultilingual Is column multilingual or not OPTIONAL
     *
     * @return boolean
     */
    protected function isColumnHeaderEqual($pattern, $cell, $isColumnMultilingual = false)
    {
        return $isColumnMultilingual
            ? preg_match('/^' . $pattern . '(_[a-z]{2})?$/iSs', $cell)
            : preg_match('/^' . $pattern . '$/iSs', $cell);
    }

    /**
     * Normalize column header
     *
     * @param string $pattern Normalization reg.exp. pattern
     * @param string $cell    Header cell
     *
     * @return string
     */
    protected function normalizeColumnHeader($pattern, $cell)
    {
        return preg_match('/^' . $pattern . '(\s+\d+\s*)?$/iSs', $cell, $match) ? $match[1] : null;
    }

    /**
     * Check - column is required or not
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isColumnRequired(array $column)
    {
        return isset($column[static::COLUMN_IS_REQUIRED])
            ? $this->resultColumnProperty($column[static::COLUMN_IS_REQUIRED])
            : false;
    }

    /**
     * Return true if tags are allowed in the column content
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isColumnTagsAllowed(array $column)
    {
        return $this->isColumnTrusted($column)
            || (
                isset($column[static::COLUMN_IS_TAGS_ALLOWED])
                ? $this->resultColumnProperty($column[static::COLUMN_IS_TAGS_ALLOWED])
                : false
            );
    }

    /**
     * Return true if column content may contain anything (including '<script>' tags)
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isColumnTrusted(array $column)
    {
        return isset($column[static::COLUMN_IS_TRUSTED])
            ? $this->resultColumnProperty($column[static::COLUMN_IS_TRUSTED])
            : false;
    }

    /**
     * Check - column has multiple values or not
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isColumnMultiple(array $column)
    {
        return isset($column[static::COLUMN_IS_MULTIPLE])
            ? $this->resultColumnProperty($column[static::COLUMN_IS_MULTIPLE])
            : false;
    }

    /**
     * Check - column is multirow or not
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isColumnMultirow(array $column)
    {
        return isset($column[static::COLUMN_IS_MULTIROW])
            ? $this->resultColumnProperty($column[static::COLUMN_IS_MULTIROW])
            : false;
    }

    /**
     * Check - column is multicolumn or not
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isColumnMulticolumn(array $column)
    {
        return isset($column[static::COLUMN_IS_MULTICOLUMN])
            ? $this->resultColumnProperty($column[static::COLUMN_IS_MULTICOLUMN])
            : false;
    }

    /**
     * Check - column is multilingual or not
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isColumnMultilingual(array $column)
    {
        return isset($column[static::COLUMN_IS_MULTILINGUAL])
            ? $this->resultColumnProperty($column[static::COLUMN_IS_MULTILINGUAL])
            : false;
    }

    /**
     * Result column property
     *
     * @param mixed $property Column information cell property
     *
     * @return mixed
     */
    protected function resultColumnProperty($property)
    {
        if (is_callable($property)) {
            $property = $property();
        }

        return $property;
    }

    /**
     * Return column length limit (if specified) or false
     *
     * @param array $column Column info
     *
     * @return boolean|integer
     */
    protected function getColumnLengthLimit(array $column)
    {
        return isset($column[static::COLUMN_LENGTH])
            ? $this->resultColumnProperty($column[static::COLUMN_LENGTH])
            : false;
    }

    /**
     * Get column value
     *
     * @param array $column Column info
     * @param array $row    Row
     *
     * @return mixed
     */
    protected function getColumnValue(array $column, array $row)
    {
        $result = array();

        $headers = $this->getColumnMetaData($column, 'headers');
        if ($headers) {
            foreach ($headers as $name => $idx) {
                if (isset($row[$idx])) {
                    $value = trim($row[$idx]);
                    if (0 < strlen($value)) {
                        if ($this->isColumnMultilingual($column)
                            && preg_match('/^' . $column['name'] . '(_([a-z]{2}))?$/iSs', $name, $m)
                        ) {
                            $name = isset($m[2]) ? $m[2] : $this->importer->getLanguageCode();
                        }

                        $result[$name] = $value;
                    }
                }

                if (!$this->isColumnMultiple($column)
                    && !$this->isColumnMulticolumn($column)
                    && !$this->isColumnMultilingual($column)
                ) {
                    break;
                }
            }

            if ($this->isColumnMultiple($column)) {
                foreach ($result as $name => $value) {
                    $result[$name] = explode(static::SUBVALUE_DELIMITER, $value);
                }
                if ($this->isColumnMultilingual($column)) {
                    $_result = array();
                    foreach ($result as $code => $value) {
                        foreach ($value as $id => $val) {
                            if (!isset($_result[$id])) {
                                $_result[$id] = array();
                            }
                            $_result[$id][$code] = $val;
                        }
                    }
                    $result = $_result;
                }
            }
        }

        return $result;
    }

    // }}}

    // {{{ Column metadata

    /**
     * Get import meta data cell
     *
     * @param string $name Cell name_
     *
     * @return mixed
     */
    protected function getMetaData($name)
    {
        $metaData = $this->importer->getOptions()->columnsMetaData;
        $class = get_called_class();

        return ($metaData && !empty($metaData[$class]) && !empty($metaData[$class][$name]))
            ? $metaData[$class][$name]
            : null;
    }

    /**
     * Set import meta data
     *
     * @param string $name  Cell name
     * @param mixed  $value Value
     *
     * @return void
     */
    protected function setMetaData($name, $value)
    {
        $metaData = $this->importer->getOptions()->columnsMetaData;
        $class = get_called_class();

        if (!isset($metaData[$class])) {
            $metaData[$class] = array();
        }

        $metaData[$class][$name] = $value;

        $this->importer->getOptions()->columnsMetaData = $metaData;
    }

    /**
     * Get import meta data cell by column
     *
     * @param array  $column Column info
     * @param string $name   Cell name
     *
     * @return mixed
     */
    protected function getColumnMetaData(array $column, $name)
    {
        $metaData = $this->getMetaData('columns');

        return ($metaData
            && !empty($metaData[$column[static::COLUMN_NAME]])
            && !empty($metaData[$column[static::COLUMN_NAME]][$name])
        )
            ? $metaData[$column[static::COLUMN_NAME]][$name]
            : null;
    }

    /**
     * Set import meta data cell by column
     *
     * @param array  $column Column info
     * @param string $name   Cell name
     * @param mixed  $value  Value
     *
     * @return void
     */
    protected function setColumnMetaData(array $column, $name, $value)
    {
        $metaData = $this->getMetaData('columns');

        if (!isset($metaData[$column[static::COLUMN_NAME]])) {
            $metaData[$column[static::COLUMN_NAME]] = array();
        }

        $metaData[$column[static::COLUMN_NAME]][$name] = $value;

        $this->setMetaData('columns', $metaData);
    }

    /**
     * Get import meta data cell by column
     *
     * @param array  $column Column info
     *
     * @return mixed
     */
    protected function getColumnHeadersAsString(array $column)
    {
        $headers = $this->getColumnMetaData($column, 'headers');

        return $headers ? implode(', ', array_keys($headers)) : null;
    }

    /**
     * Check - columns headers list is empty or not
     *
     * @return boolean
     */
    protected function isColumnHeadersEmpty()
    {
        $result = true;

        foreach ($this->getColumns() as $column) {
            if ($this->getColumnMetaData($column, 'headers')) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Get value for default language
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function getDefLangValue($value)
    {
        $code = $this->importer->getLanguageCode();

        return is_array($value)
            ? (isset($value[$code]) ? $value[$code] : null)
            : trim($value);
    }

    // }}}

    // {{{ Result messages

    /**
     * Get result message
     *
     * @param array $cell Data cell
     *
     * @return array
     */
    public static function getResultMessage(array $cell)
    {
        $result = null;

        $text = static::getTitle() . ': ';
        $count = 0;
        $comment = array();

        if (!empty($cell['addCount'])) {
            $count += $cell['addCount'];
            $comment[] = static::t('{{count}} created', array('count' => $cell['addCount']));
        }

        if (!empty($cell['updateCount'])) {
            $count += $cell['updateCount'];
            $comment[] = static::t('{{count}} updated', array('count' => $cell['updateCount']));
        }

        if (!empty($cell['count'])) {
            $count += $cell['count'];
        }

        if ($count) {
            $result = array(
                'text'    => $text . $count,
                'comment' => $comment ? '(' . implode(', ', $comment) . ')' : '',
            );

        } elseif (!empty($cell['failedCount'])) {
            $result = array(
                'text'    => static::getTitle() . ': 0',
                'comment' => '',
            );
        }

        return $result;
    }

    // }}}
}
