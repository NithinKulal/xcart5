<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Start export button
 */
class StartExport extends \XLite\View\Button\ProgressState
{
    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return $this->isExportLocked()
             ? static::t('Please wait')
             : static::t('Start Export');
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getJSCode()
    {
        return 'this.form.submit();';
    }

    /**
     * Get export state
     *
     * @return boolean
     */
    public function isExportLocked()
    {
        return \XLite\Logic\Export\Generator::isLocked();
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        $locked = $this->isExportLocked() ? 'disabled' : '';
        return parent::getClass() . ' submit main-button regular-main-button ' . $locked;
    }
}
