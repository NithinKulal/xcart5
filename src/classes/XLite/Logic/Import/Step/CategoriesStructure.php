<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Step;

/**
 * Rebuild categories structure step
 */
class CategoriesStructure extends \XLite\Logic\Import\Step\AStep
{
    /**
     * Get final note
     *
     * @return string
     */
    public function getFinalNote()
    {
        return static::t('Categories processed');
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return static::t('Processing categories...');
    }

    /**
     * Process row
     *
     * @return boolean
     */
    public function process()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Category')->correctCategoriesStructure();

        $this->getOptions()->commonData['csProcessed'] = $this->count();
        $this->getOptions()->position = $this->count();

        return true;
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->getOptions()->commonData['csCount'])) {
            $this->getOptions()->commonData['csCount']
                = \XLite\Core\Database::getRepo('XLite\Model\Category')->count();
        }

        return $this->getOptions()->commonData['csCount'];
    }

    /**
     * Check - allowed step or not
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return parent::isAllowed() && $this->count() > 0 && $this->isCategoriesStructureCorrectionAllowed();
    }

    /**
     * Check if this step was enabled by this import
     *
     * @return bool
     */
    public function isCategoriesStructureCorrectionAllowed()
    {
        return !empty($this->getOptions()->commonData['correctCategoriesAllowed']);
    }

    /**
     * Get error language label
     *
     * @return array
     */
    public function getErrorLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Categories processed: X out of Y with errors',
            [
                'X'      => $options->position,
                'Y'      => $this->count(),
                'errors' => $options->errorsCount,
                'warns'  => $options->warningsCount,
            ]
        );
    }

    /**
     * Get normal language label
     *
     * @return array
     */
    public function getNormalLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Categories processed: X out of Y',
            [
                'X' => $options->position,
                'Y' => $this->count(),
            ]
        );

    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        $list = parent::getMessages();

        if (!empty($this->getOptions()->commonData['csProcessed'])) {
            $list[] = [
                'text' => static::t(
                    'Categories processed: {{count}}',
                    ['count' => $this->getOptions()->commonData['csProcessed']]
                ),
            ];
        }

        return $list;
    }
}
