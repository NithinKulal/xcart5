<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * FileManager
 *
 */
abstract class FileManager extends \Includes\Utils\AUtils
{
    /**
     * Checks whether a file or directory exists
     *
     * @param string $file File name to check
     *
     * @return boolean
     */
    public static function isExists($file)
    {
        return file_exists($file);
    }

    /**
     * Checks whether a file or directory is readable
     *
     * @param string $file File name to check
     *
     * @return boolean
     */
    public static function isReadable($file)
    {
        return is_readable($file);
    }

    /**
     * Checks whether a file or directory is writeable
     *
     * @param string $file File name to check
     *
     * @return boolean
     */
    public static function isWriteable($file)
    {
        return is_writable($file);
    }

    /**
     * Tells whether the filename is a regular file
     *
     * @param string $file File name to check
     *
     * @return boolean
     */
    public static function isFile($file)
    {
        return is_file($file) || is_link($file);
    }

    /**
     * Tells whether the filename is a directory
     *
     * @param string $file Dir name to check
     *
     * @return boolean
     */
    public static function isDir($file)
    {
        return is_dir($file);
    }

    /**
     * Check if file is readable
     *
     * @param string $file File to check
     *
     * @return boolean
     */
    public static function isFileReadable($file)
    {
        return static::isFile($file) && static::isReadable($file);
    }

    /**
     * Check if dir is readable
     *
     * @param string $file Dir to check
     *
     * @return boolean
     */
    public static function isDirReadable($file)
    {
        return static::isDir($file) && static::isReadable($file);
    }

    /**
     * Check if file is writeable
     *
     * @param string $file File to check
     *
     * @return boolean
     */
    public static function isFileWriteable($file)
    {
        return static::isFile($file) && static::isWriteable($file);
    }

    /**
     * Check if dir is writeable
     *
     * @param string $file Dir to check
     *
     * @return boolean
     */
    public static function isDirWriteable($file)
    {
        return static::isDir($file) && static::isWriteable($file);
    }

    /**
     * Check if file or dir can be created/deleted
     *
     * @param string $path File or path to check
     *
     * @return boolean
     */
    public static function isOperateable($path)
    {
        return static::isDirWriteable(static::getDir($path));
    }

    /**
     * Return directory where a file is located
     *
     * @param string $file File path
     *
     * @return string
     */
    public static function getDir($file)
    {
        return dirname($file);
    }

    /**
     * Return real path
     *
     * @param string $path Path to prepare
     *
     * @return string
     */
    public static function getRealPath($path)
    {
        return realpath($path);
    }

    /**
     * Get command to set permissions
     *
     * @param string    $path               Path
     * @param string    $permissionsStr     Permissions to use in command
     *
     * @return string|null
     */
    public static function getPermissionsCommand($path, $permissionsStr)
    {
        if (!static::isFile($path) && !static::isDir($path)) {
            return null;
        }

        $pattern = 'find %s -type %s -exec chmod %s "{}" \\;;';

        $type = static::isDir($path)
            ? 'd'
            : 'f';

        return sprintf($pattern, $path, $type, $permissionsStr);
    }


    /**
     * Remove anything which isn't a word, whitespace, number
     * or any of the following caracters -_~,;:[]().
     */
    public static function sanitizeFilename($filename)
    {
        $sanitized = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $filename);
        $sanitized = preg_replace("([\.]{2,})", '', $sanitized);

