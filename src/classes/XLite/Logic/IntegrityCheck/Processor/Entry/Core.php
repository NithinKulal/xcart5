<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\IntegrityCheck\Processor\Entry;

/**
 * Class Core
 */
class Core implements IEntry
{
    /**
     * @var
     */
    protected $version;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Core constructor
     *
     * @param $version
     */
    public function __construct($version = null)
    {
        $this->version = $version;

        if ($this->version === null) {
            $this->version = [
                'major' => \XLite::getInstance()->getMajorVersion(),
                'minor' => \XLite::getInstance()->getMinorVersion()
            ];
        }
    }

    /**
     * @return \Iterator
     */
    public function getRealFiles()
    {
        $builder = new CoreIteratorBuilder();
        return $builder->getIterator();
    }

    /**
     * @return array
     */
    public function getHashes()
    {
        $cacheDriver = \XLite\Core\Database::getCacheDriver();

        $result = $cacheDriver->fetch($this->getCacheKey());

        if ($result === false) {
            $result = \XLite\Core\Marketplace::getInstance()->getCoreHash(
                $this->version['major'],
                $this->version['minor']
            );

            if ($result) {
                $cacheDriver->save($this->getCacheKey(), $result, 86400);
            } else {
                $this->errors[] = \XLite\Core\Marketplace::getInstance()->getLastErrorCode();
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return md5(serialize($this->version));
    }
}
