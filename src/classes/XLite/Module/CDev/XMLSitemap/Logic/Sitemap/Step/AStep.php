<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step;

use XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Generator;

/**
 * Abstract Step
 */
abstract class AStep extends \XLite\Base implements \SeekableIterator, \Countable
{
    /**
     * Generator
     *
     * @var Generator
     */
    protected $generator;

    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode = null;

    /**
     * Constructor
     *
     * @param Generator $generator Generator
     * @param string $languageCode Language code
     */
    public function __construct(Generator $generator = null, $languageCode = null)
    {
        $this->generator = $generator;
        $this->languageCode = $languageCode;
    }

    /**
     * Run step
     *
     * @return boolean
     */
    abstract public function run();

    /**
     * Finalize
     *
     * @return void
     */
    abstract public function finalize();

    /**
     * \SeekableIterator::current
     *
     * @return \XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step\AStep
     */
    public function current()
    {
        return $this;
    }
}