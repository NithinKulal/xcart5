<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify;

/**
 * Add new label dialog
 */
class AddLabel extends \XLite\View\AView
{
    /**
     * Check if language is requried or not
     *
     * @param \XLite\Model\Language $language Language_
     *
     * @return boolean
     */
    public function isRequiredLanguage(\XLite\Model\Language $language)
    {
        return $language->getCode() === static::getDefaultLanguage();
    }

    /**
     * Get added languages
     *
     * @return array
     */
    public function getAddedLanguages()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Language')->findAddedLanguages();
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'languages/add_label.twig';
    }
}
