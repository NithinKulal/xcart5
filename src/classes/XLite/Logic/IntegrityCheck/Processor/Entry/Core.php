<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\IntegrityCheck\Processor\Entry;

use XLite\Core\Pack\Distr;

/**
 * Class Core
 */
class Core implements IEntry
{
    /**
     * @var \XLite\Core\Pack\Distr
     */
    protected $coreDistr;

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
        $this->coreDistr = new Distr();
        $this->version = $version;

        if ($this->version === null) {
            $metadata = $this->coreDistr->getMetadata();

            $this->version = [
                'major' => $metadata[Distr::METADATA_FIELD_VERSION_MAJOR],
                'minor' => $metadata[Distr::METADATA_FIELD_VERSION_MINOR] . '.' . $metadata[Distr::METADATA_FIELD_VERSION_BUILD]
            ];
        }
    }

    /**
     * @return \Iterator
     */
    public function getRealFiles()
    {
        return $this->coreDistr->getDirectoryIterator();
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