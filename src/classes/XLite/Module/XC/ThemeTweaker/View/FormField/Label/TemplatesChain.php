<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\FormField\Label;

/**
 * Label
 */
class TemplatesChain extends \XLite\View\FormField\Label\ALabel
{
    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/form_field/templates_chain';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'body.twig';
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'templates-chain-field';

        return $classes;
    }

    /**
     * Return templates chain
     *
     * @return array
     */
    protected function getChain()
    {
        $result = array();

        /** @var \XLite\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();
        $shortPath = $this->getValue();

        $files = array();
        foreach ($layout->getSkinPaths(\XLite::CUSTOMER_INTERFACE) as $path) {
            $fullPath = $path['fs'] . LC_DS . $shortPath;
            if (file_exists($fullPath) && is_file($fullPath)) {
                array_unshift($files, $fullPath);
            }
        }

        foreach ($files as $fullPath) {
            $result[substr($fullPath, strlen(LC_DIR_SKINS))] = htmlspecialchars(\Includes\Utils\FileManager::read($fullPath));
        }

        return $result;
    }
}
