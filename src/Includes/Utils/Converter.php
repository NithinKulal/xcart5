<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * Converter
 *
 * @package    XLite
 */
abstract class Converter extends \Includes\Utils\AUtils
{
    /**
     * Method name translation records
     *
     * @var array
     */
    protected static $to = array(
        'Q', 'W', 'E', 'R', 'T',
        'Y', 'U', 'I', 'O', 'P',
        'A', 'S', 'D', 'F', 'G',
        'H', 'J', 'K', 'L', 'Z',
        'X', 'C', 'V', 'B', 'N',
        'M',
    );

    /**
     * Method name translation patterns
     *
     * @var array
     */
    protected static $from = array(
        '_q', '_w', '_e', '_r', '_t',
        '_y', '_u', '_i', '_o', '_p',
        '_a', '_s', '_d', '_f', '_g',
        '_h', '_j', '_k', '_l', '_z',
        '_x', '_c', '_v', '_b', '_n',
        '_m',
    );

    /**
     * Translite map
     *
     * @var array
     */
    protected static $translitMap = array(
        '!'     => '161',
        '"'     => '1066,1098,8220,8221,8222',
        "'"     => '1068,1100,8217,8218',
        '\'\''  => '147,148',
        '(R)'   => '174',
        '(TM)'  => '153,8482',
        '(c)'   => '169',
        '+-'    => '177',
        '-'     => '47,92,172,173,8211', # Replace spaces/slashes by the $subst_symbol('-' by default)
        '.'     => '183',
        '...'   => '8230',
        '0/00'  => '8240',
        '<'     => '8249',
        '<<'    => '171',
        '>'     => '8250',
        '>>'    => '187',
        '?'     => '191',
        'A'     => '192,193,194,195,196,197,256,258,260,1040,7840,7842,7844,7846,7848,7850,7852,7854,7856,7858,7860,7862',
        'AE'    => '198',
        'B'     => '1041,1042',
        'C'     => '199,262,264,266,268,1062',
        'CH'    => '1063',
        'Cx'    => '264',
        'D'     => '208,270,272,1044',
        'D%'    => '1026',
        'DS'    => '1029',
        'DZ'    => '1039',
        'E'     => '200,201,202,203,274,276,278,280,282,1045,7864,7866,7868,7870,7872,7874,7876,7878',
        'EUR'   => '128,8364',
        'F'     => '1060',
        'G'     => '284,286,288,290,1043',
        'G%'    => '1027',
        'G3'    => '1168',
        'Gx'    => '284',
        'H'     => '292,294,1061',
        'Hx'    => '292',
        'I'     => '204,205,206,207,296,298,300,302,304,1048,7880,7882',
        'IE'    => '1028',
        'II'    => '1030',
        'IO'    => '1025',
        'J'     => '308,1049',
        'J%'    => '1032',
        'Jx'    => '308',
        'K'     => '310,1050',
        'KJ'    => '1036',
        'L'     => '163,313,315,317,319,321,1051',
        'LJ'    => '1033',
        'M'     => '1052',
        'N'     => '209,323,325,327,330,1053',
        'NJ'    => '1034',
        'No.'   => '8470',
        'O'     => '164,210,211,212,213,214,216,332,334,336,416,467,1054,7884,7886,7888,7890,7892,7894,7896,7898,7900,7902,7904,7906',
        'OE'    => '140,338',
        'P'     => '222,1055',
        'R'     => ',340,342,344,1056',
        'S'     => '138,346,348,350,352,1057',
        'SCH'   => '1065',
        'SH'    => '1064',
        'Sx'    => '348',
        'T'     => '354,356,358,1058',
        'Ts'    => '1035',
        'U'     => '217,218,219,220,360,362,364,366,368,370,431,1059,7908,7910,7912,7914,7916,7918,7920',
        'Ux'    => '364',
        'V'     => '1042',
        'V%'    => '1038',
        'W'     => '372',
        'Y'     => '159,221,374,376,1067,7922,7924,7926,7928',
        'YA'    => '1071',
        'YI'    => '1031',
        'YU'    => '1070',
        'Z'     => '142,377,379,381,1047',
        'ZH'    => '1046',
        '`'     => '8216',
        '`E'    => '1069',
        '`e'    => '1101',
        'a'     => '224,225,226,227,228,229,257,259,261,1072,7841,7843,7845,7847,7849,7851,7853,7855,7857,7859,7861,7863',
        'ae'    => '230',
        'b'     => '1073,1074',
        'c'     => '162,231,263,265,267,269,1094',
        'ch'    => '1095',
        'cx'    => '265',
        'd'     => '271,273,1076',
        'd%'    => '1106',
        'ds'    => '1109',
        'dz'    => '1119',
        'e'     => '232,233,234,235,275,277,279,281,283,1077,7865,7867,7869,7871,7873,7875,7877,7879',
        'f'     => '131,402,1092',
        'g'     => '285,287,289,291,1075',
        'g%'    => '1107',
        'g3'    => '1169',
        'gx'    => '285',
        'h'     => '293,295,1093',
        'hx'    => '293',
        'i'     => '236,237,238,239,297,299,301,303,305,1080,7881,7883',
        'ie'    => '1108',
        'ii'    => '1110',
        'io'    => '1105',
        'j'     => '309,1081',
        'j%'    => '1112',
        'jx'    => '309',
        'k'     => '311,312,1082',
        'kj'    => '1116',
        'l'     => '314,316,318,320,322,1083',
        'lj'    => '1113',
        'm'     => '1084',
        'mu'    => '181',
        'n'     => '241,324,326,328,329,331,1085',
        'nj'    => '1114',
        'o'     => '186,176,242,243,244,245,246,248,333,335,337,417,449,1086,7885,7887,7889,7891,7893,7895,7897,7899,7901,7903,7905,7907',
        'oe'    => '156,339',
        'p'     => '167,182,254,1087',
        'r'     => '341,343,345,1088',
        's'     => '154,347,349,351,353,1089',
        'sch'   => '1097',
        'sh'    => '1096',
        'ss'    => '223',
        'sx'    => '349',
        't'     => '355,357,359,1090',
        'ts'    => '1115',
        'u'     => '249,250,251,252,361,363,365,367,369,371,432,1091,7909,7911,7913,7915,7917,7919,7921',
        'ux'    => '365',
        'v'     => '1074',
        'v%'    => '1118',
        'w'     => '373',
        'y'     => '253,255,375,1099,7923,7925,7927,7929',
        'ya'    => '1103',
        'yen'   => '165',
        'yi'    => '1111',
        'yu'    => '1102',
        'z'     => '158,378,380,382,1079',
        'zh'    => '1078',
        '|'     => '166',
        '~'     => '8212',
    );

