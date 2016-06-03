<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\TranslationLanguage;

/**
 * Abstract translation language 
 */
abstract class ATranslationLanguage extends \XLite\Base
{
    /**
     * Label handlers (cache)
     *
     * @var   array
     */
    protected $labelHandlers;

    /**
     * Define label handlers
     *
     * @return array
     */
    protected function defineLabelHandlers()
    {
        return array(
            '_X_ items'                   => 'XItemsMinicart',
            'X items in bag'              => 'XItemsInBag',
            'X items'                     => 'XItems',
            'X items available'           => 'XItemsAvailable',
            'Your shopping bag - X items' => 'YourShoppingBagXItems',
            'X modules will be upgraded'  => 'XModulesWillBeUpgraded',
            'X modules will be disabled'  => 'XModulesWillBeDisabled',
        );
    }

    /**
     * Get label handler
     *
     * @param string $name Label name
     *
     * @return string
     */
    public function getLabelHandler($name)
    {
        $handler = null;
        $handlers = $this->getLabelHandlers();

        if (!empty($handlers[$name])) {
            $handler = $handlers[$name];

            if (is_string($handler)) {
                if (method_exists($this, $handler)) {
                    $handler = array($this, $handler);

                } elseif (method_exists($this, 'translateLabel' . ucfirst($handler))) {
                    $handler = array($this, 'translateLabel' . ucfirst($handler));
                }
            }

            if (!is_callable($handler)) {
                $handler = null;
            }
        }

        return $handler;
    }

    /**
     * Get label handlers
     *
     * @return array
     */
    protected function getLabelHandlers()
    {
        if (!isset($this->labelHandlers)) {
            $this->labelHandlers = $this->defineLabelHandlers();
        }

        return $this->labelHandlers;
    }

    // {{{ Label translators

    /**
     * Translate label 'X items' in minicart
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItemsMinicart(array $arguments)
    {
        return 1 == $arguments['count']
            ? \XLite\Core\Translation::getInstance()->translateByString('_X_ item', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('_X_ items', $arguments);
    }

    /**
     * Translate label 'X items in bag'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItemsInBag(array $arguments)
    {
        return 1 == $arguments['count']
            ? \XLite\Core\Translation::getInstance()->translateByString('X item in bag', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('X items in bag', $arguments);
    }

    /**
     * Translate label 'X items'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItems(array $arguments)
    {
        return 1 == $arguments['count']
            ? \XLite\Core\Translation::getInstance()->translateByString('X item', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('X items', $arguments);
    }

    /**
     * Translate label 'X items available'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItemsAvailable(array $arguments)
    {
        return 1 == $arguments['count']
            ? \XLite\Core\Translation::getInstance()->translateByString('X item available', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('X items available', $arguments);
    }

    /**
     * Translate label 'Your shopping bag - X items'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelYourShoppingBagXItems(array $arguments)
    {
        return 1 == $arguments['count']
            ? \XLite\Core\Translation::getInstance()->translateByString('Your shopping bag - X item', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('Your shopping bag - X items', $arguments);
    }

    /**
     * Translate label 'X modules will be upgraded'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXModulesWillBeUpgraded(array $arguments)
    {
        return 1 == $arguments['count']
            ? \XLite\Core\Translation::getInstance()->translateByString('X module will be upgraded', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('X modules will be upgraded', $arguments);
    }

    /**
     * Translate label 'X modules will be disabled'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXModulesWillBeDisabled(array $arguments)
    {
        return 1 == $arguments['count']
            ? \XLite\Core\Translation::getInstance()->translateByString('X module will be disabled', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('X modules will be disabled', $arguments);
    }

    // }}}
}
