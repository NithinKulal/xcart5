<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LayoutSettings;

/**
 * Layout settings
 */
class Settings extends \XLite\View\AView
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'layout_settings/settings/style.less';

        return $list;
    }

    /**
     * Returns current skin
     *
     * @return \XLite\Model\Module
     */
    public function getCurrentSkin()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout_settings/settings/body.twig';
    }

    /**
     * Returns preview image url
     *
     * @return string
     */
    protected function getPreviewImageURL()
    {
        return \XLite\Core\Layout::getInstance()->getCurrentLayoutPreview();
    }

    /**
     * Returns current skin name
     *
     * @return string
     */
    protected function getCurrentSkinName()
    {
        $name = static::t('Standard');

        /** @var \XLite\Model\Module $module */
        $module = $this->getCurrentSkin();
        if ($module) {
            $name = \XLite\Core\Layout::getInstance()->getLayoutColorName() ?: $module->getModuleName();
        }

        return $name;
    }

    /**
     * Check show settings
     *
     * @return boolean
     */
    protected function showSettingsForm()
    {
        /** @var \XLite\Model\Module $module */
        $module = $this->getCurrentSkin();

        return $module && $module->callModuleMethod('showSettingsForm', false);
    }

    /**
     * Check has custom options
     *
     * @return boolean
     */
    protected function getSettingsForm()
    {
        /** @var \XLite\Model\Module $module */
        $module = $this->getCurrentSkin();

        return $module
            ? $module->getSettingsForm()
            : '';
    }
}
