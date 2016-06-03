<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify;

/**
 * Add (activate) language dialog
 */
class AddLanguage extends \XLite\View\AView
{
    /**
     * Get inactive languages
     *
     * @return array
     */
    public function getInactiveLanguages()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Language')
            ->findInactiveLanguages();
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'languages/add_language.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getInactiveLanguages();
    }

    /**
     * Get related module page URL
     *
     * @param \XLite\Model\Language $entity Language object
     *
     * @return string
     */
    protected function getModulePageURL($entity)
    {
        $url = null;

        $module = $entity->getModule();

        if (!empty($module) && preg_match('/(\w+)\\\\(\w+)/', $module, $match)) {
            $moduleObj = \XLite\Core\Database::getRepo('XLite\Model\Module')->findModuleByName($module);
            $url = $moduleObj->getInstalledURL();
        }

        return $url;
    }
}
