<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Iconv wrapper
 */
class Iconv extends \XLite\Base\Singleton
{
    const CHUNK_SIZE = 8192;

    /**
     * Charsets
     *
     * @var array
     */
    protected $charsets;

    /**
     * Check - iconv wrapper is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return function_exists('iconv')
            && $this->getCharsets();
    }

    /**
     * Get charsets from config
     *
     * @return array
     */
    protected function getDefinedCharsets()
    {
        return \Includes\Utils\ConfigParser::getOptions(
            array('export-import', 'encodings_list')
        );
    }

    /**
     * Get charsets
     *
     * @return array
     */
    public function getCharsets()
    {
        if (!isset($this->charsets)) {
            $this->charsets = array();
            $exec = @func_find_executable('iconv');

            if (function_exists('iconv') || $exec) {

                $charsets = $this->getDefinedCharsets();

                foreach ($charsets as $name) {
                    $name = rtrim($name, '/');
                    $this->charsets[$name] = str_replace('_', ' ', $name);
                }
            }
        }

        return $this->charsets;
    }

    /**
     * Convert charset
     *
     * @param string $from From charset
     * @param string $to   To charset
     * @param string $text Text
     *
     * @return string
     */
    public function convert($from, $to, $text)
    {
        return function_exists('iconv') ? iconv($from, $to . "//IGNORE", $text) : $text;
    }

    /**
     * Convert charset
     *
     * @param string $from       From charset
     * @param string $to         To charset
     * @param string $path       File path
     * @param string $outputPath Output file path OPTIONAL
     *
     * @return boolean
     */
    public function convertFile($from, $to, $path, $outputPath = null, $forceBuffered = false)
    {
        $result = false;

        $outputPath = $outputPath ?: $path;

        // Suppress E_WARNING about open_basedir
        $exec = @func_find_executable('iconv');
        if ($exec && !$forceBuffered) {
            $result = $this->convertNative($exec, $from, $to, $path, $outputPath);
        } else {
            $result = $this->convertBuffered($from, $to, $path, $outputPath);
        }

        return $result;
    }

    /**
     * Convert charset using native iconv
     *
     * @param string $exec       Exec path
     * @param string $from       From charset
     * @param string $to         To charset
     * @param string $path       File path
     * @param string $outputPath Output file path OPTIONAL
     *
     * @return boolean
     */
    public function convertNative($exec, $from, $to, $path, $outputPath)
    {
        $tmp = $path . '.tmp';
        exec(
            $exec
            . ' -s'
            . ' --from-code=' . $from
            . ' --to-code=' . $to
            . ' ' . escapeshellarg($path)
            . ' > ' . escapeshellarg($tmp)
        );
        exec(
            'mv'
            . ' ' . escapeshellarg($tmp)
            . ' ' . escapeshellarg($outputPath)
        );

        return true;
    }

    /**
     * Convert charset using iconv()
     *
     * @param string $from       From charset
     * @param string $to         To charset
     * @param string $path       File path
     * @param string $outputPath Output file path OPTIONAL
     *
     * @return boolean
     */
    public function convertBuffered($from, $to, $path, $outputPath)
    {
        $chunksize = static::CHUNK_SIZE;
        $in = fopen($path, "rb");
        $out = fopen($outputPath, "wb");

        while (!feof($in)) {
            do {
                $line = fread($in, $chunksize);
                if (!mb_check_encoding($line, $from)) {
                    fseek($in, -$chunksize, SEEK_CUR);
                    $chunksize += 1;
                }
            } while (!mb_check_encoding($line, $from) && $chunksize < (static::CHUNK_SIZE + 100));

            $outputText = @$this->convert($from, $to, $line);
            fwrite($out, $outputText);
        }

        fclose($in);
        fclose($out);

        return true;
    }
}
