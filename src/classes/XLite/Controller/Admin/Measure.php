<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Measure
 */
class Measure extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Measure action
     *
     * @return void
     */
    protected function doActionMeasure()
    {
        if (!\XLite\Core\Config::getInstance()->Internal->probe_key) {
            $key = md5(strval(microtime(true) * 1000000) . uniqid(true));
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => 'Internal',
                    'name'     => 'probe_key',
                    'value'    => $key,
                )
            );
            \XLite\Core\Config::getInstance()->Internal->probe_key = $key;
        }

        $this->requestProbe();

        $this->redirect(\XLite\Core\Converter::buildURL());
    }

    /**
     * Request probe script
     *
     * @return void
     */
    protected function requestProbe()
    {
        $url = \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL(
                '',
                '',
                array('key' => \XLite\Core\Config::getInstance()->Internal->probe_key),
                'probe.php'
            )
        );

        set_time_limit(0);

        if (null === \XLite\Core\Operator::checkURLAvailability($url)) {
            \XLite\Core\TopMessage::addError('Measuring productivity in manual mode failed.');
        }
    }
}
