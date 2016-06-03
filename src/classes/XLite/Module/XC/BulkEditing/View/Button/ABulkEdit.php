<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\Button;

/**
 * ItemsExport button
 */
abstract class ABulkEdit extends \XLite\View\Button\AButton
{
    protected $additionalButtons;
    protected $additionalButtonsWidgets;

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/BulkEditing/button/bulk_edit.js';

        return $list;
    }

    /**
     * Register CSS files for delete address button
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/BulkEditing/button/bulk_edit.css';

        return $list;
    }

    abstract protected function defineAdditionalButtons();

    protected function getAdditionalButtons()
    {
        if (null === $this->additionalButtons) {
            $this->additionalButtons = $this->defineAdditionalButtons();

            uasort($this->additionalButtons, function ($a, $b) {
                $aPos = (int) (isset($a['position']) ? $a['position'] : 0);
                $bPos = (int) (isset($b['position']) ? $b['position'] : 0);

                return $aPos === $bPos ? 0 : ($aPos < $bPos ? -1 : 1);
            });
        }

        return $this->additionalButtons;
    }

    protected function getAdditionalButtonsWidgets()
    {
        if (null === $this->additionalButtonsWidgets) {
            $this->additionalButtonsWidgets = [];
            foreach ($this->getAdditionalButtons() as $button) {
                $class = isset($button['class'])
                    ? $button['class']
                    : 'XLite\Module\XC\BulkEditing\View\Button\Scenario';
                unset($button['class']);

                $this->additionalButtonsWidgets[] = $this->getWidget($button, $class);
            }
        }

        return $this->additionalButtonsWidgets;
    }

    /**
     * Get attributes
     *
     * @return boolean
     */
    protected function isMultipleOptions()
    {
        return 1 < count($this->getAdditionalButtons());
    }

    /**
     * Get attributes
     *
     * @return boolean
     */
    protected function getFirstProviderLabel()
    {
        $buttons = $this->getAdditionalButtons();
        $firstButtonKey = key($buttons);
        $button = $buttons[$firstButtonKey];

        return isset($button['label']) ? $button['label'] : $firstButtonKey;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Edit all');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/BulkEditing/button/bulk_edit.twig';
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' bulk-edit';
    }
}
