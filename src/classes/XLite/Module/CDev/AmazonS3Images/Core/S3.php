<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Core;

use AWSSDK\Aws\S3\S3Client;
use AWSSDK\Guzzle\Log\MonologLogAdapter;
use AWSSDK\Guzzle\Plugin\Log\LogPlugin;
use AWSSDK\Monolog\Logger;
use AWSSDK\Monolog\Handler\StreamHandler;

/**
 * AWS S3 client
 */
class S3 extends \XLite\Base\Singleton
{
    const GENERATION_LIMIT = 100;
    const DEFAULT_REGION = 'us-east-1';

    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * AWS S3 client 
     * 
     * @var \S3
     */
    protected static $client;

    /**
     * Valid status
     * 
     * @var boolean
     */
    protected $valid = false;

    /**
     * URL prefix
     * 
     * @var string
     */
    protected static $urlPrefix;

    /**
     * Flag: true - all requests to Amazon servers will be logged
     *
     * @var boolean
     */
    protected static $debugEnabled = false;

    /**
     * Check valid status
     * 
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Write 
     * 
     * @param string $path        Short path
     * @param string $data        Data
     * @param array  $httpHeaders HTTP headers OPTIONAL
     *  
     * @return boolean
     */
    public function write($path, $data, array $httpHeaders = array())
    {
        $result = false;

        try {

            $res = static::getClient()->upload(
                static::getConfig()->bucket,
                $path,
                $data,
                'public-read',
                $httpHeaders
            );

            $result = isset($res['ObjectURL']);

        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Copy 
     * 
     * @param string $from        Full path
     * @param string $to          Short path
     * @param array  $httpHeaders HTTP headers OPTIONAL
     *  
     * @return boolean
     */
    public function copy($from, $to, array $httpHeaders = array())
    {
        $result = false;

        if (\Includes\Utils\FileManager::isExists($from) && $f = fopen($from, 'r')) {

            try {

                $res = static::getClient()->upload(
                    static::getConfig()->bucket,
                    $to,
                    $f,
                    'public-read',
                    $httpHeaders
                );

                $result = isset($res['ObjectURL']);

            } catch (\Exception $e) {
                \XLite\Logger::getInstance()->registerException($e);
            }
        }

        return $result;
    }

    /**
     * Read 
     * 
     * @param string $path Short path
     *  
     * @return string
     */
    public function read($path)
    {
        $result = null;

        try {
            $config = static::getConfig();
            $command = static::getClient()->getCommand(
                'GetObject',
                array(
                    'Bucket' => $config->bucket,
                    'Key'    => $path,
                )
            );

            $res = $command->getResult();

            $result = isset($res['Body']) ? (string)$res['Body'] : '';

        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Delete 
     * 
     * @param string $path Short path
     *  
     * @return boolean
     */
    public function delete($path)
    {
        $result = false;

        try {
            static::getClient()->getCommand(
                'DeleteObject',
                array(
                    'Bucket' => static::getConfig()->bucket,
                    'Key' => $path
                )
            )->getResult();

            $result = true;

        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Delete directory
     *
     * @param string $path Short path
     *
     * @return boolean
     */
    public function deleteDirectory($path)
    {
        $result = false;

        try {
            static::getClient()->deleteMatchingObjects(static::getConfig()->bucket, $path);
            $result = true;

        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Read directory
     *
     * @param string $path Short path
     *
     * @return array
     */
    public function readDirectory($path)
    {
        $result = array();

        try {
            $config = static::getConfig();
            $client = static::getClient();
            $objects = $client->getCommand('ListObjects', array('Bucket' => $config->bucket, 'Prefix' => $path))->getResult();

            if (isset($objects['Contents']) && is_array($objects['Contents'])) {
                foreach ($objects['Contents'] as $o) {
                    if ($o['Key'] !== $path) {
                        $result[] = $o['Key'];
                    }
                }
            }

        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Check - path is directory or not
     *
     * @param string $path Short path
     *
     * @return boolean
     */
    public function isDir($path)
    {
        $result = false;

        try {
            $config = static::getConfig();
            $command = static::getClient()->getCommand(
                'HeadObject',
                array(
                    'Bucket' => $config->bucket,
                    'Key'    => $path,
                )
            );

            $res = $command->getResult();

            $result = (
                isset($res['ContentType'])
                && 'binary/octet-stream' == $res['ContentType']
            );

        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * Get URL by short path
     * 
     * @param string $path Short path
     *  
     * @return string
     */
    public static function getURL($path)
    {
        if (!isset(static::$urlPrefix)) {

            $config = static::getConfig();

            $protocol = 'https_only' === \XLite\Core\Config::getInstance()->CDev->AmazonS3Images->cloudfront_protocol
                ? 'https:'
                : '';

            static::$urlPrefix = $protocol . '//';
            if ($config->cloudfront_domain) {
                static::$urlPrefix .= $config->cloudfront_domain . '/';

            } else {
                $url = static::getClient()->getObjectUrl($config->bucket, $path);
                static::$urlPrefix .= rtrim(parse_url($url, PHP_URL_HOST), '/') . '/';

                if (strpos(static::$urlPrefix, $config->bucket) === false) {
                    static::$urlPrefix .= $config->bucket . '/';
                }
            }
        }

        $name = basename($path);
        $path = preg_replace('/\/' . preg_quote($name, '/') . '/', '/' . urlencode($name), $path);
        return static::$urlPrefix . $path;
    }

    /**
     * Is given string match one of possible s3 urls
     *
     * @param $url
     *
     * @return bool
     */
    public function isMatchS3Url($url)
    {
        $possibleUrls = $this->executeCachedRuntime(function(){
            $config = static::getConfig();
            $result = [];

            if ($config->cloudfront_domain) {
                $result[] = $config->cloudfront_domain;
            }

            $s3url = static::getClient()->getObjectUrl($config->bucket, '');
            $s3url = rtrim(parse_url($s3url, PHP_URL_HOST), '/');

            if (strpos(static::$urlPrefix, $config->bucket) === false) {
                $result[] = $s3url . '/' . $config->bucket . '/';
                $result[] = $config->bucket . '.' . $s3url;
            } else {
                $result[] = $s3url;
                $result[] = str_replace($config->bucket . '.', '', $s3url) . '/' . $config->bucket . '/';
            }

        });

        foreach ($possibleUrls as $possibleUrl) {
            if (strpos($url, $possibleUrl) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check - file is exists or not
     * 
     * @param string $path Short path
     *  
     * @return boolean
     */
    public function isExists($path)
    {
        return static::getClient()->doesObjectExist(static::getConfig()->bucket, $path);
    }

    /**
     * Generate unique path 
     * 
     * @param string $path Short path
     *  
     * @return string
     */
    public function generateUniquePath($path)
    {
        if ($this->isExists($path)) {
            if (preg_match('/^(.+)\.([^\.]+)$/Ss', $path, $match)) {
                $base = $match[1] . '.';
                $ext = '.' . $match[2];

            } else {
                $base = $path . '.';
                $ext = '';
            }

            $i = 0;
            do {
                $path = $base . uniqid('', true) . $ext;
                $i++;
            } while ($this->isExists($path) && self::GENERATION_LIMIT > $i);
        }

        return $path;
    }

    /**
     * Check settings 
     * 
     * @param string $bucket    S3 bucket
     * @param string $accessKey AWS access key OPTIONAL
     * @param string $secretKey AWS secret key OPTIONAL
     *  
     * @return boolean
     */
    public function checkSettings($bucket, $accessKey = null, $secretKey = null)
    {
        $valid = false;

        $client = (!empty($accessKey) || !empty($secretKey))
            ? static::getS3Client($accessKey, $secretKey)
            : static::getClient();

        if ($client) {

            $region = $this->detectBucketLocation($client, $bucket);

            if (isset($region)) {
                $valid = true;
                if ($this->getConfig()->region != $region) {
                    \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                        array(
                            'category' => 'CDev\\AmazonS3Images',
                            'name'     => 'region',
                            'value'    => $region,
                        )
                    );
                }
            }
        }

        return $valid;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        $config = static::getConfig();

        if ($config->access_key && $config->secret_key && $config->bucket && function_exists('curl_init')) {
    
            static::preloadIncludes();

            try {
                $this->valid = $this->checkSettings($config->bucket);

            } catch (\Exception $e) {
                \XLite\Logger::getInstance()->registerException($e);
            }
        }
    }

    /**
     * Detect and return bucket location (region)
     *
     * @param \Aws\S3\S3Client $client S3 client
     * @param string           $bucket
     *
     * @return string
     */
    protected function detectBucketLocation($client, $bucket)
    {
        $location = null;

        try {

            $command = $client->getCommand(
                'GetBucketLocation',
                array(
                    'Bucket' => $bucket,
                )
            );

            $result = $command->getResult();

            if ($result && isset($result['Location'])) {
                $location = $result['Location'];
            }

        } catch (\Exception $e) {
        }

        return $location;
    }

    // {{{ Service methods

    /**
     * Get client
     *
     * @return \Aws\S3\S3Client
     */
    protected static function getClient()
    {
        if (!static::$client) {

            $config = static::getConfig();

            $region = $config->region ?: null;

            static::$client = static::getS3Client($config->access_key, $config->secret_key, $region);
        }

        return static::$client;
    }

    /**
     * Create S3 client object
     *
     * @return \Aws\S3\S3Client
     */
    protected static function getS3Client($key, $secret, $region = null)
    {
        static::preloadIncludes();

        if (empty($region)) {
            $region = static::DEFAULT_REGION;
        }

        $client = \Aws\S3\S3Client::factory(
            array(
                'key'       => $key,
                'secret'    => $secret,
                'signature' => 'v4',
                'region'    => $region,
            )
        );

        if (static::$debugEnabled) {
            $client->addSubscriber(static::getLogPlugin());
        }

        return $client;
    }

    /**
     * Get module options
     *
     * @return \XLite\Core\CommonCell
     */
    protected static function getConfig()
    {
        return \XLite\Core\Config::getInstance()->CDev->AmazonS3Images;
    }

    /**
     * Get log plugin for S3 client
     *
     * @return \LogPlugin
     */
    protected static function getLogPlugin()
    {
        $log = new \Monolog\Logger('aws');

        $header = '<' . '?php die(); ?' . '>' . PHP_EOL;

        $path = \XLite\Logger::getCustomLogPath('AWSS3');

        if (!file_exists($path) || strlen($header) > filesize($path)) {
            @file_put_contents($path, $header);
        }

        $log->pushHandler(new \Monolog\Handler\StreamHandler($path, \Monolog\Logger::DEBUG));

        $logger = new \Guzzle\Log\MonologLogAdapter($log);

        $logPlugin = new \Guzzle\Plugin\Log\LogPlugin($logger);

        return $logPlugin;
    }

    /**
     * Load AWS SDK autoloader
     *
     * @return void
     */
    protected static function preloadIncludes()
    {
        include_once LC_DIR_MODULES . 'CDev' . LC_DS . 'AmazonS3Images' . LC_DS . 'lib' . LC_DS . 'AWSSDK' . LC_DS . 'aws-autoloader.php';
    }

    // }}}
}
