<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\TranslationDriver;

/**
 * DB-based driver (without cache)
 */
class DbDirect extends \XLite\Core\TranslationDriver\ATranslationDriver
{
    const TRANSLATION_DRIVER_NAME = 'DatabaseDirect';


    /**
     * Translate label
     *
     * @param string $name Label name
     * @param string $code Language code
     *
     * @return string|void
     */
    public function translate($name, $code)
    {
        $label = $this->getRepo()->findOneByName($name);

        return $label && $label->getHardTranslation($code)
            ? $label->getHardTranslation($code)->getLabel()
            : null;
    }

    /**
     * Check if driver is valid or not
     *
     * @return boolean
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Reset language driver
     *
     * @return void
     */
    public function reset()
    {
    }
}
