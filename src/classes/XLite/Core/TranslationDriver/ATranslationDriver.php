<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\TranslationDriver;

/**
 * Abstract translation driver
 */
abstract class ATranslationDriver extends \XLite\Base
{
    /**
     * Mark driver as invalid
     *
     * @var string
     */
    protected $invalid = false;

    /**
     * Translate label
     *
     * @param string $name Label name
     * @param string $code Language code
     *
     * @return string|void
     *
     * @throws \XLite\Core\Exception\TranslationDriverError
     */
    abstract public function translate($name, $code);

    /**
     * Check - valid driver or not
     *
     * @return boolean
     */
    public function isValid()
    {
        return !$this->invalid;
    }

    /**
     * Reset language driver
     *
     * @return void
     */
    public function reset()
    {
        $this->invalid = false;
    }

    /**
     * Mark driver as invalid
     */
    public function markAsInvalid()
    {
        $this->invalid = true;
    }

    /**
     * Get driver name
     *
     * @return string
     */
    public function getName()
    {
        return static::TRANSLATION_DRIVER_NAME;
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Repo\LanguageLabel
     */
    protected function getRepo()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel');
    }
}
