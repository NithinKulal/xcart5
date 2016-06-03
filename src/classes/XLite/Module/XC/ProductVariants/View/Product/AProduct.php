<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product;

/**
 * Abstract class
 *
 */
abstract class AProduct extends \XLite\View\AView
{
    /**
     * The number of variants limits
     */
    const VARIANTS_NUMBER_SOFT_LIMIT = 30;
    const VARIANTS_NUMBER_HARD_LIMIT = 300;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('product'));
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
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductVariants/variants/parts';
    }

    /**
     * Return block style
     *
     * @return string
     */
    protected function getBlockStyle()
    {
        return 'dialog-block';
    }

    /**
     * Get variants limit warning message (for tooltip)
     *
     * @return string
     */
    protected function getLimitWarningMessage()
    {
        return static::t('Number of variants warning', array('limit' => $this->getVariantsNumberSoftLimit()));
    }

    /**
     * Get variants limit confirmation message (for js confirmation)
     *
     * @return string
     */
    protected function getLimitConfirmationMessage()
    {
        return static::t('Number of variants confirmation', array('limit' => $this->getVariantsNumberSoftLimit()));
    }

    /**
     * Get variants limit error message (for tooltip and JS alert)
     *
     * @return string
     */
    protected function getLimitErrorMessage()
    {
        return static::t("Number of variants error", array('limit' => $this->getVariantsNumberHardLimit()));
    }

    /**
     * Get variants number warning message (for variants page)
     *
     * @return string
     */
    protected function getVariantsNumberWarning()
    {
        return static::t("Number of variants warning message", array('limit' => $this->getVariantsNumberSoftLimit()));
    }

    /**
     * Get variants number soft limit (to display warning if exceed)
     *
     * @return integer
     */
    protected function getVariantsNumberSoftLimit()
    {
        return static::VARIANTS_NUMBER_SOFT_LIMIT;
    }

    /**
     * Get variants number hard limit (to display error)
     *
     * @return integer
     */
    protected function getVariantsNumberHardLimit()
    {
        return static::VARIANTS_NUMBER_HARD_LIMIT;
    }
}