        return $sanitized;
    }

    /**
     * Return relative path by an absolute one
     *
     * @param string $path      Path to convert
     * @param string $compareTo Base part of the path
     *
     * @return string
     */
    public static function getRelativePath($path, $compareTo)
    {
        $path      = static::getRealPath($path);
        $compareTo = static::getCanonicalDir($compareTo);

        return ($path && $compareTo) ? preg_filter('|^' . preg_quote($compareTo, '|') . '|USsi', '', $path) : null;
    }

    /**
     * Prepare file path
     *
     * @param string  $dir   Dir to prepare
     * @param boolean $check Flag OPTIONAL
     *
     * @return string
     */
    public static function getCanonicalDir($dir, $check = true)
    {
        if ($check) {
            $dir = static::getRealPath($dir);
        }

        if (!$check || (!empty($dir) && static::isDir($dir))) {
            $dir = \Includes\Utils\Converter::trimTrailingChars($dir, LC_DS) . LC_DS;
        }

        return $dir ?: null;
    }

    /**
     * Create directory
     *
     * @param string  $dir  Directory path
     * @param integer $mode Permissions OPTIONAL
     *
     * @return boolean
     */
    public static function mkdir($dir, $mode = 0755)
    {
        return static::isOperateable($dir) && !static::isDir($dir)
            ? mkdir($dir, $mode)
            : false;
    }

    /**
     * Create directories tree recursive
     *
     * @param string  $dir  Directory path
     * @param integer $mode Permissions OPTIONAL
     *
     * @return boolean
     */
    public static function mkdirRecursive($dir, $mode = 0755)
    {
        return static::isDir($dir) ?: (
            ($newDir = static::getDir($dir)) != $dir
            && static::mkdirRecursive($newDir, $mode)
            && static::mkdir($dir, $mode)
        );
    }

    /**
     * Remove directories tree recursive
     *
     * @param string $dir Directory path
     *
     * @return void
     */
    public static function unlinkRecursive($dir)
    {
        if (static::isDir($dir)) {
            $filter = new \Includes\Utils\FileFilter($dir, null, \RecursiveIteratorIterator::CHILD_FIRST);

            // :KLUDGE: fix for some stupid FSs
            foreach ($filter->getIterator() as $file) {
                $file->isDir() ? static::deleteDir($file->getRealPath()) : static::deleteFile($file->getRealPath());
            }

            // Unset is required to release directory
            // and avoid 'Permission denied' warning on rmdir() on Windows servers
            unset($filter);

            static::deleteDir($dir, true);
        }
    }

    /**
     * Copy the whole directory tree
     *
     * @param string $dirFrom Catalog from which files will be copied
     * @param string $dirTo   Catalog to which files will be copied
     *
     * @return void
     */
    public static function copyRecursive($dirFrom, $dirTo)
    {
        if (static::isDir($dirFrom)) {
            $dirFrom = static::getCanonicalDir($dirFrom);
            $dirTo   = static::getCanonicalDir($dirTo, false);

            $filter = new \Includes\Utils\FileFilter($dirFrom, null, \RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($filter->getIterator() as $file) {
                $pathFrom = $file->getRealPath();
                $pathTo   = $dirTo . static::getRelativePath($pathFrom, $dirFrom);

                if ($file->isDir()) {
                    static::mkdirRecursive($pathTo);

                } else {
                    static::copy($pathFrom, $pathTo);
                }
            }
        }
    }

    /**
     * Return hash of the file
     *
     * @param string  $path      File path
     * @param integer $skipCheck Flag OPTIONAL
     *
     * @return string
     */
    public static function getHash($path, $skipCheck = false)
    {
        return ($skipCheck || static::isFileReadable($path)) ? md5_file($path) : null;
    }

    /**
     * Get unique file name in the certain directory
     *
     * @param string $dir  Directory name
     * @param string $file File name
     *
     * @return string
     */
    public static function getUniquePath($dir, $file)
    {
        $dir      = static::getCanonicalDir($dir, false);
        $file     = static::sanitizeFilename(
            \Includes\Utils\Converter::convertToTranslit($file)
        );
        $pathinfo = pathinfo($file);
        $counter  = 1;

        while (static::isFile($path = $dir . $file)) {
            $file = $pathinfo['filename'] . '_' . $counter++ . '.' . $pathinfo['extension'];
        }

        return $path;
    }

    /**
     * Change file or directory permissions
     *
     * @param string  $path File path
     * @param integer $mode Permissions
     *
     * @return boolean
     */
    public static function chmod($path, $mode)
    {
        return chmod($path, $mode);
    }

    /**
     * Read data from a file
     *
     * @param string  $path      File path
     * @param integer $skipCheck Flag OPTIONAL
     *
     * @return string
     */
    public static function read($path, $skipCheck = false)
    {
        return ($skipCheck || static::isFileReadable($path)) ? file_get_contents($path) : null;
    }

    /**
     * Write data to a file
     *
     * @param string  $path  File path
     * @param string  $data  Data to write
     * @param integer $flags Some optional flags OPTIONAL
     * @param integer $mode  Permisions to set OPTIONAL
     *
     * @return boolean
     */
    public static function write($path, $data, $flags = 0, $mode = 0644)
    {
        return static::mkdirRecursive(static::getDir($path))
            && (static::isFileWriteable($path) || (!static::isExists($path) && static::isOperateable($path)))
            && false !== file_put_contents($path, $data, $flags);
    }

    /**
     * Replace data to a file by pattern
     *
     * @param string  $path    File path
     * @param string  $data    Data to write
     * @param string  $pattern Pattern to use for replacement
     * @param integer $flags   Some optional flags OPTIONAL
     * @param integer $mode    Permisions to set OPTIONAL
     *
     * @return integer
     */
    public static function replace($path, $data, $pattern, $flags = 0, $mode = 0644)
    {
        return static::write($path, preg_replace($pattern, $data, static::read($path)), $flags, $mode);
    }

    /**
     * Get filepath of the resource handle
     *
     * @param resource  $resource      File resource\handle
     *
     * @return boolean
     */
    public static function getResourcePath($resource)
    {
        $meta_data = stream_get_meta_data($resource);
        $filename = $meta_data["uri"];
        return realpath($filename);
    }

    /**
     * Delete file
     *
     * @param string  $path      File path
     * @param integer $skipCheck Flag OPTIONAL
     *
     * @return boolean
     */
    public static function deleteFile($path, $skipCheck = false)
    {
        return ($skipCheck || (static::isFile($path) && static::isOperateable($path))) ? unlink($path) : false;
    }

    /**
     * Delete dir
     *
     * @param string  $dir       Directory to delete
     * @param integer $skipCheck Flag OPTIONAL
     *
     * @return boolean
     */
    public static function deleteDir($dir, $skipCheck = false)
    {
        return ($skipCheck || (static::isDir($dir) && static::isOperateable($dir))) ? @rmdir($dir) : false;
    }

    /**
     * Copy file
     *
     * @param string  $pathFrom  File path (from)
     * @param string  $pathTo    File path (to)
     * @param boolean $overwrite Flag OPTIONAL
     *
     * @return boolean
     */
    public static function copy($pathFrom, $pathTo, $overwrite = true)
    {
        $result = false;

        if (!$overwrite && static::isFile($pathTo)) {
            $result = true;

        } elseif (static::isFile($pathFrom)) {
            $result = static::mkdirRecursive(static::getDir($pathTo)) && copy($pathFrom, $pathTo);
        }

        return $result;
    }

    /**
     * Move file
     *
     * @param string  $pathFrom  File path (from)
     * @param string  $pathTo    File path (to)
     * @param boolean $overwrite Flag OPTIONAL
     *
     * @return boolean
     */
    public static function move($pathFrom, $pathTo, $overwrite = true)
    {
        $result = false;

        if (!$overwrite && static::isFile($pathTo)) {
            $result = true;

        } elseif (static::isFile($pathFrom)) {
            $result = static::mkdirRecursive(static::getDir($pathTo)) && rename($pathFrom, $pathTo);
        }

        return $result;
    }

    /**
     * Move uploaded file to a new location
     *
     * @param string $key   Index in the $_FILES array
     * @param string $dirTo Destination OPTIONAL
     * @param string $name  Result file name OPTIONAL
     *
     * @return string
     */
    public static function moveUploadedFile($key, $dirTo = LC_DIR_TMP, $name = null, $mode = 0644)
    {
        $path = null;
        if (isset($_FILES[$key]) && UPLOAD_ERR_OK === $_FILES[$key]['error'] && static::isDirWriteable($dirTo)) {
            $path = static::getUniquePath($dirTo, $name ?: $_FILES[$key]['name']);

            if (move_uploaded_file($_FILES[$key]['tmp_name'], $path)) {
                static::chmod($path, $mode);
            } else {
                $path = null;
            }
        }

        return $path;
    }

    /**
     * Move uploaded file from multiple array to a new location
     *
     * @param string  $key      Index in the $_FILES array
     * @param integer $position Position in multiple $_FILES array with key
     * @param string  $dirTo    Destination OPTIONAL
     * @param string  $name     Result file name OPTIONAL
     *
     * @return string
     */
    public static function moveUploadedFileByMultiple($key, $position, $dirTo = LC_DIR_TMP, $name = null, $mode = 0644)
    {
        $path = null;
        if (isset($_FILES[$key])
            && isset($_FILES[$key]['name'][$position])
            && '' != $_FILES[$key]['name'][$position]
            && UPLOAD_ERR_OK === $_FILES[$key]['error'][$position]
            && static::isDirWriteable($dirTo)
        ) {
            $path = static::getUniquePath($dirTo, $name ?: $_FILES[$key]['name'][$position]);

            if (move_uploaded_file($_FILES[$key]['tmp_name'][$position], $path)) {
                static::chmod($path, $mode);
            } else {
                $path = null;
            }
        }

        return $path;
    }

    /**
     * Return file size
     *
     * @param string  $path      File path
     * @param integer $skipCheck Flag OPTIONAL
     *
     * @return integer
     */
    public static function getFileSize($path, $skipCheck = false)
    {
        return ($skipCheck || static::isFile($path)) ? filesize($path) : false;
    }

    /**
     * check if function disabled in php.ini
     *
     * @return boolean
     */
    public static function isFunctionDisabled($functionName)
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));

        return in_array($functionName, $disabledFunctions);
    }

    /**
     * Return available disk space
     *
     * @param string $dir A directory of the filesystem or disk partition OPTIONAL
     *
     * @return float|null
     */
    public static function getDiskFreeSpace($dir = LC_DIR_ROOT)
    {
        $availableSpace = null;

        if (!static::isFunctionDisabled('disk_free_space')) {
             $availableSpace = disk_free_space(!empty($dir) ? $dir : LC_DIR_ROOT);
        }

        return $availableSpace;
    }

    /**
     * Find executable file
     *
     * @param string $filename File name
     *
     * @return string
     */
    public static function findExecutable($filename)
    {
        $result = null;
        $directories = explode(LC_OS_IS_WIN ? ';' : ':', @getenv('PATH'));

        if (LC_OS_IS_WIN) {
            $filename .= '.exe';
        } else {
            array_unshift($directories, '/usr/bin', '/usr/local/bin');
        }

        // The false catalog means the filename will checked from the current directory
        $directories[] = false;

        foreach ($directories as $dir) {
            $file = ($dir ? ($dir . LC_DS) : '') . $filename;

            if (is_executable($file)) {
                $result = $file;
                break;
            }
        }

        return $result;
    }

    /**
     * Return TRUE if the directory is empty
     * Return FALSE if the directory is not empty
     * Return NULL if the directory is not a FS directory
     *
     * @param string $dir
     *
     * @return boolean | null
     */
    public static function isEmptyDir($dir)
    {
        return static::isDirWriteable($dir)
            ? count(scandir($dir)) == 2
            : null;
    }

    /**
     * Check - file is image or not
     *
     * @param string $path File path
     *
     * @return boolean
     */
    public static function isImage($path)
    {
        $result = false;

        if (function_exists('exif_imagetype')) {
            $result = 0 < (int)@exif_imagetype($path);

        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $result = preg_match('/^image\/.*/', finfo_file($finfo, $path));
            finfo_close($finfo);

        } else {
            $data = @getimagesize($path);
            $result = is_array($data) && $data[0];
        }

        return $result;
    }

    /**
     * Return true if file has image extension
     *
     * @param string $path File path
     *
     * @return boolean
     */
    public static function isImageExtension($path)
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), static::getImageExtensions());
    }

    /**
     * Return true if file has sql extension
     *
     * @param string $path File path
     *
     * @return boolean
     */
    public static function isSQLExtension($path)
    {
        return in_array(
            strtolower(pathinfo($path, PATHINFO_EXTENSION)),
            array(
                'sql'
            )
        );
    }

    /**
     * Get list of allowed image extensions
     *
     * @return array
     */
    public static function getImageExtensions()
    {
        return array('gif', 'jpg', 'jpeg', 'png', 'ico');
    }

    /**
     * Check - file is in csv format or not
     *
     * @param string $path File path
     *
     * @return boolean
     */
    public static function isCSV($path)
    {
        $fileInfo = pathinfo($path);

        return 'csv' == strtolower($fileInfo['extension']);
    }

    /**
     * Make relative path for $absToPath resource from $absFromPath resource
     *
     * @param string $absFromPath
     * @param string $absToPath
     *
     * @return string
     */
    public static function makeRelativePath($absFromPath, $absToPath)
    {
        $absFromPath = explode(LC_DS, $absFromPath);
        $absToPath = explode(LC_DS, $absToPath);

        do {
            $from = array_shift($absFromPath);
            $to   = array_shift($absToPath);
        } while ($from === $to && !empty($absFromPath) && !empty($absToPath));

        return str_repeat('..' . LC_DS, count($absFromPath))
            . $to . (!empty($absToPath) ? (LC_DS . implode(LC_DS, $absToPath)) : '');
    }

    /**
     * Native fputcsv is a little broken https://bugs.php.net/bug.php?id=43225
     *
     * @param resource $handle    The file pointer
     * @param array    $fields    An array of values
     * @param string   $delimiter The field delimiter (one character only) OPTIONAL
     * @param string   $enclosure The field enclosure (one character only) OPTIONAL
     * @param string   $escape    The escape character (one character only) OPTIONAL
     *
     * @return integer
     */
    public static function fputcsv($handle, array $fields, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        $regexp = sprintf('/(?:%s|%s|\s)/S', $delimiter_esc, $enclosure_esc);

        foreach ($fields as $field) {
            $output[] = preg_match($regexp, $field) ? (
                $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure
            ) : $field;
        }

        return fwrite($handle, implode($delimiter, $output) . PHP_EOL);
    }

    /**
     * Because we are using own fputcsv https://bugs.php.net/bug.php?id=43225
     *
     * @param array  $fields    An array of values
     * @param string $enclosure The field enclosure (one character only) OPTIONAL
     *
     * @return array
     */
    public static function unescapeCsv(array $fields, $enclosure = '"')
    {
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        $regexp = sprintf('/(?:%s{2}|\s)/S', $enclosure_esc);

        foreach ($fields as $field) {
            $output[] = preg_match($regexp, $field) ? (
                rtrim(str_replace($enclosure . $enclosure, $enclosure, $field), $enclosure)
            ) : $field;
        }

        return $output;
    }
}
