<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguageSelector;

/**
 * Abstract language selector
 */
abstract class ALanguageSelector extends \XLite\View\AView
{
    /**
     * Active languages cache
     */
    protected static $languages;

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'language_selector';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return 1 < count($this->getActiveLanguages());
    }

    /**
     * Return list of all active languages
     *
     * @return array
     */
    protected function getActiveLanguages()
    {
        $list = static::$languages;

        if (null === $list) {
            $list = array();

            foreach (\XLite\Core\Database::getRepo('\XLite\Model\Language')->findActiveLanguages() as $language) {
                if ($this->isActiveLanguage($language)) {
                    $list[] = $language;
                }
            }

            static::$languages = $list;
        }

        return $list;
    }

    /**
     * Link to change language
     *
     * @param \XLite\Model\Language $language Language to set
     *
     * @return string
     */
    protected function getChangeLanguageLink(\XLite\Model\Language $language)
    {
        return $this->buildURL(
            $this->getTarget(),
            'change_language',
            array(
                'language' => $language->getCode(),
            ) + $this->getAllParams(),
            false
        );
    }


    /**
     * Check if language is selected
     *
     * @param \XLite\Model\Language $language Language to check
     *
     * @return boolean
     */
    protected function isLanguageSelected(\XLite\Model\Language $language)
    {
        return $language->getCode() === \XLite\Core\Session::getInstance()->getLanguage()->getCode();
    }

    /**
     * Check if language is selected
     *
     * @param \XLite\Model\Language $language Language to check
     *
     * @return boolean
     */
    protected function isActiveLanguage(\XLite\Model\Language $language)
    {
        return true;
    }
}
