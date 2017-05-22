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
        return [
            '_X_ items'                                   => 'XItemsMinicart',
            'X items in bag'                              => 'XItemsInBag',
            'X items'                                     => 'XItems',
            'X items available'                           => 'XItemsAvailable',
            'Your shopping bag - X items'                 => 'YourShoppingBagXItems',
            'X modules will be upgraded'                  => 'XModulesWillBeUpgraded',
            'X modules will be disabled'                  => 'XModulesWillBeDisabled',
            'X-Cart Business trial will expire in X days' => 'TrialWillExpireInXDays',
            'X days left'                                 => 'XDaysLeft',
        ];
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
        $handler  = null;
        $handlers = $this->getLabelHandlers();

        if (!empty($handlers[$name])) {
            $handler = $handlers[$name];

            if (is_string($handler)) {
                if (method_exists($this, $handler)) {
                    $handler = [$this, $handler];

                } elseif (method_exists($this, 'translateLabel' . ucfirst($handler))) {
                    $handler = [$this, 'translateLabel' . ucfirst($handler)];
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

    /**
     * @param integer $number
     * @param string  $code OPTIONAL
     *
     * @return int
     */
    protected function getPluralizationRule($number, $code = \XLite\Core\Translation::DEFAULT_LANGUAGE)
    {
        return \Symfony\Component\Translation\PluralizationRules::get($number, $code);
    }

    /**
     * @param array   $list
     * @param integer $number
     * @param string  $code
     *
     * @return mixed
     */
    protected function getLabelByRule(array $list, $number, $code = \XLite\Core\Translation::DEFAULT_LANGUAGE)
    {
        $index = $this->getPluralizationRule($number, $code);

        return isset($list[$index]) ? $list[$index] : $list[0];
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
        $label = $this->getLabelByRule(
            [
                '_X_ item',
                '_X_ items',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
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
        $label = $this->getLabelByRule(
            [
                'X item in bag',
                'X items in bag',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
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
        $label = $this->getLabelByRule(
            [
                'X item',
                'X items',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
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
        $label = $this->getLabelByRule(
            [
                'X item available',
                'X items available',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
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
        $label = $this->getLabelByRule(
            [
                'Your shopping bag - X item',
                'Your shopping bag - X items',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
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
        $label = $this->getLabelByRule(
            [
                'X module will be upgraded',
                'X modules will be upgraded',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
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
        $label = $this->getLabelByRule(
            [
                'X module will be disabled',
                'X modules will be disabled',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X-Cart Business trial will expire in X days'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelTrialWillExpireInXDays(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X-Cart Business trial will expire in X day',
                'X-Cart Business trial will expire in X days',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X days left'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXDaysLeft(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X day left',
                'X days left',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    // }}}
}
