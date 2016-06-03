<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\Template
 */
class Template extends \XLite\View\FormField\Select\Regular
{
    const SKIN_STANDARD = 'standard';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/template.less';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/template.js';

        return $list;
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return parent::getValue() ?: static::SKIN_STANDARD;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = array();
        foreach ($this->getSkinModules() as $module) {
            $options[$this->getModuleId($module)] = $this->getModuleLabel($module);
        }

        return $options;
    }

    /**
     * Returns skin modules
     *
     * @return array
     */
    protected function getSkinModules()
    {
        $result = array(static::SKIN_STANDARD);

        if (!defined('LC_MODULE_CONTROL')) {
            define('LC_MODULE_CONTROL', true);
        }

        $skin_modules = \XLite\Core\Database::getRepo('XLite\Model\Module')->getSkinModules();

        foreach ($skin_modules as $module) {
            $colors = $module->callModuleMethod('getLayoutColors');

            if ($colors) {
                foreach (array_keys($colors) as $color) {
                    $result[] = array(
                        'module' => $module,
                        'color'  => $color,
                    );
                }
            } else {
                $result[] = array(
                    'module' => $module,
                );
            }
        }

        return $result;
    }

    /**
     * Returns option id
     *
     * @param array|string $module Module
     *
     * @return string
     */
    protected function getModuleId($module)
    {
        if (static::SKIN_STANDARD === $module) {
            $result = static::SKIN_STANDARD;

        } else {
            $result = $module['module']->getModuleId() . (isset($module['color']) ? ('_' . $module['color']) : '');
        }

        return (string) $result;
    }

    /**
     * Returns option image
     *
     * @param array|string $module Module
     *
     * @return string
     */
    protected function getModuleImage($module)
    {
        if (static::SKIN_STANDARD === $module) {
            $result = \XLite\Core\Layout::getInstance()->getResourceWebPath('images/layout/preview_list.jpg');

        } else {
            $skinModule = $module['module'];
            $image = 'preview_list' . (isset($module['color']) ? ('_' . $module['color']) : '') . '.jpg';
            $result = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                'modules/' . $skinModule->getAuthor() . '/' . $skinModule->getName() . '/' . $image
            );
        }

        return $result
            ?: \XLite\Core\Layout::getInstance()->getResourceWebPath('images/layout/preview_list_placeholder.jpg');
    }

    /**
     * Returns option image
     *
     * @param array|string $module Module
     *
     * @return string
     */
    protected function getModuleLabel($module)
    {
        if (static::SKIN_STANDARD === $module) {
            $result = static::t('Standard');

        } else {
            $availableColors = $module['module']->callModuleMethod('getLayoutColors');

            $result = (isset($module['color']) && isset($availableColors[$module['color']]))
                ? $availableColors[$module['color']]
                : $module['module']->getModuleName();
        }

        return $result;
    }

    /**
     * Check module is selected
     *
     * @param array|string $module Module
     *
     * @return boolean
     */
    protected function isModuleSelected($module)
    {
        $value = $this->getValue();

        if (static::SKIN_STANDARD === $module) {
            $result = static::SKIN_STANDARD === $value;

        } else {
            $result = $this->getModuleId($module) === (string) $value;
        }

        return $result;
    }

    /**
     * Check module is recently installed
     *
     * @param array|string $module Module
     *
     * @return boolean
     */
    protected function isMarked($module)
    {
        $result = false;

        if (static::SKIN_STANDARD !== $module
            && \XLite\Core\Request::getInstance()->recent
        ) {
            $installedIds = \XLite\Controller\Admin\Base\AddonsList::getRecentlyInstalledModuleList();

            $result = in_array($module['module']->getModuleId(), $installedIds, false);
        }

        $moduleId = \XLite\Core\Request::getInstance()->moduleId;
        if ($moduleId) {
            if (static::SKIN_STANDARD === $module) {
                $result = static::SKIN_STANDARD === $moduleId;

            } else {
                $result = $this->getModuleId($module) === $moduleId;
            }
        }

        return $result;
    }

    /**
     * Check if redeploy is required
     *
     * @param array|string $module Module
     *
     * @return string
     */
    protected function isRedeployRequired($module)
    {
        $moduleId = static::SKIN_STANDARD !== $module ? $module['module']->getModuleId() : $module;

        return (string) ((int) $this->getValue() !== (int) $moduleId);
    }

    /**
     * Returns style class
     *
     * @param array|string $module Module
     *
     * @return string
     */
    protected function getStyleClass($module)
    {
        $result = 'template';

        if ($this->isModuleSelected($module)) {
            $result .= ' selected checked';
        }

        if ($this->isMarked($module)) {
            $result .= ' marked';
        }

        return $result;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'template.twig';
    }
}
