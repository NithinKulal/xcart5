<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Import;

/**
 * Progress section
 */
class Progress extends \XLite\View\AView
{
    use \XLite\View\EventTaskProgressProviderTrait;

    /**
     * Returns processing unit
     * @return mixed
     */
    protected function getProcessor()
    {
        return \XLite::getController()->getImporter();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/progress.twig';
    }

    /**
     * Provides status message for progress bar
     * 
     * @return string
     */
    protected function getProgressMessage()
    {
        return \XLite\Core\Translation::lbl('Initializing...');
    }

    /**
     * Get time label
     *
     * @return string
     */
    protected function getTimeLabel()
    {
        return \XLite\Core\Translation::formatTimePeriod($this->getImporter()->getStep()->getTimeRemain());
    }
}
