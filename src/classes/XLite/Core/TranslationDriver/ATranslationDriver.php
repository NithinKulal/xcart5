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
     * Translate label
     *
     * @param string $name Label name
     * @param string $code Language code
     *
     * @return string|void
     */
    abstract public function translate($name, $code);

    /**
     * Check - valid driver or not
     *
     * @return boolean
     */
    abstract public function isValid();

    /**
     * Reset language driver
     *
     * @return void
     */
    abstract public function reset();


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