    /**
     * File size suffixes.
     * Source: http://en.wikipedia.org/wiki/Template:Quantities_of_bytes
     * Source: http://physics.nist.gov/cuu/Units/binary.html
     *
     * @var array
     */
    protected static $byteMultipliers = array('b', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

    /**
     * Generate query string
     *
     * @param array  $data      data to use
     * @param string $glue      string to add between param name and value
     * @param string $separator string to separate <name,value> pairs
     * @param string $quotes    char (string) to quote the value
     *
     * @return string
     */
    public static function buildQuery(array $data, $glue = '=', $separator = '&', $quotes = '')
    {
        $result = array();

        foreach ($data as $name => $value) {
            $result[] = $name . $glue . $quotes . $value . $quotes;
        }

        return implode($separator, $result);
    }

    /**
     * Parse arguments array
     *
     * @param array   $args     Array to parse
     * @param string  $glue     Char to agglutinate "name" and "value"
     * @param string  $quotes   Char to quote the "value" param
     * @param boolean $hasParts Flag OPTIONAL
     *
     * @return array
     */
    public static function parseArgs(array $args, $glue = '=', $quotes = '', $hasParts = true)
    {
        if (!isset($glue)) {
            $glue = '=';
        }

        $result = array();

        foreach ($args as $part) {

            if (!$hasParts) {
                $result[] = trim(trim($part), $quotes);

            } elseif (1 < count($tokens = explode($glue, trim($part)))) {
                $result[$tokens[0]] = trim($tokens[1], $quotes);
            }
        }

        return $result;
    }

    /**
     * Parse string into array
     *
     * @param string  $query     Query
     * @param string  $glue      Char to agglutinate "name" and "value"
     * @param string  $separator Char to agglutinate <"name", "value"> pairs
     * @param string  $quotes    Char to quote the "value" param
     * @param boolean $hasParts  Flag OPTIONAL
     *
     * @return array
     */
    public static function parseQuery($query, $glue = '=', $separator = '&', $quotes = '', $hasParts = true)
    {
        return static::parseArgs(explode($separator, $query), $glue, $quotes, $hasParts);
    }

    /**
     * Remove leading characters from string
     *
     * @param string $string string to prepare
     * @param string $chars  charlist to remove
     *
     * @return string
     */
    public static function trimLeadingChars($string, $chars)
    {
        return ltrim($string, $chars);
    }

    /**
     * Remove trailing characters from string
     *
     * @param string $string string to prepare
     * @param string $chars  charlist to remove
     *
     * @return string
     */
    public static function trimTrailingChars($string, $chars)
    {
        return rtrim($string, $chars);
    }

    /**
     * Get formatted price
     *
     * @param float $price value to format
     *
     * @return string
     */
    public static function formatPrice($price)
    {
        return sprintf('%.02f', round(doubleval($price), 2));
    }

    /**
     * Convert a string like "test_foo_bar" into the camel case (like "testFooBar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToCamelCase($string)
    {
        return ucfirst(str_ireplace(self::$from, self::$to, strval($string)));
    }

    /**
     * Convert a string like "testFooBar" into the underline style (like "test_foo_bar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertFromCamelCase($string)
    {
        return str_replace(self::$to, self::$from, lcfirst(strval($string)));
    }

    /**
     * Convert a string like "test_foo_bar" into the Pascal case (like "TestFooBar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToPascalCase($string)
    {
        return ucfirst(static::convertToCamelCase($string));
    }

    /**
     * Get canonical form of class name
     *
     * @param string  $class    Class name to prepare
     * @param boolean $relative Flag to enclose class name with namespace separator
     *
     * @return string
     */
    public static function prepareClassName($class, $relative = true)
    {
        return ($relative ? '' : '\\') . static::trimLeadingChars($class, '\\');
    }

    /**
     * Get file name by PHP class name
     *
     * @param string $class Class name
     *
     * @return string
     */
    public static function getClassFile($class)
    {
        return str_replace('\\', LC_DS, static::trimLeadingChars($class, '\\')) . '.php';
    }

    /**
     * Get full version
     *
     * @param string $versionMajor Major version
     * @param string $versionMinor Minor version
     *
     * @return string
     */
    public static function composeVersion($versionMajor, $versionMinor)
    {
        return $versionMajor . '.' . $versionMinor;
    }

    /**
     * Parse version and return array contained minorVersion and build number
     *
     * @param string $version Version (e.g '0', '1.1')
     *
     * @return array
     */
    public static function parseMinorVersion($version)
    {
        if (preg_match('/^(\d+)(?:\.(\d+))$/', $version, $match)) {
            $version = $match[1];
            $build = !empty($match[2]) ? $match[2] : 0;

        } else {
            $build = 0;
        }

        return array($version, $build);
    }

    /**
     * Prepare human-readable output for file size
     *
     * @param integer $size      Size in bytes
     * @param string  $separator To return a string OPTIONAL
     *
     * @return string
     */
    public static function formatFileSize($size, $separator = null)
    {
        $multiplier = 0;

        while (1000 < $size) {

            // http://en.wikipedia.org/wiki/Template:Quantities_of_bytes
            // http://physics.nist.gov/cuu/Units/binary.html
            $size /= 1000;

            $multiplier++;
        }

        // Do not display numbers after decimal point if size is in kilobytes.
        // When size is greater than display one number after decimal point.
        $result = array(number_format($size, $multiplier > 1 ? 1 : 0), static::$byteMultipliers[$multiplier]);

        return isset($separator) ? implode($separator, $result) : $result;
    }

    /**
    * Convert strings like 1M, 512K and so on to bytes size
    * 
    * @param string $sizeStr String represantation of filesize
    * 
    * @return integer
     */
    public static function returnBytesIniGetSize ($sizeStr)
    {
        switch (substr ($sizeStr, -1))
        {
            case 'M': case 'm': return (int)$sizeStr * 1048576;
            case 'K': case 'k': return (int)$sizeStr * 1024;
            case 'G': case 'g': return (int)$sizeStr * 1073741824;
            default: return $sizeStr;
        }
    }

    /**
     * Remove \r and \n chars from string (e.g to prevent CRLF injections)
     * 
     * @param string $value Input value
     *  
     * @return string
     */
    public static function removeCRLF($value)
    {
        return trim(preg_replace('/[\r\n]+/', '', ((string)$value)));
    }

    /*
     * Compose URL from target, action and additional params
     *
     * @param string $target    Page identifier OPTIONAL
     * @param string $action    Action to perform OPTIONAL
     * @param array  $params    Additional params OPTIONAL
     * @param string $interface Interface script OPTIONAL
     *
     * @return string
     */
    public static function buildURL($target = '', $action = '', array $params = array(), $interface = null)
    {
        $result = strval($interface);
        $urlParams = array();

        if (!empty($target)) {
            $urlParams['target'] = $target;
        }

        if (!empty($action)) {
            $urlParams['action'] = $action;
        }

        $params = $urlParams + $params;

        if (!empty($params)) {
            $result .= '?' . http_build_query($params, '', '&');
        }

        return $result;
    }

    /*
     *  Convert a string like "testFooBar" to translit
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToTranslit($string)
    {
        $tr = array();

        $string = static::normalizeUTF8($string);

        foreach (static::$translitMap as $letter => $set) {
            $letters = explode(',', $set);
            foreach ($letters as $v) {
                if ($v < 256) {
                    $tr[chr($v)] = $letter;
                }
                $tr['&#' . $v . ';'] = $letter;
            }

        }

        for ($i = 0; $i < 256; $i++) {
            if (empty($tr['&#' . $i . ';'])) {
                $tr['&#' . $i . ';'] = chr($i);
            }
        }

        if (function_exists('mb_encode_numericentity')) {
            $string = mb_encode_numericentity($string, array (0x0, 0xffff, 0, 0xffff), 'UTF-8');
        }

        return strtr($string, $tr);
    }

    /**
     * Normalize string to avoid grapheme cluster boundaries
     * @see http://www.unicode.org/reports/tr29/#Grapheme_Cluster_Boundaries
     *
     * @param string $string String to normalize
     *
     * @return string
     */
    public static function normalizeUTF8($string)
    {
        return class_exists('Normalizer')
            ? \Normalizer::normalize($string, \Normalizer::FORM_KC)
            : $string;
    }
}
